<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ServiceControllerTest extends ApiSender
{
    /**
     * A basic feature test example.
     */
    public function test_getTechnicianServices(): void
    {
        $authorization = 'Bearer 10|uHJ9hvoU4mtKiWmdMdXxOq7IdXdlbjRgFzmDYUicd5251007';
        $response = $this->sendApi('GET', '/api/technician/services', [], $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }
}
