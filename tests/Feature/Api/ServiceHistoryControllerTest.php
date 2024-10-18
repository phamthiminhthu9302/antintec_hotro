<?php

namespace Api;

use Tests\Feature\Api\ApiSender;
use Tests\TestCase;

class ServiceHistoryControllerTest extends ApiSender
{
    /**
     * A basic feature test example.
     */
    public function test_getServiceHistorySuccess(): void
    {
        $authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        $response = $this->sendApi('GET', '/api/services-management', [], $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_getServiceHistoryRoleAdminSuccess(): void
    {
        $authorization = 'Bearer 5|qp3WrlQaLvezo6mHfQCLv2LZCW00G5P4SPKO4TmUde48944b';
        $response = $this->sendApi('GET', '/api/admin/services-management/12', [], $authorization);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_getServiceHistoryRoleAdminFailed(): void
    {
        $authorization = 'Bearer 7|qrsGEzcHaXYDoeUHNEucyYzLpSHbG3LxmJUvXf6Dd61396fc';
        $response = $this->sendApi('GET', '/api/admin/services-management/12', [], $authorization);
        echo $response->getContent();
        $response->assertStatus(401);
    }



}
