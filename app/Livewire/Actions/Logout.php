<?php

namespace App\Livewire\Actions;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Logout extends Component
{
    /**
     * Log the current user out of the application.
     */
    public function logout()
    {
        Auth::guard('web')->logout();
        session()->invalidate();
        session_start();
        session_unset();
        Session::flush();

        return $this->redirect('/', navigate: true);
    }

    public function render()
    {
        return view('livewire.actions.logout');
    }
}
