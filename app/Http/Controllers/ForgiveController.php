<?php

namespace App\Http\Controllers;

use App\Http\Requests\Forgive\ToggleRequest;
use App\Models\Forgive;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForgiveController extends Controller
{
    public function toggle(ToggleRequest $request)
    {
        /** @var User */
        $user = Auth::user();

        $post = Post::find($request->input('post_id'));
        if (is_null($post)) {
            return response()->json([
            'message' => '対象の投稿が存在しません。'
            ], 404);
        }

        if ($request->input('is_forgive')) {
            $user->forgives()->syncWithoutDetaching($request->input('post_id'));
            return response()->json([
            'message' => '「赦す」を登録しました。'
            ], 200);
        }

        $user->forgives()->detach($request->input('post_id'));

        return response()->json([
            'message' => '「赦す」を解除しました。'
        ], 200);
    }
    
    public function index()
    {
        $forgive = Forgive::select(
            'forgives.*',
            'users.name',
            'users.image',
            'posts.content',
        )
        ->join('users', 'forgives.user_id', '=', 'users.id')
        ->join('posts', 'forgives.post_id', '=', 'posts.id')
        ->join('categories', 'posts.category_id', '=', 'categories.id')
        // ->groupBy('posts.id') 
        // ->distinct()
        ->orderBy('forgives.updated_at', 'desc') // 最新の順にソート（必要に応じて変更）
        ->paginate(5); // 5件ずつページネーション

    return response()->json($forgive);

    }
}
