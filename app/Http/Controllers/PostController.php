<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\DeleteRequest;
use App\Http\Requests\Post\IndexRequest;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Models\Post;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $posts = $query
        ->orderBy('updated_at','desc')
        ->paginate(5);
        

        return response()->json($posts);
    }


    public function store(StoreRequest $request)
    {
        $user = Auth::user();
        
        Post::create([
            'user_id' => $user->id,
            'category_id' => $request->input('category_id'),
            'content' => $request->input('content'),
        ]);
        
        return response()->json([
            'message' => '懺悔を登録しました。',
        ]);
    }    
    public function show()
    {
        $user_id = Auth::user()->id;
        
        $query = Post::where('user_id', $user_id)
        ->select('posts.id','user_id','category_id','content','category_name')
        ->join('categories','posts.category_id','=','categories.id')
        ->orderBy('posts.updated_at','desc')
        ->paginate(5);
        
        return response()->json($query);
    }    
    public function update(UpdateRequest $request)
    {
        $user = Auth::user();
        $post = Post::where('user_id', $user->id)
        ->find($request->input('id'));

        if (is_null($post)) {
            return response()->json([
                'message' => '更新対象の懺悔が存在しません。',
            ], 404);
        }

        $post->update([
            'category_id' => $request->input('category_id'),
            'content' => $request->input('content'),
        ]);

        return response()->json([
            'message' => '懺悔を更新しました。',
        ]);
    }
    public function delete(DeleteRequest $request)
    {
        $user = Auth::user();
        $post = Post::where('user_id', $user->id)->find($request->input('id'));
        
        if (is_null($post)) {
            return response()->json([
            'message' => '削除対象の懺悔が存在しません。',
        ], 404);
        }

        Log::debug(print_r($post));
        
        $post->delete();
        
        return response()->json([
            'message' => '懺悔を削除しました。',
        ]);
    }
}