<?php

namespace Tests\Unit;

use App\Enums\ServerStatus;
use App\Meeting;
use App\Server;
use App\User;
use BigBlueButton\BigBlueButton;
use BigBlueButton\Responses\GetMeetingsResponse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;
use TiMacDonald\Log\LogFake;

class ServerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test getMeetings response for different server status and wrong api details
     */
    public function testGetMeetingsWithStatusAndOffline()
    {
        $server = Server::factory()->create();

        // Server marked as inactive
        $server->status = ServerStatus::DISABLED;
        $server->save();
        $this->assertNull($server->getMeetings());
        $server->status = ServerStatus::ONLINE;
        $server->save();

        // Server marked as offline
        $server->status = ServerStatus::OFFLINE;
        $server->save();
        $this->assertNull($server->getMeetings());
        $server->status = ServerStatus::ONLINE;
        $server->save();

        // Test with invalid domain name
        $server->base_url = 'https://fake.notld/bigbluebutton/';
        $server->save();
        $this->assertNull($server->getMeetings());
    }

    /**
     * Test if server response is correctly passed through
     */
    public function testGetMeetingsWithResponse()
    {
        $bbbResponseMock = Mockery::mock(GetMeetingsResponse::class, function ($mock) {
            $mock->shouldReceive('failed')
                ->once()
                ->andReturn(false);
            $mock->shouldReceive('getMeetings')
                ->once()
                ->andReturn('test-response');
        });

        $bbbMock = Mockery::mock(BigBlueButton::class, function ($mock) use ($bbbResponseMock) {
            $mock->shouldReceive('getMeetings')
                ->once()
                ->andReturn($bbbResponseMock);
        });

        $serverMock = Mockery::mock(Server::class, function ($mock) use ($bbbMock) {
            $mock->shouldReceive('bbb')
                ->once()
                ->andReturn($bbbMock);
        })->makePartial();

        $serverMock->offline = 0;
        $serverMock->status  = 1;

        self::assertEquals('test-response', $serverMock->getMeetings());
    }

    /**
     * Test if a failed server response results in empty response
     */
    public function testGetMeetingsWithFailedResponse()
    {
        $bbbReponseMock = Mockery::mock(GetMeetingsResponse::class, function ($mock) {
            $mock->shouldReceive('failed')
                ->once()
                ->andReturn(true);
        });

        $bbbMock = Mockery::mock(BigBlueButton::class, function ($mock) use ($bbbReponseMock) {
            $mock->shouldReceive('getMeetings')
                ->once()
                ->andReturn($bbbReponseMock);
        });

        $serverMock = Mockery::mock(Server::class, function ($mock) use ($bbbMock) {
            $mock->shouldReceive('bbb')
                ->once()
                ->andReturn($bbbMock);
        })->makePartial();

        $serverMock->status = ServerStatus::ONLINE;

        self::assertNull($serverMock->getMeetings());
    }

    /**
     * Test if closure resets current usage for offline and not for online
     */
    public function testUsageClearedOnOffline()
    {
        // Create new fake server
        $server                          = Server::factory()->create();

        // Set the live usage data of server
        $server->participant_count       = 1;
        $server->listener_count          = 2;
        $server->voice_participant_count = 3;
        $server->video_count             = 4;
        $server->meeting_count           = 5;
        $server->status                  = ServerStatus::ONLINE;
        $server->save();

        $server->refresh();
        $this->assertEquals(ServerStatus::ONLINE, $server->status);
        $this->assertEquals(1, $server->participant_count);
        $this->assertEquals(2, $server->listener_count);
        $this->assertEquals(3, $server->voice_participant_count);
        $this->assertEquals(4, $server->video_count);
        $this->assertEquals(5, $server->meeting_count);

        $server->status                  = ServerStatus::OFFLINE;
        $server->save();

        // Reload data and check if everything is reset, as the server is offline
        $server->refresh();
        $this->assertEquals(ServerStatus::OFFLINE, $server->status);
        $this->assertNull($server->participant_count);
        $this->assertNull($server->listener_count);
        $this->assertNull($server->voice_participant_count);
        $this->assertNull($server->video_count);
        $this->assertNull($server->meeting_count);
    }

    /**
     * Test if closure resets current usage for disabled
     */
    public function testUsageClearedOnDisabled()
    {
        // Create new fake server
        $server                          = Server::factory()->create();

        // Set the live usage data of server
        $server->participant_count       = 1;
        $server->listener_count          = 2;
        $server->voice_participant_count = 3;
        $server->video_count             = 4;
        $server->meeting_count           = 5;
        $server->status                  = ServerStatus::ONLINE;
        $server->save();

        $server->refresh();
        $server->status                  = ServerStatus::DISABLED;
        $server->save();

        // Reload data and check if everything is reset, as the server is disabled
        $server->refresh();
        $this->assertEquals(ServerStatus::DISABLED, $server->status);
        $this->assertNull($server->participant_count);
        $this->assertNull($server->listener_count);
        $this->assertNull($server->voice_participant_count);
        $this->assertNull($server->video_count);
        $this->assertNull($server->meeting_count);
    }

    /**
     * Check if attendance is getting logged
     */
    public function testLogAttendance()
    {
        Log::swap(new LogFake);
        setting(['attendance.enabled'=>true]);

        $bbbMock = Mockery::mock(BigBlueButton::class, function ($mock) {
            $mock->shouldReceive('getMeetings')->once()->andReturn(new GetMeetingsResponse(simplexml_load_file(__DIR__.'/../Fixtures/Attendance/GetMeetings-Start.xml')));
            $mock->shouldReceive('getMeetings')->once()->andReturn(new GetMeetingsResponse(simplexml_load_file(__DIR__.'/../Fixtures/Attendance/GetMeetings-1.xml')));
            $mock->shouldReceive('getMeetings')->once()->andReturn(new GetMeetingsResponse(simplexml_load_file(__DIR__.'/../Fixtures/Attendance/GetMeetings-2.xml')));
            $mock->shouldReceive('getMeetings')->once()->andReturn(new GetMeetingsResponse(simplexml_load_file(__DIR__.'/../Fixtures/Attendance/GetMeetings-End.xml')));
        });

        $server = Server::factory()->create();
        $server->setBBB($bbbMock);

        $meeting = Meeting::factory()->create(['id'=> '409e94ee-e317-4040-8cb2-8000a289b49d','start'=>'2021-06-25 09:24:25','end'=>null,'record_attendance'=>true,'attendeePW'=> 'asdfgh32343','moderatorPW'=> 'h6gfdew423']);
        $meeting->server()->associate($server);
        $meeting->save();

        $userA = User::factory()->create(['id'=>99,'firstname'=> 'Mable', 'lastname' => 'Torres', 'email' => 'm.torres@example.net']);
        $userB = User::factory()->create(['id'=>100,'firstname'=> 'Gregory', 'lastname' => 'Dumas', 'email' => 'g.dumas@example.net']);

        $server->updateUsage();
        $meeting->refresh();

        // Check attendance data after first run

        // Count total attendance datasets
        $this->assertCount(4, $meeting->attendees);

        // Check if users are added correct
        $attendeeUserA = $meeting->attendees()->where('user_id', 99)->first();
        $this->assertTrue($attendeeUserA->user->is($userA));
        $this->assertNull($attendeeUserA->name);
        $this->assertNull($attendeeUserA->session_id);
        $this->assertNull($attendeeUserA->leave);
        $this->assertNotNull($attendeeUserA->join);

        // Check if guests are added correct
        $attendeeGuestA = $meeting->attendees()->where('session_id', 'PogeR6XH8I2SAeCqc8Cp5y5bD9Qq70dRxe4DzBcb')->first();
        $this->assertEquals('Marie Walker', $attendeeGuestA->name);
        $this->assertNull($attendeeGuestA->user);
        $this->assertNull($attendeeGuestA->leave);
        $this->assertNotNull($attendeeGuestA->join);

        // Check if errors are logged
        Log::assertLogged('notice', function ($message, $context) {
            return $message == 'Unknown prefix for attendee found.' && $context == ['prefix' => '2','meeting'=> '409e94ee-e317-4040-8cb2-8000a289b49d'];
        });
        Log::assertLogged('notice', function ($message, $context) {
            return $message == 'Attendee user not found.' && $context == ['user' => '101','meeting'=> '409e94ee-e317-4040-8cb2-8000a289b49d'];
        });

        $server->updateUsage();
        $meeting->refresh();

        // Count total attendance datasets
        $this->assertCount(4, $meeting->attendees);

        // Check if user and guest session end got detected
        $attendeeUserA = $meeting->attendees()->where('user_id', 99)->first();
        $this->assertNotNull($attendeeUserA->leave);
        $attendeeGuestA = $meeting->attendees()->where('session_id', 'PogeR6XH8I2SAeCqc8Cp5y5bD9Qq70dRxe4DzBcb')->first();
        $this->assertNotNull($attendeeGuestA->leave);

        // Check if user and guest sessions of the other users are not ended
        $attendeeUserA = $meeting->attendees()->where('user_id', 100)->first();
        $this->assertNull($attendeeUserA->leave);
        $attendeeGuestA = $meeting->attendees()->where('session_id', 'LQC1Pb5TSBn2EM5njylocogXPgIQIknKQcvcWMRG')->first();
        $this->assertNull($attendeeGuestA->leave);

        $server->updateUsage();
        $meeting->refresh();

        // Count total attendance datasets, should increase as two new sessions exists
        $this->assertCount(6, $meeting->attendees);

        // Check if the new session for the user is added
        $attendeeUserA = $meeting->attendees()->where('user_id', 99)->get();
        $this->assertNotNull($attendeeUserA[0]->leave);
        $this->assertNotNull($attendeeUserA[0]->join);
        $this->assertNull($attendeeUserA[1]->leave);
        $this->assertNotNull($attendeeUserA[1]->join);

        // Check if the new session for the guest is added
        $attendeeGuestA = $meeting->attendees()->where('session_id', 'PogeR6XH8I2SAeCqc8Cp5y5bD9Qq70dRxe4DzBcb')->get();
        $this->assertNotNull($attendeeGuestA[0]->leave);
        $this->assertNotNull($attendeeGuestA[0]->join);
        $this->assertNull($attendeeGuestA[1]->leave);
        $this->assertNotNull($attendeeGuestA[1]->join);

        $server->updateUsage();
        $meeting->refresh();

        // Check if end time is set after meeting ended
        foreach ($meeting->attendees as $attendee) {
            $this->assertNotNull($attendee->leave);
        }
    }

    /**
     * Check if attendance is not getting logged if disabled
     */
    public function testLogAttendanceDisabled()
    {
        setting(['attendance.enabled'=>false]);

        $bbbMock = Mockery::mock(BigBlueButton::class, function ($mock) {
            $mock->shouldReceive('getMeetings')->andReturn(new GetMeetingsResponse(simplexml_load_file(__DIR__.'/../Fixtures/Attendance/GetMeetings-Start.xml')));
        });

        $server = Server::factory()->create();
        $server->setBBB($bbbMock);

        $meeting = Meeting::factory()->create(['id'=> '409e94ee-e317-4040-8cb2-8000a289b49d','start'=>'2021-06-25 09:24:25','end'=>null,'record_attendance'=>true,'attendeePW'=> 'asdfgh32343','moderatorPW'=> 'h6gfdew423']);
        $meeting->server()->associate($server);
        $meeting->save();

        // Check if attendance is not logged if enabled for this meeting, but disabled globally
        $server->updateUsage();
        $meeting->refresh();
        $this->assertCount(0, $meeting->attendees);

        // Check if attendance is not logged if disabled for this meeting, but enabled globally
        setting(['attendance.enabled'=>true]);
        $meeting->record_attendance = false;
        $meeting->save();
        $server->updateUsage();
        $meeting->refresh();
        $this->assertCount(0, $meeting->attendees);
    }
}
