<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->json('GET', 'api/charts/ncm', ['filters' => ['teste' => 1]]);

        $response->assertStatus(200);
    }
}
