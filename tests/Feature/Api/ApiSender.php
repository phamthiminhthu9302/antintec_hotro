<?php

namespace Tests\Feature\Api;

use Tests\TestCase;

class ApiSender extends TestCase
{
    public function sendApi($method, $url, $data, $authorization): \Illuminate\Testing\TestResponse
    {

        $headers = [
            'Accept' => 'application/vnd.api+json',
            'Content-Type' => 'application/vnd.api+json',
            'Authorization' => $authorization
        ];
        return $this->json($method, $url, $data, $headers);
    }
}
