<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Message;
use App\Http\Controllers\Api\HttpResponses;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    use HttpResponses;


    public function getMessagesByToken(Request $request)
    {
        $user = auth()->user();
        $result = Message::where('sender_id', '=', $user->user_id)->orderBy('created_at')->get();//remember to add orderBy createdAt
        return $this->success($result);
    }

    public function getMessagesBySenderId($id)
    {

        $result = Message::where('sender_id', '=', $id)->orderBy('created_at')->get();//remember to add orderBy createdAt
        if ($result->count() == 0) {
            return $this->fail('Not contain message with sender id ' . $id);
        }
        return $this->success($result);
    }

    public function sendMessage(Request $request)
    {

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
