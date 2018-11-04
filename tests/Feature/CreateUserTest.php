<?php

namespace Tests\Feature;

use App\Group;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ValidatesRequest;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesRequest;

    /** @test */
    public function createUserDefaultState()
    {
        $groups = factory(Group::class, 1)->create();
        $state = User::NON_ACTIVE_STATE;

        $this->assertSuccess($groups, $state);
    }


    /** @test */
    public function createUserWithoutDefaultState()
    {
        $groups = factory(Group::class, 1)->create();
        $state = User::ACTIVE_STATE;

        $this->assertSuccess($groups, $state, true);
    }

    /** @test */
    public function createUserWithSomeGroups()
    {
        $groups = factory(Group::class, 2)->create();
        $state = User::NON_ACTIVE_STATE;

        $this->assertSuccess($groups, $state);
    }

    /** @test */
    public function failingCreateUserRequiredRules()
    {
        $requestData = [];

        $response = $this->json('post', 'users', $requestData);

        $this->assertValidationErrors($response, 'required', [
            'email',
            'last_name',
            'first_name',
            'groups'
        ]);
    }

    /** @test */
    public function failingCreateUserEmailRules()
    {
        $requestData = [
            'email' => 'ivan.petrovexample.com',
        ];

        $response = $this->json('post', 'users', $requestData);

        $this->assertValidationErrors($response, 'email', [
            'email'
        ]);
    }

    /** @test */
    public function failingCreateUserUniqueRules()
    {
        $user = factory(User::class)->create(['email' => 'ivan.petrov@example.com']);
        $requestData = [
            'email' => 'ivan.petrov@example.com',
        ];

        $response = $this->json('post', 'users', $requestData);

        $this->assertValidationErrors($response, 'unique', [
            'email'
        ]);
    }

    /** @test */
    public function failingCreateUserExistsRules()
    {
        $requestData = [
            'email' => 'ivan.petrov@example.com',
            'groups' => [1]
        ];

        $response = $this->json('post', 'users', $requestData);

        $this->assertValidationErrors($response, 'exists', [
            'groups'
        ]);
    }

    /** @test */
    public function failingCreateUserArrayRules()
    {
        $requestData = [
            'email' => 'ivan.petrov@example.com',
            'groups' => 1
        ];

        $response = $this->json('post', 'users', $requestData);

        $this->assertValidationErrors($response, 'array', [
            'groups'
        ]);
    }

    /** @test */
    public function failingCreateUserInRules()
    {
        $requestData = [
            'email' => 'ivan.petrov@example.com',
            'state' => 'undefined state'
        ];

        $response = $this->json('post', 'users', $requestData);

        $this->assertValidationErrors($response, 'in', [
            'state'
        ]);
    }

    private function assertSuccess($groups, $state, $requestHasStateValue = false)
    {
        $expectedContent = $this->getExpectedContent($groups, $state);
        if ($requestHasStateValue) {
            $requestData = $this->getRequestData($groups, $state);
        } else {
            $requestData = $this->getRequestData($groups);
        }

        $response = $this->json('post', 'users', $requestData);

        $response->assertStatus(201);
        $response->assertJson($expectedContent);
    }

    private function getUserData($state = null)
    {
        $data = [
            'email' => 'ivan.petrov@example.com',
            'last_name' => 'Petrov',
            'first_name' => 'Ivanov'
        ];
        if (!is_null($state)) {
            $data['state'] = $state;
        }
        return $data;
    }

    private function getExpectedContent($groups, $state = null)
    {
        $time = '2018-01-01 13:14:15';
        Carbon::setTestNow(Carbon::createFromTimeString($time));

        return [
            'result' => array_merge($this->getUserData($state), [
                'id' => 1,
                'state' => $state,
                'created_at' => $time,
                'groups' => $groups->toArray()
            ])
        ];
    }

    private function getRequestData($groups, $state = null)
    {
        return array_merge($this->getUserData($state), ['groups' => $groups->pluck('id')]);
    }
}
