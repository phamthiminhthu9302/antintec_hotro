<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class NotificationControllerTest extends ApiSender
{
    /**
     * A basic feature test example.
     */
    public function test_getAllNotificationsByCustomerId(): void
    {   // user id 7 14|cX6cstUFjC5OySOAFsQtqTmd4SQZdZYjyVtekQ9g395ddefa
        // user id 12 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc
        //user id 9 15|9e8MvYIZMhWh8RhTB5FEBzJZ8cBJ6ia4MFQA4uU868e8c7de
        $authorization = 'Bearer 14|cX6cstUFjC5OySOAFsQtqTmd4SQZdZYjyVtekQ9g395ddefa';
        $response = $this->sendApi('GET', '/api/notifications', [], $authorization);
        echo $response->getStatusCode();
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_getAllNotificationsByCustomerIdFailed(): void
    {
        $authorization = 'Bearer 15|9e8MvYIZMhWh8RhTB5FEBzJZ8cBJ6ia4MFQA4uU868e8c7de';
        $response = $this->sendApi('GET', '/api/notifications', [], $authorization);
        echo $response->getStatusCode();
        echo $response->getContent();
        $response->assertStatus(404);
    }

    public function test_readNotification()
    {
        $authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        $response = $this->sendApi('put', '/api/notifications/read', [
            'notification_id' => '22',
        ], $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_readNotificationFailed()
    {
        //failed because user id not match in notification table
        $authorization = 'Bearer 5|qp3WrlQaLvezo6mHfQCLv2LZCW00G5P4SPKO4TmUde48944b';
        $response = $this->sendApi('put', '/api/notifications/read', [
            'notification_id' => '500'
        ], $authorization);
        echo $response->getContent();
        $response->assertStatus(404);
    }
}
