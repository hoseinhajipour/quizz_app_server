<?php

namespace App\Http\Controllers;

use App\Models\CoinUseType;
use App\Models\Friend;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function Profile(Request $request)
    {
        return ['status' => "ok", "userinfo" => Auth()->user()];
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
        return ["status" => "ok", "users" => $users];
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
        if ($request->notification_id) {
            $userInfo->notification_id = $request->notification_id;
        }

        $userInfo->save();

        return ["status" => "ok", "userInfo" => $userInfo];
    }

    public function UpdateCoinWallet(Request $request)
    {
        $CoinUseType = CoinUseType::where("id", $request->type_id)->first();

        $authUser = auth()->user();
        if ($CoinUseType->type == "decrease") {
            if ($authUser->coin >= abs($CoinUseType->amount)) {
                $authUser->coin += $CoinUseType->amount;
            }
        } else {
            $authUser->coin += $CoinUseType->amount;
        }

        $authUser->save();
        return ["status" => "ok",
            "coinUseType" => $CoinUseType,
            "userInfo" => $authUser
        ];
    }
}
