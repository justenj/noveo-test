<?php

namespace Tests\Feature;

use App\Group;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RemoveUserFromGroupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function removeUserFromGroup()
    {
        $group = factory(Group::class)->create();
        $user = factory(User::class)->create();

        $group->users()->save($user);

        $response = $this->json('DELETE', "groups/{$group->id}/users/{$user->id}");
        $response->assertStatus(204);
    }

    /** @test */
    public function removeUserFromGroupWithoutUsers()
    {
        $group = factory(Group::class)->create();
        $user = factory(User::class)->create();

        $response = $this->json('DELETE', "groups/{$group->id}/users/{$user->id}");
        $response->assertStatus(204);
    }

    /** @test */
    public function removeUserFromUndefinedGroup()
    {
        $user = factory(User::class)->create();

        $response = $this->json('DELETE', "groups/1/users/{$user->id}");
        $response->assertStatus(404);
    }

    /** @test */
    public function removeUndefinedUserFromGroup()
    {
        $group = factory(Group::class)->create();

        $response = $this->json('DELETE', "groups/{$group->id}/users/1");
        $response->assertStatus(404);
    }
}
