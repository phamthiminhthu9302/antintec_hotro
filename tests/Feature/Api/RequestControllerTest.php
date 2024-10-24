<?php

namespace Api;
use Tests\Feature\Api\ApiSender;

class RequestControllerTest extends ApiSender
{
    public function test_createRequestFailed()
    {
        $authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        date_default_timezone_set("Asia/Ho_Chi_minh");
        $response = $this->sendApi('post', '/api/requests', [
            'technician_id' => '8',
            'service_id' => '1',
            'latitude' => '100',
            'longitude' => '200',
            'photo' => null,
            'description' => "xe hư mất rồi",
            'status' => 'pendinggg',
            'location' => "192 lầu 2 huỳnh mẫn đạt",
            'requested_at' => date('Y-m-d h:i:s'),
        ], $authorization);
        echo $response->getContent();
        //failed because status value not in [pending, in_progress, completed,cancelled]
        $response->assertStatus(422);
    }

    public function test_createRequestSuccess()
    {
        $authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        date_default_timezone_set("Asia/Ho_Chi_minh");
        $response = $this->sendApi('post', '/api/requests', [
            'technician_id' => '8',
            'service_id' => '1',
            'latitude' => '100',
            'longitude' => '200',
            'photo' => null,
            'description' => "xe hư mất rồi",
            'status' => 'in_progress',
            'location' => "192 lầu 2 huỳnh mẫn đạt",
            'requested_at' => date('Y-m-d h:i:s'),
        ], $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_updateRequestSuccess()
    {   //user id = 8:  Bearer 12|2lSS0VmtPjAotLesQF14Klxu9sr9yQ8CZtvUgKHI2ce7e940
        //$authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc'; user id = 12
        $authorization =  'Bearer 12|2lSS0VmtPjAotLesQF14Klxu9sr9yQ8CZtvUgKHI2ce7e940';
        $response = $this->sendApi('put', '/api/requests/status', [
            'request_id' => '15',
            'status' => 'completed'
        ], $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_updateRequestFailed()
    {
        $authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        $response = $this->sendApi('put', '/api/requests/status', [
            'request_id' => '15',
            'status' => 'completed'
        ], $authorization);
        echo $response->getContent();
        $response->assertStatus(400);
    }


}
