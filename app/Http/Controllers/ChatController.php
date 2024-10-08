<?php

namespace App\Http\Controllers;

use App\Events\MessagePeople;
use App\Http\Controllers\Controller;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Events\MessageSent;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{

    public function getUserCurrent()
    {
        $user = Auth::id();

        $people = [];

        $new =  DB::select('SELECT DISTINCT (request_id) FROM messages WHERE sender_id = ? OR receiver_id = ?', [$user, $user]);


        foreach ($new as $r) {
            $message =  Message::where('request_id', $r->request_id)
                ->orderBy('created_at', 'desc')
                ->first();
            $people[] = [
                'user_c' => $user,
                'receiver_id' => $message->receiver_id,
                'sender_id' => $message->sender_id,
                'request_id' => $message->request_id,
                'is_seen' => $message->is_seen,
                'message' => $message->message,
                'created_at' => $message->created_at,
            ];
        }
        $count_request = [];
        foreach ($new as $r) {

            $message =  Message::where('request_id', $r->request_id)->where('receiver_id', $user)
                ->where('is_seen', 'false')
                ->selectRaw('count(is_seen)as count')->get();
            $count_request[] =
                [
                    'request_id' => $r->request_id,
                    'count' => $message[0]->count
                ];
        }

        $count = 0;
        $messageis_seen =  Message::where('receiver_id', $user)
            ->select('is_seen')
            ->get();
        foreach ($messageis_seen as $r) {
            if ($r->is_seen == false) {
                $count++;
            }
        }

        $results = [];
        $role = User::where('user_id', $user)->select('role')->first();
        if ($role->role == 'customer') {
            $requests = DB::table('requests')
                ->where('customer_id', $user)
                ->where(function ($query) {
                    $query->where('status', 'in_progress')
                        ->orWhere('status', 'completed');
                })
                ->select('technician_id', 'request_id', 'customer_id')
                ->get();

            foreach ($requests as $r) {
                $receiver_id = $r->technician_id;
                $receiver_name = User::where('user_id', $receiver_id)->select('username')->first();
                $results[] = [
                    'request_id' => $r->request_id,
                    'receiver_id' => $receiver_id,
                    'sender_id' => $r->customer_id,
                    'user_c' => $user,
                    'receiver_name' => $receiver_name->username
                ];
            }
        } else {
            $requests = DB::table('requests')
                ->where('technician_id', $user)
                ->where(function ($query) {
                    $query->where('status', 'in_progress')
                        ->orWhere('status', 'completed');
                })
                ->select('customer_id', 'request_id', 'technician_id')
                ->get();


            foreach ($requests as $r) {
                $receiver_id = $r->customer_id;
                $receiver_name = User::where('user_id', $receiver_id)->select('username')->first();
                $results[] = [
                    'request_id' => $r->request_id,
                    'receiver_id' => $receiver_id,
                    'sender_id' => $r->technician_id,
                    'user_c' => $user,
                    'receiver_name' => $receiver_name->username
                ];
            }
        }

        return  response()->json(['count_request' => $count_request, 'results' => $results, 'message' => $people, 'count' => $count]);
    }
    public function getMessages($request_id, $receiver_id)
    {

        $user = Auth::id();
        $messages = Message::where('request_id', $request_id)
            ->with(['sender', 'receiver'])
            ->orderBy('created_at', 'asc')
            ->get();
        $user_name = User::where('user_id', $receiver_id)->first();
        return response()->json([
            'messages' => $messages,
            'request_id' => $request_id,
            'receiver_id' => $receiver_id,
            'user' => $user,
            'user_name' => $user_name
        ]);
    }

    // Xử lý gửi tin nhắn
    public function sendMessage(Request $request)
    {
        $mess = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'request_id' => $request->request_id,
            'message' => $request->message,
        ]);

        $user = Auth::id();

        $people = [];

        $new =  DB::select('SELECT DISTINCT (request_id) FROM messages WHERE sender_id = ? OR receiver_id = ?', [$user, $user]);;
        event(new MessageSent(Auth::user(), $mess));

        foreach ($new as $r) {
            $message =  Message::where('request_id', $r->request_id)
                ->orderBy('created_at', 'desc')
                ->first();
            $people[] = [
                'user_c' => $user,
                'receiver_id' => $message->receiver_id,
                'sender_id' => $message->sender_id,
                'request_id' => $message->request_id,
                'is_seen' => $message->is_seen,
                'message' => $message->message,
                'created_at' => $message->created_at,
            ];
        }
        $count_request = [];
        foreach ($new as $r) {

            $message =  Message::where('request_id', $r->request_id)->where('receiver_id', $user)
                ->where('is_seen', 'false')
                ->selectRaw('count(is_seen)as count')->get();
            $count_request[] =
                [
                    'request_id' => $r->request_id,
                    'count' => $message[0]->count
                ];
        }

        $results = [];
        $role = User::where('user_id', $user)->select('role')->first();
        if ($role->role == 'customer') {
            $requests = DB::table('requests')
                ->where('customer_id', $user)
                ->where(function ($query) {
                    $query->where('status', 'in_progress')
                        ->orWhere('status', 'completed');
                })
                ->select('technician_id', 'request_id', 'customer_id')
                ->get();

            foreach ($requests as $r) {
                $receiver_id = $r->technician_id;
                $receiver_name = User::where('user_id', $receiver_id)->select('username')->first();
                $results[] = [
                    'request_id' => $r->request_id,
                    'receiver_id' => $receiver_id,
                    'sender_id' => $r->customer_id,
                    'user_c' => $user,
                    'receiver_name' => $receiver_name->username
                ];
            }
        } else {
            $requests = DB::table('requests')
                ->where('technician_id', $user)
                ->where(function ($query) {
                    $query->where('status', 'in_progress')
                        ->orWhere('status', 'completed');
                })
                ->select('customer_id', 'request_id', 'technician_id')
                ->get();


            foreach ($requests as $r) {
                $receiver_id = $r->customer_id;
                $receiver_name = User::where('user_id', $receiver_id)->select('username')->first();
                $results[] = [
                    'request_id' => $r->request_id,
                    'receiver_id' => $receiver_id,
                    'sender_id' => $r->technician_id,
                    'user_c' => $user,
                    'receiver_name' => $receiver_name->username
                ];
            }
        }
        event(new MessagePeople($results, $people, $count_request));
        return ['status' => 'Message Sent!'];
    }



    public function markAsSeen(Request $request)
    {
        $message = Message::where('message_id', $request->messageIds)->first();

        if ($message) {
            $message->is_seen = true;
            $message->save();
        }

        return response()->json(['success' => true]);
    }
}
