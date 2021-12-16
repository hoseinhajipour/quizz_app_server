<?php

namespace App\Http\Livewire;

use App\Models\User;
use Illuminate\Http\Request;
use Livewire\Component;

class UserSearch extends Component
{
    public $username = "";

    public function mount()
    {

    }

    public function render(Request $request)
    {
        $users = User::where("username", "LIKE", "%$this->username%")
            ->where('id', '!=', auth()->user()->id)
            ->get();
        return view('livewire.user-search', [
            "users" => $users,
        ]);
    }
}
