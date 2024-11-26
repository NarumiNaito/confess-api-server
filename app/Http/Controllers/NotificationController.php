<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class NotificationController extends Controller
{
    public function index(){
        $user = Auth::user();
            $notifications = Notification::with([
            'comment.user',
            'forgive.user',
            'comment.post',
            'forgive.post',
            'comment.post.user',
            'forgive.post.user',
            'comment.post.category',
            'forgive.post.category'
          ])->where('user_id',$user->id)
          ->where('is_read',false)
          ->orderBy('updated_at','desc')
          ->get();


          $result = $notifications->map(function($notification) {
            $type = $notification->comment instanceof Comment ? 'comment' : 'forgive';
            $image = $notification->$type->user->image;
            $categoryName = $notification->$type?->post?->category?->category_name;
            $userName = $notification->$type?->post?->user?->name;
            $userImage = $notification->$type?->post?->user?->image;



            return [
              'type' => $type,
              'post' => $notification->$type?->post,
              'user' => $notification->$type?->user,
              $type.'_id' => $notification->$type->id,
              'userImage' => $userImage ? Storage::disk('s3')->url(config('filesystems.disks.s3.bucket') . '/' . $userImage):null,
              'image' => $image ? Storage::disk('s3')->url(config('filesystems.disks.s3.bucket') . '/' . $image):null,
              'category_name' => $categoryName,
              'userName' => $userName,
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
