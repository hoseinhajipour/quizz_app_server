<?php

namespace App\Http\Controllers;

use App\Models\Token;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {

            return Auth()->user()->createToken($request->email);
        }
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'username' => 'required',
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $user = User::where("email", $request->email)->first();
        if (!$user) {
            $user = User::create(request(['username', 'email', 'password']));
        }
        Auth()->login($user);
        $token = Auth()->user()->createToken($request->email);

        return $token;

    }

    public function guestuser(Request $request)
    {
        $lastID = User::all()->count() + 1;
        $username = "quest_" . $lastID;
        $user = new User();
        $user->username = $username;
        $user->rank = 1;
        $user->role_id = 2;
        $user->coin = setting('gamesetting.starter_coin');
        $user->save();
        Auth()->login($user);
        $token = Auth()->user()->createToken($user->username);
        return $token;
    }


}
