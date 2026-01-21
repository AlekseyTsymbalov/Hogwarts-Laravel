<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function signin(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->notFoundResponse('Invalid credentials');
        }

        $token = $user->createToken('api')->plainTextToken;

        return $this->okResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }
}
