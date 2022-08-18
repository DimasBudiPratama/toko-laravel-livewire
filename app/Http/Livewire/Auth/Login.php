<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Login extends Component
{
    public $form = [
        'email' => '',
        'password' => ''
    ];

    public function submit()
    {
        $this->validate([
            'form.email'    => 'required|email',
            'form.password' => 'required'
        ]);

        $user = User::where([
            "email" => $this->form["email"]
        ])->get();

        if (count($user) == 1 && Auth::attempt($this->form)) {
            redirect()->route('home');
        } else {
            session()->flash('error', 'Email & Password yang anda masukkan salah!!');
        }
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
