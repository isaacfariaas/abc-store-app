<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use DateInterval;
use DateTimeImmutable;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        if ($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string'],
        ])) {

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            event(new Registered($user));

            Auth::login($user);
            $expire_at = new DateTimeImmutable();
            $expire_at = $expire_at->add(new DateInterval('PT1440M'));
            $token = $user->createToken('JWT', ['*'], $expire_at);
            return response()->json(["register" => true, "token" => $token->plainTextToken, "error" => ""], 200);
        } else {
            return response()->json(["register" => false, "token" => "", "error" => "User not enabled"], 401);
        }
    }
}
