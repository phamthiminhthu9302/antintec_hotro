<?php

namespace App\Providers;

use App\Models\Message;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class ShareDataViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        View::composer('*', function ($view) {
            // Lấy tên view hiện tại
            $currentPath = request()->path();


            // Nếu đường dẫn không nằm trong danh sách ngoại lệ thì chia sẻ dữ liệu
            $excludedPaths = [
                'logout',
                'register',
                'login',
                'profile',
                'reset-password/{token}',
                'forgot-password',
                'reset-password',
                'login/forgot-password',
                'static-sign-in',
                'static-sign-up'



            ];

            if (!in_array($currentPath, $excludedPaths)) {
                $user = Auth::id();

                $alert = Notification::where('user_id', $user)
                    ->orderBy('created_at', 'desc')
                    ->take(5) // Lấy 5 bản ghi đầu tiên
                    ->get();
                $notification = [];
                $count_notification = 0;
                foreach ($alert as $r) {
                    if (!$r->is_read) {
                        $count_notification++;
                    }
                    $notification[] = [
                        'notification_id' => $r->notification_id,
                        'user_id' => $r->user_id,
                        'message' => $r->message,
                        'is_read' => $r->is_read,
                        'created_at' => $r->created_at,
                    ];
                }
                $people = [];
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

                $new =  DB::select('SELECT DISTINCT (request_id) FROM messages WHERE sender_id = ? OR receiver_id = ?', [$user, $user]);;


                foreach ($new as $r) {
                    $message =  Message::where('request_id', $r->request_id)
                        ->orderBy('created_at', 'desc')
                        ->first();
                    $people[] = [
                        'user_c' => $user,
                        'sender_id' => $message->sender_id,
                        'request_id' => $message->request_id,
                        'is_seen' => $message->is_seen,
                        'message' => urldecode($message->message),
                        'created_at' => $message->created_at,
                    ];
                }



                $role = User::where('user_id', $user)->select('role')->first();
                // if ($role->role === 'customer') {
                //     $requests = DB::table('requests')
                //         ->where('customer_id', $user)
                //         ->where(function ($query) {
                //             $query->where('status', 'in_progress')
                //                 ->orWhere('status', 'completed');
                //         })
                //         ->select('technician_id', 'request_id')
                //         ->get();

                //     foreach ($requests as $r) {
                //         $receiver_id = $r->technician_id;
                //         $receiver_name = User::where('user_id', $receiver_id)->select('username')->first();
                //         $results[] = [
                //             'request_id' => $r->request_id,
                //             'receiver_id' => $receiver_id,
                //             'receiver_name' => $receiver_name->username
                //         ];
                //     }
                // } else {
                //     $requests = DB::table('requests')
                //         ->where('technician_id', $user)
                //         ->where(function ($query) {
                //             $query->where('status', 'in_progress')
                //                 ->orWhere('status', 'completed');
                //         })
                //         ->select('customer_id', 'request_id')
                //         ->get();

                //     foreach ($requests as $r) {
                //         $receiver_id = $r->customer_id;
                //         $receiver_name = User::where('user_id', $receiver_id)->select('username')->first();
                //         $results[] = [
                //             'request_id' => $r->request_id,
                //             'receiver_id' => $receiver_id,
                //             'receiver_name' => $receiver_name->username
                //         ];
                //     }
                // }


                // dd($people, $results, $notification);
                $view->with('notification', $notification);
                $view->with('user', $user);
                $view->with('count_message', $count);
                $view->with('count_notification',  $count_notification);
                $view->with('results', $results);
                $view->with('messages', $people);
            }
        });
    }
}
