<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\RegisterRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\StoreRequest;
use App\Http\Requests\User\UpdateRequest;
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

        return response()->json($users);
    }

    // public function store(StoreRequest $request)
    // {
    //     $userId = Auth::id();
    //     $existsProfile = User::where('user_id', $userId)->exists();

    //     if ($existsProfile) {
    //         return response()->json([
    //         'message' => 'すでにプロフィールが登録されています。'
    //     ]);
    // }

    //     if (is_null($request->image)) {
    //         User::create([
    //             'user_id' => $userId,
    //             'name' => $request->name,
    //             'image' => null,
    //         ]);

    //     return response()->json([
    //         'message' => 'プロフィール情報を登録しました。'
    //     ]);
    //     }

    //     $extension = $request->image->extension();
    //     $fileName = Str::uuid().'.'.$extension;
    //     $uploadedFilePath = Storage::disk('s3')->putFile('images', $request->image, $fileName);

    //     User::create([
    //         'user_id' => $userId,
    //         'name' => $request->name,
    //         'image' => $uploadedFilePath,
    //     ]);

    //     return response()->json([
    //         'message' => 'プロフィール情報を登録しました。'
    //     ]);
    // }

    // public function show($id)
    // {
    //     $profile = User::find($id);

    //     if (is_null($profile)) {
    //         return response()->json([
    //             'message' => 'プロフィールが存在しません。'
    //         ]);
    //     }

    //     $imagePath = Storage::disk('s3')->url(config('filesystems.disks.s3.bucket').'/'.$profile->image);

    //     return response()->json([
    //         'id' => $profile->id,
    //         'name' => $profile->name,
    //         'image' => $imagePath
    //         ]);
    //     }

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
    }
