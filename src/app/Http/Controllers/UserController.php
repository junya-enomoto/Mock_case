<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;

class UserController extends Controller
{
    public function showRegister()
    {
        return view('register');
    }
    
    public function showLogin()
    {
        return view('login');
    }

    
}
