<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SigninRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function signin(SigninRequest $request)
    {
        $data = $request->validated();

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return $this->unauthorizedResponse([], 'Invalid credentials');
        }

        $token = $user->createToken('api')->plainTextToken;

        return $this->okResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ]);
    }
}
