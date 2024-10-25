<?php

namespace App\Http\Controllers;

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
        ->join('users','comments.user_id', '=', 'users.id')
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
}
