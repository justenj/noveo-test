<?php

namespace Tests;

trait ValidatesRequest
{
    /**
     * Assert validation errors
     *
     * @return void
     */
    private function assertValidationErrors($response, $rule, $fields = [])
    {
        $response->assertStatus(422);

        foreach ($fields as $field) {
            $this->assertArraySubset([$field => [__('validation.' . $rule, ['attribute' => str_replace('_', ' ', $field)])]], (array)$response->getData()->errors);
        }
    }
}
