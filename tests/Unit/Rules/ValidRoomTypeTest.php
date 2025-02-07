<?php

namespace Tests\Unit\Rules;

use App\Role;
use App\RoomType;
use App\Rules\ValidRoomType;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ValidRoomTypeTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testPasses()
    {
        $roleA = Role::factory()->create();
        $roleB = Role::factory()->create();
        $user  = User::factory()->create();
        $user->roles()->sync([$roleB->id]);
        $roomTypeA = RoomType::factory()->create([
            'restrict' => true
        ]);
        $roomTypeA->roles()->sync([$roleA->id]);
        $roomTypeB = RoomType::factory()->create([
            'restrict' => true
        ]);
        $roomTypeB->roles()->sync([$roleB->id]);
        $roomTypeC = RoomType::factory()->create();
        $roomTypeC->roles()->sync([$roleA->id]);

        $validRoomType = new ValidRoomType(null);

        $this->assertFalse($validRoomType->passes('', null));
        $this->assertFalse($validRoomType->passes('', 1337));
        $this->assertFalse($validRoomType->passes('', $roomTypeA->id));
        $this->assertFalse($validRoomType->passes('', $roomTypeB->id));
        $this->assertFalse($validRoomType->passes('', $roomTypeC->id));

        $validRoomType = new ValidRoomType($user);

        $this->assertFalse($validRoomType->passes('', null));
        $this->assertFalse($validRoomType->passes('', 1337));
        $this->assertFalse($validRoomType->passes('', $roomTypeA->id));
        $this->assertTrue($validRoomType->passes('', $roomTypeB->id));
        $this->assertTrue($validRoomType->passes('', $roomTypeC->id));
    }

    public function testMessage()
    {
        $this->assertEquals(__('validation.custom.invalid_room_type'), (new ValidRoomType(null))->message());
    }
}
