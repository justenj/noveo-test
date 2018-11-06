<?php

namespace Tests\Feature;

use App\Group;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FetchUserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function fetchUser()
    {
        $user = factory(User::class)->create();
        $groups = factory(Group::class, 1)->create();
        $user->groups()->save($groups[0]);

        $response = $this->json('GET', "users/{$user->id}");

        $response->assertStatus(200);

        $expectedContent = [
            'result' => [
                'id' => 1,
                'email' => $user->email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'state' => $user->state,
                'created_at' => $user->created_at,
                'groups' => $groups->toArray()
            ]
        ];
        $response->assertJson($expectedContent);
    }

    /** @test */
    public function fetchUnknownUser()
    {
        $response = $this->json('GET', "users/1");
        $response->assertStatus(404);
    }
}
