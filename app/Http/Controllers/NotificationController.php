<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(){
        $user = Auth::user();
            $notifications = Notification::with([
            'comment.user',
            'forgive.user',
            'comment.post',
            'forgive.post'
          ])->where('user_id',$user->id)
          ->where('is_read',false)
          ->get();

          $result = $notifications->map(function($notification) {
            $type = $notification->comment instanceof Comment ? 'comment' : 'forgive';
            
            return [
              'type' => $type,
              'post' => $notification->$type?->post,
              'user' => $notification->$type?->user,
            ];
        });

        return response()->json($result->toArray());
          }

          public function count()
        {
        $user = Auth::user();

        $count = Notification::where('user_id', $user->id)
          ->where('is_read', false)
          ->count();

        return response()->json([
            'count' => $count
        ]);
      }
    }
