<?php

namespace Tests\Feature;

use App\Group;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ValidatesRequest;

class CreateGroupTest extends TestCase
{
    use RefreshDatabase;
    use ValidatesRequest;

    /** @test */
    public function createGroup()
    {
        $this->withoutExceptionHandling();

        $name = 'Moderators';
        $expectedContent = [
            'result' => [
                'id' => 1,
                'name' => $name
            ]
        ];

        $response = $this->json('POST', 'groups', ['name' => $name]);

        $response->assertStatus(201);
        $response->assertJson($expectedContent);
    }

    /** @test */
    public function failingCreateGroupRequiredRules()
    {
        $requestData = [];

        $response = $this->json('post', 'groups', $requestData);

        $this->assertValidationErrors($response, 'required', [
            'name'
        ]);
    }

    /** @test */
    public function failingCreateGroupUniqueRules()
    {
        $requestData = [
            'name' => 'Unique group',
        ];
        $group = factory(Group::class)->create($requestData);

        $response = $this->json('post', 'groups', $requestData);

        $this->assertValidationErrors($response, 'unique', [
            'name'
        ]);
    }
}
