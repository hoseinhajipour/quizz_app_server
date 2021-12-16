<?php

namespace App\Http\Controllers;

use App\Http\Livewire\UserSearch;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        return view("pages.UserSearch");
    }
}
