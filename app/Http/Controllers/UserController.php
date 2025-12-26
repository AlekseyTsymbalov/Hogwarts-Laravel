<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Показать профиль конкретного пользователя.
     */
    public function signin(Request $request): View
    {
        echo "<pre>";
        var_dump($request);
        die();
    }
}