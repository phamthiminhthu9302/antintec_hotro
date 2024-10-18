<?php

namespace Api;

use DateTime;
use DateTimeZone;
use Tests\Feature\Api\ApiSender;

class RequestControllerTest extends ApiSender
{
    public function test_createRequest()
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
            'status' => 'pending',
            'location' => "192 lầu 2 huỳnh mẫn đạt",
            'requested_at' => date('Y-m-d h:i:s'),
        ], $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_updateRequest()
    {
        $authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        $response = $this->sendApi('put', '/api/requests/status', [
            'request_id' => '8',
            'status' => 'pending'
        ], $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_readNotification()
    {
        $authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        $response = $this->sendApi('put', '/api/requests/read', [
            'notification_id' => '5'
        ], $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_readNotificationFailed() {
        //failed because user id not match in notification table
        $authorization = 'Bearer 5|qp3WrlQaLvezo6mHfQCLv2LZCW00G5P4SPKO4TmUde48944b';
        $response = $this->sendApi('put', '/api/requests/read', [
            'notification_id' => '5'
        ], $authorization);
        echo $response->getContent();
        $response->assertStatus(400);
    }
}
