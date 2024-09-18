<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\LoginRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;


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
}