<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\SigninRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $user = User::create($request->validated());

        return $this->createdResponse(
            new UserResource($user)
        );
    }

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

    public function signout(Request $request)
    {
        $request->user()
            ->currentAccessToken()
            ->delete();

        return $this->noContentResponse();
    }
}
