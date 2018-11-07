<?php

namespace Tests;

trait PaginationTesting
{
    /**
     * Fetching test
     *
     * @test
     */
    public function fetchEntities()
    {
        factory($this->getModelClass(), 31)->create();

        $this->assertPagination();
        $this->assertPagination(2);
        $this->assertPagination(3);
        $this->assertPagination(4);
    }

    private function assertPagination($page = 1)
    {
        if ($page === 1) {
            $expectedContent = [
                'result' => $this->getModelClass()::paginate()->setPath(url($this->getUrlPath()))->toArray()
            ];
            $response = $this->requestWithoutPage();
        } else {
            $expectedContent = [
                'result' => $this->getModelClass()::paginate(null, ['*'], 'page', $page)->setPath(url($this->getUrlPath()))->toArray()
            ];
            $response = $this->requestWithPage($page);
        }

        $response->assertStatus(200);
        $response->assertJson($expectedContent);
    }

    private function requestWithPage($page = 2)
    {
        return  $this->json('GET', "{$this->getUrlPath()}?page={$page}");
    }

    private function requestWithoutPage()
    {
        return $this->json('GET', $this->getUrlPath());
    }

    private function getModelClass()
    {
        return $this->modelClass;
    }

    private function getUrlPath()
    {
        return $this->urlPath;
    }
}
