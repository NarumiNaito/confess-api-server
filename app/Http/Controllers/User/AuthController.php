<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Models\BookMark;
use App\Models\Comment;
use App\Models\Forgive;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!Auth::guard('user')->attempt($credentials)) {
            throw new AuthenticationException('ログインに失敗しました。');
        }
        
        $request->session()->regenerate();

        return response()->json([
            'message' => 'ログインしました。'
        ]);
    }

    public function register(RegisterRequest $request)
    {
        
        $existsEmail = User::where('email', $request->email)->exists();

        if ($existsEmail) {
            return response()->json([
                'message' => 'メールアドレスがすでに登録されています。'
            ],410
        );
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::guard('user')->login($user);

        return response()->json([
            'message' => 'ユーザ登録が完了しました。',
        ]);
        
    }

    public function logout(Request $request)
    {
        Auth::guard('user')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'ログアウトしました。',
        ]);
    }
    public function User()
    {
        
        $user_id = Auth::id(); 
        $users = User::where('id', $user_id)
        ->select('id','name','email','password','image','created_at','updated_at')
        ->get();

        $users->each(function ($user) {
            if ($user->image) {
                $user->image = Storage::disk('s3')->url(config('filesystems.disks.s3.bucket').'/'.$user->image);
            }
            });

        return response()->json($users);
    }

    

        public function update(UpdateRequest $request)
        {
            $profile = User::find($request->id);
    
            if (is_null($profile)) {
                return response()->json([
                    'message' => '更新対象のプロフィールが存在しません。'
                ]);
            }
    
            $oldImage = $profile->image;
    
            if (is_null($request->image)) {
                $profile->update([
                    'name' => $request->name,
                    'image' => null,
                ]);
    
                $oldImage && Storage::disk('s3')->delete($oldImage);
        
                return response()->json([
                    'message' => 'プロフィール情報を更新しました。'
                ]);
            }
    
            $extension = $request->image->extension();
            $fileName = Str::uuid().'.'.$extension;
    
            $uploadedFilePath = Storage::disk('s3')->putFile('images', $request->image, $fileName);
    
            $profile->update([
                'name' => $request->name,
                'image' => $uploadedFilePath,
            ]);
    
            $oldImage && Storage::disk('s3')->delete($oldImage);
    
            return response()->json([
                'message' => 'プロフィール情報を更新しました。'
            ]);
        }

        public function delete(Request $request)
        {
            
            $validatedData = $request->validate([
                'email' => 'required|email',
                'password' => 'required|string',
                'id' => 'required|integer',
            ]);
        
            
            $email = $validatedData['email'];
            $password = $validatedData['password'];
            $userId = $validatedData['id'];
        
            
            $user = User::find($userId);
        
            if (is_null($user)) {
                return response()->json([
                    'message' => '削除対象のアカウントが存在しません。',
                ], 403);
            }
        
            
            if ($user->email !== $email) {
                return response()->json([
                    'message' => 'メールアドレスが一致しません。',
                ], 404);
            }
        
            
            if (!Hash::check($password, $user->password)) {
                return response()->json([
                    'message' => 'パスワードが一致しません。',
                ], 405);
            }
        
            Post::where('user_id', '=', $request->id)->delete();
            Forgive::where('user_id', '=', $request->id)->delete();
            Comment::where('user_id', '=', $request->id)->delete();
            Notification::where('user_id', '=', $request->id)->delete();
            BookMark::where('user_id', '=', $request->id)->delete();
            $user->delete();
        
            return response()->json([
                'message' => 'アカウントを削除しました。',
            ], 200);
        }
    }
