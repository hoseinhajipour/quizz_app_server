<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class Leaderboard extends Component
{
    public $Leaderboards=[];

    public function mount(){
        $this->Leaderboards = User::orderBy('score', 'DESC')->get()->take(10);
    }
    public function render()
    {
        return view('livewire.leaderboard');
    }
}
