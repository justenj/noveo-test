<?php

namespace Tests\Feature;

use App\Group;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddUserIntoGroupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function addUserIntoGroup()
    {
        $group = factory(Group::class)->create();
        $user = factory(User::class)->create();

        $response = $this->json('PUT', "groups/{$group->id}/users/{$user->id}");
        $response->assertStatus(204);
    }

    /** @test */
    public function addUserIntoGroupWhereHeAlreadyExists()
    {
        $group = factory(Group::class)->create();
        $user = factory(User::class)->create();

        $group->users()->save($user);

        $response = $this->json('PUT', "groups/{$group->id}/users/{$user->id}");
        $response->assertStatus(204);
    }

    /** @test */
    public function addUserIntoUndefinedGroup()
    {
        $user = factory(User::class)->create();

        $response = $this->json('PUT', "groups/1/users/{$user->id}");
        $response->assertStatus(404);
    }

    /** @test */
    public function addUndefinedUserIntoGroup()
    {
        $group = factory(Group::class)->create();

        $response = $this->json('PUT', "groups/{$group->id}/users/1");
        $response->assertStatus(404);
    }
}
