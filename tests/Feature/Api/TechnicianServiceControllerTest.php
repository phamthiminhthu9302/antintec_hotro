<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TechnicianServiceControllerTest extends ApiSender
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $authorization = 'Bearer 11|uCIxwOEhIfpjxTrBhBQcd2V8anlLY1CU8OQWfLJY4924774e';
        $response = $this->sendApi('get', 'api/technician/services', [], $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_createTechnicianService()
    {
        //$authorization = 'Bearer 11|uCIxwOEhIfpjxTrBhBQcd2V8anlLY1CU8OQWfLJY4924774e';
        $authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        $params = [
            'service_id' => '5',
            'status' => 'active',
            'available_from' => date('Y-m-d H:i:s'),
            'available_to' => date('Y-m-d H:i:s'),
        ];
        $response = $this->sendApi('post', 'api/technician/services', $params, $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_updateTechnicianService()
    {
        //'Bearer 11|uCIxwOEhIfpjxTrBhBQcd2V8anlLY1CU8OQWfLJY4924774e'
        $authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        $params = [
            'service_id' => '5',
            'status' => 'inactive',
            'available_from' => date('Y-m-d H:i:s'),
            //'available_to' => date('Y-m-d H:i:s'),
        ];

        $response = $this->sendApi('put', 'api/technician/services', $params, $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_updateTechnicianServiceFailed()
    {
        $authorization = 'Bearer 11|uCIxwOEhIfpjxTrBhBQcd2V8anlLY1CU8OQWfLJY4924774e';
        $params = [
            'service_id' => 500,
            'status' => 'active',
            //'available_from' => date('Y-m-d H:i:s'),
            //'available_to' => date('Y-m-d H:i:s'),
        ];

        $response = $this->sendApi('put', 'api/technician/services', $params, $authorization);
        echo $response->getContent();
        $response->assertStatus(422);
    }

    public function test_getAllAvailableTechnicianByServiceId()
    {
        $authorization = 'Bearer 11|uCIxwOEhIfpjxTrBhBQcd2V8anlLY1CU8OQWfLJY4924774e';
        $params = [
            'service_id' => 3,
        ];
        $response = $this->sendApi('get', 'api/technician/services/available', $params, $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }
}
