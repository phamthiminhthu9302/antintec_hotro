<?php

namespace App\Http\Controllers\Api;
trait HttpResponses
{
    public function success($data, $message = "Operation successfully", $code = 200)
    {
        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => $message,
        ], $code, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    public function fail($message = "Operation failed", $code = 400)
    {
        return response()->json([
            'status' => false,
            'message' => $message,
        ], $code, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }

    public function message($message, $code = 200){
        return response()->json([
            'status' => true,
            'message' => $message,
        ], $code, ['Content-Type' => 'application/json;charset=UTF-8', 'Charset' => 'utf-8'], JSON_UNESCAPED_UNICODE);
    }
}
