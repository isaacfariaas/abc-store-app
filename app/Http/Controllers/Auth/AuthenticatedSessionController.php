<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use DateInterval;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request)
    {

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = Auth::user();
            $expire_at = new DateTimeImmutable();
            $expire_at = $expire_at->add(new DateInterval('PT1440M'));
            $token = $user->createToken('JWT', ['*'], $expire_at);

            return response()->json(["login" => true, "token" => $token->plainTextToken, "error" => ""], 200);
        } else {
            return response()->json(["login" => false, "token" => "", "error" => "User invalid"], 401);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $request->user()->currentAccessToken()->delete();

        return response()->json(["logout" => true, "error" => ""], 200);
    }
}
