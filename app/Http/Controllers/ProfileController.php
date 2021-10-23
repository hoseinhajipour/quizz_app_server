<?php

namespace App\Http\Controllers;

use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function Profile(Request $request)
    {
        return Auth()->user();
    }

    public function myfriends(Request $request)
    {
        $friends = Friend::where("user_id", auth()->user()->id)->with("user_info")->get();

        return $friends;
    }

    public function followunfollowfriends(Request $request)
    {
        $isMyFriend = Friend::Where("user_id", auth()->user()->id)->where("to_user_id", $request->user_id)->first();
        if ($isMyFriend) {
            $isMyFriend->delete();
            return ['status' => "unfollow"];
        } else {
            $MyFriend = new Friend();
            $MyFriend->user_id = auth()->user()->id;
            $MyFriend->to_user_id = $request->user_id;
            $MyFriend->save();
            return ['status' => "follow"];
        }
    }

    public function searchUser(Request $request)
    {
        $users = User::where("username", "LIKE", "%$request->username%")->get();
        return $users;
    }

    public function updateProfile(Request $request)
    {
        $userInfo = auth()->user();
        if ($request->avatar_id) {
            $userInfo->avatar_id = $request->avatar_id;
        }
        if ($request->username) {
            $userInfo->username = $request->username;
        }
        if ($request->phone) {
            $userInfo->phone = $request->phone;
        }

        $userInfo->save();
    }
}