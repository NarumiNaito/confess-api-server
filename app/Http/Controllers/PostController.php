<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\IndexRequest;
use App\Models\Category;
use App\Models\Post;

class PostController extends Controller
{
    public function index(IndexRequest $request)
    {
        // リクエストからパラメータを取得
        $searchWord = $request->input('search_word');
        $categoryId = $request->input('category_id');
        

        $query = Post::select('posts.*', 'users.name', 'users.image','categories.category_name')
        ->join('users', 'posts.user_id', '=', 'users.id')
        ->join('categories','posts.category_id','=','categories.id');

        // search_wordが存在する場合の条件追加
        if ($searchWord) {
        $query->where(function ($q) use ($searchWord) {
            $q->where('users.name', 'like', '%' . $searchWord . '%')
            ->orWhere('posts.content', 'like', '%' . $searchWord . '%');
        });
        }

        // category_idが存在する場合の条件追加
        if ($categoryId) {
        $query->where('posts.category_id', $categoryId);
        }


        // get()で実際にデータを取得
        $posts = $query->paginate(5);

        return response()->json($posts);
    }
}