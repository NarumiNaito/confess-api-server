<?php

namespace App\Http\Controllers;

use App\Http\Requests\Forgive\ToggleRequest;
use App\Models\Forgive;
use App\Models\Notification;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            $forgive = Forgive::where('user_id',$user->id)
            ->where('post_id',$request->input("post_id"))
            ->first();

            Notification::updateOrCreate([
                'user_id'=>$post->user->id,
                'forgive'=>$forgive->id
            ],[
                'is_read'=>false
            ]);
            
            return response()->json([
            'message' => '「赦す」を登録しました。'
            ], 200);
        }

        $user->forgives()->detach($request->input('post_id'));

        return response()->json([
            'message' => '「赦す」を解除しました。'
        ], 200);
    }
    public function index($id)
    {
        $forgive = Forgive::where('post_id', $id)
        ->select('forgives.*','user_id','post_id', 'users.name', 'users.image',)
        ->join('users','forgives.user_id', '=', 'users.id')
        ->orderBy('forgives.updated_at','desc')
        ->paginate(5);

        $forgive->each(function ($q) {
            if ($q->image) {
                $q->image = Storage::disk('s3')->url(config('filesystems.disks.s3.bucket').'/'.$q->image);
            }
            });
    
        return response()->json($forgive);
    }
}
