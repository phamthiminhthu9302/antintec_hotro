<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Api\ApiSender;
use Tests\TestCase;

class AuthControllerTest extends ApiSender
{
    /**
     * A basic feature test example.
     */
    public function test_register(): void
    {
        $params = [
            'username' => 'unittest03',
            'email' => 'unittest03@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'phone' => '115',
            'address' => '192 Lầu 2 Huỳnh Mẫn Đạt',
            'role' => 'customer'];

        $response = $this->sendApi('POST', '/api/register', $params, null);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_login_Success(): void
    {

        //samples data:
        //'username' => 'Elwin Bartoletti', username' => 'Dr. Lula Goodwin',
        //password' => '1234567890',
        $params = [
            'username' => 'Dr. Lula Goodwin',
            //'email' => 'test01@gmail.com',
            'password' => '1234567890',
            //'phone' => '1111111111',
        ];
        $response = $this->post('/api/check-login', $params);
        $responseData = $response->getContent();
        dump($responseData);
        $response->assertStatus(200);

    }
}
