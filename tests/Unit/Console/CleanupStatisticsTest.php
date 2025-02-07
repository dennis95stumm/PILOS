<?php

namespace Tests\Unit\Console;

use App\Meeting;
use App\MeetingStat;
use App\Server;
use App\ServerStat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CleanupStatisticsTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testClear()
    {
        setting()->set('statistics.servers.retention_period', 10);
        setting()->set('statistics.meetings.retention_period', 20);

        // Create fake data
        $server  = Server::factory()->create();
        $meeting = Meeting::factory()->create();

        $serverStat1                          = new ServerStat();
        $serverStat1->created_at              = now()->subDays(11)->toDateString();
        $serverStat1->participant_count       = 5;
        $serverStat1->listener_count          = 5;
        $serverStat1->voice_participant_count = 5;
        $serverStat1->video_count             = 5;
        $serverStat1->meeting_count           = 5;
        $server->stats()->save($serverStat1);

        $serverStat2                          = new ServerStat();
        $serverStat2->participant_count       = 1;
        $serverStat2->listener_count          = 1;
        $serverStat2->voice_participant_count = 1;
        $serverStat2->video_count             = 1;
        $serverStat2->meeting_count           = 1;
        $server->stats()->save($serverStat2);

        $meetingStat1                          = new MeetingStat();
        $meetingStat1->created_at              = now()->subDays(21)->toDateString();
        $meetingStat1->participant_count       = 5;
        $meetingStat1->listener_count          = 5;
        $meetingStat1->voice_participant_count = 5;
        $meetingStat1->video_count             = 5;
        $meeting->stats()->save($meetingStat1);

        $meetingStat2                          = new MeetingStat();
        $meetingStat2->participant_count       = 1;
        $meetingStat2->listener_count          = 1;
        $meetingStat2->voice_participant_count = 1;
        $meetingStat2->video_count             = 1;
        $meeting->stats()->save($meetingStat2);

        // Check if the datasets exit
        $this->assertCount(2, $server->stats);
        $this->assertCount(2, $meeting->stats);

        // Run cleanup command
        $this->artisan('cleanup:statistics');

        // Reload database
        $server->refresh();
        $meeting->refresh();

        // Check if old datasets have been removed and only the newer datasets are still there
        $this->assertCount(1, $server->stats);
        $this->assertCount(1, $meeting->stats);

        $this->assertTrue($server->stats()->first()->is($serverStat2));
        $this->assertTrue($meeting->stats()->first()->is($meetingStat2));
    }
}
