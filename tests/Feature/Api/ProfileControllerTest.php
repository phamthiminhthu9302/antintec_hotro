<?php

namespace Tests\Feature\Api;
require_once 'vendor/autoload.php';

use App\Models\User;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_retrieveProfile(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->get('/api/profile');
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_updateProfile(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->put('/api/profile', [
            'phone' => '0000000000',
            'address' => '192 Lầu 5 Huỳnh Mẫn Đạt Q5',
            'role' => 'technician'
        ]);
        echo $response->getContent();
        $response->assertStatus(200);
    }

    public function test_updatePassword(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $response = $this->put('/api/profile/password', [
            'password' => '1234567890',
        ]);
        $userResponse = $response->getContent();
        echo $userResponse;
        $response->assertStatus(200);

        $this->refreshApplication();

        $params = [
            'username' => $user->username,
            'password' => '1234567890'
        ];
        echo "\n";
        echo 'username: ' . $user['username'] . PHP_EOL;
        $responseLogin = $this->post('/api/check-login', $params);
        echo $responseLogin->getContent();
        $responseLogin->assertStatus(200);
    }

    public function test_updatePaymentMethod(): void
    {
        $response = $this->withHeaders([
            'Authorization' => 'Bearer 5|qp3WrlQaLvezo6mHfQCLv2LZCW00G5P4SPKO4TmUde48944b',
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json'
        ])->json('PUT', '/api/profile/payment', [
            "request_id" => 4,
            "payment_method" => "e_wallet"]);
        echo $response->getContent();
        $response->assertStatus(200);
    }


}
