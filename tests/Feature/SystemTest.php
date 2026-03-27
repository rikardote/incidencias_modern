<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SystemTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_landing_page_accessible(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Bienvenido');
    }
}
