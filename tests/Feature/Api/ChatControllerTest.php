<?php

namespace Tests\Feature\Api;

use Api\ApiSender;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChatControllerTest extends ApiSender
{
    /**
     * A basic feature test example.
     */


    public function test_getMessageByRoleAdminFailed(): void
    {
        $authoriztion = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        // 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc' -> role user id 12 in db
        //Bearer 5|qp3WrlQaLvezo6mHfQCLv2LZCW00G5P4SPKO4TmUde48944b -> role *

        //only role admin can access this route
        $response = $this->sendApi('GET', '/api/admin/messages/sender/12', [], $authoriztion);
        echo $response->getContent();
        $response->assertStatus(401);
    }

    public function test_getMessageByRoleAdminSuccess(): void
    {
        $authorization = 'Bearer 5|qp3WrlQaLvezo6mHfQCLv2LZCW00G5P4SPKO4TmUde48944b';
        $response = $this->sendApi('GET', '/api/admin/messages/sender/12', [], $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_getMessageByTokenSuccess(): void
    {
        $authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        $response = $this->sendApi('GET', '/api/messages', ['receiver_id' => '8'], $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_getMessageByTokenFailed(): void
    {
        $authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        $response = $this->sendApi('GET', '/api/messages', ['receiver_id' => '10'], $authorization);
        echo $response->getContent();
        $response->assertStatus(400);
    }

    public function test_sendMessageSuccess(): void
    {
        $authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        $response = $this->sendApi('POST', '/api/messages', [
            'request_id' => '7',
            'message' => 'sender id 12 send from php unit test to receiver id 8',
            'receiver_id' => '8'
        ], $authorization);

        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_sendMessageFailed(): void
    {
        $authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        $response = $this->sendApi('POST', '/api/messages', [
            'request_id' => '7',
            'message' => 'sender id 12 send from php unit test to receiver id 8',
            'receiver_id' => '30000'
        ], $authorization);

        echo $response->getContent();
        $response->assertStatus(422);
    }
}
