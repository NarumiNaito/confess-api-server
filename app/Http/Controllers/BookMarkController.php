<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookMark\ToggleRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookMarkController extends Controller
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

        if ($request->input('is_bookmarks')) {
            $user->bookmarks()->syncWithoutDetaching($request->input('post_id'));
            return response()->json([
            'message' => '「ブックマーク」を登録しました。'
            ], 200);
        }

        $user->bookmarks()->detach($request->input('post_id'));

        return response()->json([
            'message' => '「ブックマーク」を解除しました。'
        ], 200);
    }
}
