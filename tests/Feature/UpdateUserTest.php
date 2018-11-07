<?php

namespace Tests\Feature;

use App\Group;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ValidatesRequest;

class UpdateUserTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesRequest;

    /** @test */
    public function updateUser()
    {
        $email = 'first@test.com';
        $user = factory(User::class)->create(['email' => $email]);
        $groups = factory(Group::class, 1)->create();

        $user->groups()->save($groups[0]);

        $requestData = $user->toArray();

        $newEmail = 'second@test.com';
        $requestData['email'] = $newEmail;
        $requestData['groups'] = $groups->pluck('id');
        $response = $this->json('PUT', "users/{$user->id}", $requestData);

        $response->assertStatus(200);

        $expectedContent = [
            'result' => [
                'id' => 1,
                'email' => $newEmail,
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
    public function updateUserSameEmailValue()
    {
        $email = 'first@test.com';
        $user = factory(User::class)->create(['email' => $email]);
        $groups = factory(Group::class, 1)->create();

        $user->groups()->save($groups[0]);

        $requestData = $user->toArray();

        $requestData['groups'] = $groups->pluck('id');
        $response = $this->json('PUT', "users/{$user->id}", $requestData);

        $response->assertStatus(200);

        $expectedContent = [
            'result' => [
                'id' => 1,
                'email' => $email,
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
    public function updateUserDifferentIdValue()
    {
        $email = 'first@test.com';
        $user = factory(User::class)->create(['email' => $email]);
        $groups = factory(Group::class, 1)->create();

        $user->groups()->save($groups[0]);

        $requestData = $user->toArray();

        $requestData['groups'] = $groups->pluck('id');
        $requestData['id'] = 2;
        $response = $this->json('PUT', "users/{$user->id}", $requestData);

        $response->assertStatus(200);

        $expectedContent = [
            'result' => [
                'id' => 1,
                'email' => $email,
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
    public function updateUserChangingGroups()
    {
        $email = 'first@test.com';
        $user = factory(User::class)->create(['email' => $email]);
        $groups = factory(Group::class, 3)->create();

        $beforeRequestGroups = $groups->filter(function ($group) {
            return $group->id !== 3;
        });
        $user->groups()->saveMany($beforeRequestGroups);

        $afterRequestGroups = $groups->filter(function ($group) {
            return $group->id !== 1;
        });
        $requestData = $user->toArray();
        $requestData['groups'] = $afterRequestGroups->pluck('id');

        $response = $this->json('PUT', "users/{$user->id}", $requestData);

        $response->assertStatus(200);
        $this->assertEquals($afterRequestGroups->pluck('id'), $user->groups()->get()->pluck('id'));

        $expectedContent = [
            'result' => [
                'id' => 1,
                'email' => $email,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'state' => $user->state,
                'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                'groups' => $user->groups->toArray()
            ]
        ];
        $response->assertJson($expectedContent);
    }

    /** @test */
    public function updateUnknownUser()
    {
        $response = $this->json('PUT', "users/1" , ['email' => 'first@test.com']);

        $response->assertStatus(404);
    }

    /** @test */
    public function failingUpdateUserRequiredRules()
    {
        $requestData = [];

        $user = factory(User::class)->create();
        $response = $this->json('PUT', "users/{$user->id}", $requestData);

        $this->assertValidationErrors($response, 'required', [
            'email',
            'last_name',
            'first_name'
        ]);
    }

    /** @test */
    public function failingUpdateUserEmailRules()
    {
        $requestData = [
            'email' => 'ivan.petrovexample.com',
        ];

        $user = factory(User::class)->create();
        $response = $this->json('PUT', "users/{$user->id}", $requestData);

        $this->assertValidationErrors($response, 'email', [
            'email'
        ]);
    }

    /** @test */
    public function failingUpdateUserUniqueRules()
    {
        $requestData = [
            'email' => 'second@test.com',
        ];

        $user1 = factory(User::class)->create(['email' => 'first@test.com']);
        $user2 = factory(User::class)->create(['email' => 'second@test.com']);
        $response = $this->json('PUT', "users/{$user1->id}", $requestData);

        $this->assertValidationErrors($response, 'unique', [
            'email'
        ]);
    }

    /** @test */
    public function failingUpdateUserExistsRules()
    {
        $requestData = [
            'email' => 'ivan.petrov@example.com',
            'groups' => [1]
        ];

        $user = factory(User::class)->create();
        $response = $this->json('PUT', "users/{$user->id}", $requestData);

        $this->assertValidationErrors($response, 'exists', [
            'groups'
        ]);
    }

    /** @test */
    public function failingUpdateUserArrayRules()
    {
        $requestData = [
            'email' => 'ivan.petrov@example.com',
            'groups' => 1
        ];

        $user = factory(User::class)->create();
        $response = $this->json('PUT', "users/{$user->id}" , $requestData);

        $this->assertValidationErrors($response, 'array', [
            'groups'
        ]);
    }

    /** @test */
    public function failingUpdateUserInRules()
    {
        $requestData = [
            'email' => 'ivan.petrov@example.com',
            'state' => 'undefined state'
        ];

        $user = factory(User::class)->create();
        $response = $this->json('PUT', "users/{$user->id}" , $requestData);

        $this->assertValidationErrors($response, 'in', [
            'state'
        ]);
    }
}
