<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_register(): void
    {
        $params = [
            'username' => 'unittest02',
            'email' => 'unittest02@gmail.com',
            'password' => '12345678',
            'password_confirmation' => '12345678',
            'phone' => '113',
            'address' => '192 Lầu 2 Huỳnh Mẫn Đạt',
            'role' => 'customer'];

        $response = $this->post('/api/register', $params);

        dump($response->getContent());
        $response->assertStatus(200);
    }

    public function test_login_Success(): void
    {
        $params = [
            //'username' => 'unittest01',
            //'email' => 'test01@gmail.com',
            'password' => '12345678',
            'phone' => '1111111111',
        ];
        $response = $this->post('/api/check-login', $params);
        $responseData = $response->getContent();
        dump($responseData);
        $response->assertStatus(200);

    }
}
