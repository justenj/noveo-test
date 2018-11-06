<?php

namespace Tests\Feature;

use App\Group;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ValidatesRequest;

class UpdateGroupTest extends TestCase
{
    use RefreshDatabase;

    use RefreshDatabase;
    use ValidatesRequest;

    /** @test */
    public function updateGroup()
    {
        $name = 'Moderators';
        $group = factory(Group::class)->create(['name' => $name]);

        $newName = 'Admins';
        $response = $this->json('PUT', "groups/{$group->id}" , ['name' => $newName]);

        $response->assertStatus(200);

        $expectedContent = [
            'result' => [
                'id' => 1,
                'name' => $newName
            ]
        ];

        $response->assertJson($expectedContent);
    }

    /** @test */
    public function updateGroupSameNameValue()
    {
        $name = 'Moderators';
        $group = factory(Group::class)->create(['name' => $name]);

        $newName = 'Moderators';
        $response = $this->json('PUT', "groups/{$group->id}" , ['name' => $newName]);

        $response->assertStatus(200);

        $expectedContent = [
            'result' => [
                'id' => 1,
                'name' => $newName
            ]
        ];

        $response->assertJson($expectedContent);
    }

    /** @test */
    public function updateUnknownGroup()
    {
        $response = $this->json('PUT', "groups/1" , ['name' => 'Moderators']);

        $response->assertStatus(404);
    }

    /** @test */
    public function failingUpdateGroupRequiredRules()
    {
        $requestData = [];
        $group = factory(Group::class)->create();

        $response = $this->json('PUT', "groups/{$group->id}", $requestData);

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
        factory(Group::class)->create($requestData);
        $group = factory(Group::class)->create();

        $response = $this->json('PUT', "groups/{$group->id}" , $requestData);

        $this->assertValidationErrors($response, 'unique', [
            'name'
        ]);
    }
}
