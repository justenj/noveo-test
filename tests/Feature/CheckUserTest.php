<?php

namespace Tests\Feature;

use App\Group;
use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ValidatesRequest;

class CheckUserTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesRequest;

    /** @test */
    public function userExists()
    {
        $email = 'first@test.com';
        $user = factory(User::class)->create(['email' => $email]);
        $response = $this->json('POST', 'users/check', ['email' => $email]);

        $expectedJson = [
            'result' => [
                'exists' => true,
                'state' => 'non active',
                'user_id' => $user->id
            ]
        ];
        $response->assertJson($expectedJson);
    }

    /** @test */
    public function userDoesNotExists()
    {
        $email = 'first@test.com';
        $response = $this->json('POST', 'users/check', ['email' => $email]);

        $expectedJson = [
            'result' => [
                'exists' => false
            ]
        ];
        $response->assertJson($expectedJson);
    }

    /** @test */
    public function failingCheckUserRequiredRules()
    {
        $response = $this->json('POST', 'users/check', []);
        $this->assertValidationErrors($response, 'required', [
            'email'
        ]);
    }

    /** @test */
    public function failingCheckUserEmailRules()
    {
        $requestData = [
            'email' => 'ivan.petrovexample.com',
        ];

        $response = $this->json('POST', 'users/check', $requestData);

        $this->assertValidationErrors($response, 'email', [
            'email'
        ]);
    }
}
