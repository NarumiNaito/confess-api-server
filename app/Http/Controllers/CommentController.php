<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\DeleteRequest;
use App\Http\Requests\Comment\StoreRequest;
use App\Models\Comment;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    public function index($id)
    {
    
        $comments = Comment::where('post_id', $id)
        ->select('comments.*','user_id','post_id','content', 'users.name', 'users.image',)
        ->join('users','comments.user_id', '=', 'users.id')
        ->orderBy('comments.updated_at','desc')
        ->paginate(5);

        $comments->each(function ($comment) {
            if ($comment->image) {
                $comment->image = Storage::disk('s3')->url(config('filesystems.disks.s3.bucket').'/'.$comment->image);
            }
            });
    
        return response()->json($comments);

    }
    public function show($id)
    {
    
        $comments = Comment::where('post_id', $id)
        ->select('comments.*','user_id','post_id','content', 'users.name', 'users.image',)
        ->join('users','comments.user_id', '=', 'users.id')
        ->orderBy('comments.updated_at','desc')
        ->paginate(5);

        $comments->each(function ($comment) {
            if ($comment->image) {
                $comment->image = Storage::disk('s3')->url(config('filesystems.disks.s3.bucket').'/'.$comment->image);
            }
            });
    
        return response()->json($comments);

    }
    public function store(StoreRequest $request)
    {
        $user = Auth::user();
        
        $comment = Comment::create([
            'user_id' => $user->id,
            'post_id' => $request->input('post_id'),
            'content' => $request->input('content'),
        ]);
        
        Notification::create([
            'user_id'=>$comment->post->user_id,
            'comment_id'=>$comment->id,
            'is_read'=>false
        ]);
        
        return response()->json([
            'message' => 'コメントを登録しました。',
        ]);
    }    
    public function delete(DeleteRequest $request)
    {
      
        $comment = Comment::find($request->input('id'));
        
        if (is_null($comment)) {
            return response()->json([
            'message' => '削除対象のコメントが存在しません。',
        ], 404);
        }
        
        Notification::where('comment_id',$comment->id)->delete();
        $comment->delete();
        
        return response()->json([
            'message' => '懺悔を削除しました。',
        ]);
    }
    public function updateNotification($commentId)
    {
        $user = Auth::user();

        Notification::where('user_id', $user->id)
          ->where('comment_id', $commentId)
          ->update(['is_read'=> true]);

        return response()->json([
            'message' => 'コメント内容を既読にしました。'
        ]);
    }
}
