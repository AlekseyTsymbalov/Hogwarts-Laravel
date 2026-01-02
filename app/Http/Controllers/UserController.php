<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class UserController extends Controller
{
    /**
     * Показать профиль конкретного пользователя.
     */
    public function signin(Request $request): User
    {
        $user = User::whereEmail($request->email)->first();
        if (!$user) {
            throw new RuntimeException('User not found');
        }

        if (!Hash::check($request->password, $user->password )) {
            throw new RuntimeException('Password is incorrect');
        }

        Auth::login($user);

        return $user;
    }
}