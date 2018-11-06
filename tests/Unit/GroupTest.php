<?php

namespace Tests\Unit;

use App\Group;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GroupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function groupHasUsers()
    {
        $excludedUsers = factory(User::class, 3)->create();
        $includedUsers = factory(User::class, 2)->create();
        $group = factory(Group::class)->create();

        $group->users()->saveMany($includedUsers);

        $groupUsers = $group->users()->get();
        $this->assertEquals($groupUsers->pluck('id'), $includedUsers->pluck('id'));
    }
}
