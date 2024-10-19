<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function index($id)
    {
       
        $comment = Comment::where('post_id', $id)->paginate(5);
      
        return response()->json($comment);

        // $query = Post::select('posts.*', 'users.name', 'users.image','categories.category_name')
        // ->join('users', 'posts.user_id', '=', 'users.id')
        // ->join('categories','posts.category_id','=','categories.id');

        // // search_wordが存在する場合の条件追加
        // if ($searchWord) {
        // $query->where(function ($q) use ($searchWord) {
        //     $q->where('users.name', 'like', '%' . $searchWord . '%')
        //     ->orWhere('posts.content', 'like', '%' . $searchWord . '%');
        // });
        // }

        // // category_idが存在する場合の条件追加
        // if ($categoryId) {
        // $query->where('posts.category_id', $categoryId);
        // }


        // get()で実際にデータを取得
        // $posts = $query
        // ->orderBy('updated_at','desc')
        // ->paginate(5);
        

     
    }
}
