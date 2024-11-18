<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\DeleteRequest;
use App\Http\Requests\Comment\StoreRequest;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function index($id)
    {
    
        $comment = Comment::where('post_id', $id)
        ->select('comments.*','user_id','post_id','content', 'users.name', 'users.image',)
        ->join('users','comments.user_id', '=', 'users.id')
        ->orderBy('comments.updated_at','desc')
        ->paginate(5);
    
        return response()->json($comment);

    }
    public function show($id)
    {
    
        $comment = Comment::where('post_id', $id)
        ->select('comments.*','user_id','post_id','content', 'users.name', 'users.image',)
        ->join('users','comments.user_id', '=', 'users.id')
        ->orderBy('comments.updated_at','desc')
        ->paginate(5);
    
        return response()->json($comment);

    }
    public function store(StoreRequest $request)
    {
        $user = Auth::user();
        
        Comment::create([
            'user_id' => $user->id,
            'post_id' => $request->input('post_id'),
            'content' => $request->input('content'),
        ]);
        
        return response()->json([
            'message' => 'コメントを登録しました。',
        ]);
    }    
    public function delete(DeleteRequest $request)
    {
        // $user = Auth::user();
        $comment = Comment::find($request->input('id'));
        
        if (is_null($comment)) {
            return response()->json([
            'message' => '削除対象のコメントが存在しません。',
        ], 404);
        }
        
        $comment->delete();
        
        return response()->json([
            'message' => '懺悔を削除しました。',
        ]);
    }
}
