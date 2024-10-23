<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Message;

class ChatController extends Controller
{
    use HttpResponses;
    public function getMessagesByToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'receiver_id' => ['required', 'numeric', 'min:1']
        ]);
        try {
            $user = auth()->user();
            $result = Message::where('sender_id', $user->user_id)
                ->where('receiver_id', $validated['receiver_id'])
                ->orderByDesc('created_at')->get();
            if ($result->count() == 0) {
                return $this->fail('Not contain message between sender id ' . $user->user_id
                    . ' and receiver id ' . $validated['receiver_id']);
            }
            return $this->success($result);
        } catch (\Throwable $th) {
            return $this->fail($th->getMessage());
        }
    }

    public function getMessagesByUserId(Request $request, $id)
    {
        $isAdmin = $request->user()->tokenCan('role:admin');
        if ($isAdmin) {
            $result = Message::where('sender_id', '=', $id)->orderByDesc('created_at')->get();
            if ($result->count() == 0) {
                return $this->fail('Not contain message with sender id ' . $id);
            }
            return $this->success($result);
        }
        return $this->fail("Unauthorized", 401);
    }

    public function sendMessage(Request $request) : JsonResponse
    {
        try {
            $validated = $request->validate([
                'message' => ['required'],
                'receiver_id' => ['required', 'numeric', 'exists:users,user_id'],
                'request_id' => ['required', 'numeric']
            ]);
            $message = Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $validated['receiver_id'],
                'message' => $validated['message'],
                'is_seen' => false,
                'request_id' => $validated['request_id']
            ]);
            return $this->success($message);
        } catch (\Exception $e) {
            return $this->fail($e->getMessage(), 422); //validated fail
        }
    }

    public function seenMessage(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message_id' => ['required']
        ]);

        $message = Message::where('message_id', $validated['message_id'])->update(['is_seen' => true]);
        return $this->success($message);
    }
}
