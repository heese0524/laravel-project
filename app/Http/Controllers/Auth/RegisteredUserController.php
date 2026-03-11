<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest; // 👈 1. 引入你自己的 Request
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    // 👇 2. 把参数从 Request $request 改成 RegisterRequest $request
    public function store(RegisterRequest $request): RedirectResponse
    {
        // 👈 3. 删除 $request->validate(...) 整行！验证已在 RegisterRequest 中自动完成

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect()->route('home');
    }
}