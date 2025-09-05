<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Http\Requests\AuthRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function auth(AuthRequest $request)
    {
        $request = $request->validated();

        $user = User::where('email', $request['email'])->first();
        if (! $user || ! Hash::check($request['password'], $user->password)) {
            return ResponseHelper::jsonResponse(false, 'Email atau password salah.', null, 400);
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $data = [
            'user' => $user,
            'token' => $token
        ];
        return ResponseHelper::jsonResponse(true, 'User berhasil login', $data, 200);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ResponseHelper::jsonResponse(true, 'User berhasil logout', null, 200);
    }
}
