<?php

namespace Tests\Feature;

use App\Group;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FetchGroupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function fetchGroups()
    {
        factory(Group::class, 31)->create();

        $this->assertPagination();
        $this->assertPagination(2);
        $this->assertPagination(3);
        $this->assertPagination(4);
    }

    private function assertPagination($page = 1)
    {
        if ($page === 1) {
            $expectedContent = [
                'result' => Group::paginate()->setPath(url('groups'))->toArray()
            ];
            $response = $this->requestWithoutPage();
        } else {
            $expectedContent = [
                'result' => Group::paginate(null, ['*'], 'page', $page)->setPath(url('groups'))->toArray()
            ];
            $response = $this->requestWithPage($page);
        }

        $response->assertStatus(200);
        $response->assertJson($expectedContent);
    }

    private function requestWithPage($page = 2)
    {
        return  $this->json('GET', 'groups?page=' . $page);
    }

    private function requestWithoutPage()
    {
        return $this->json('GET', 'groups');
    }
}
