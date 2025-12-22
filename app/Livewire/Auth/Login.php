<?php

namespace App\Livewire\Auth;

use App\Helper;
use App\Rules\ReCaptcha;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email|max:191')]
    public string $email = '';

    #[Validate('required|string|min:6|max:191')]
    public string $password = '';

    public $recaptchaToken;

    public function mount()
    {
        $this->dispatch('autoFocusElement', elId: 'email');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function login()
    {
        $this->email = str_replace(' ', '', $this->email);

        $this->validate();

        // Rate Limiting: 5 attempts per minute
        $throttleKey = Str::lower($this->email) . '|' . request()->ip();
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            session()->flash('error', "Too many login attempts. Please try again in $seconds seconds.");
            return;
        }

        if (App::environment(['production', 'uat'])) {
            $recaptchaResponse = ReCaptcha::verify($this->recaptchaToken);
            if (! $recaptchaResponse['success']) {
                $this->clearForm(); // clear all form data
                Helper::logInfo(static::class, __FUNCTION__, __('messages.login.recaptchaError'), ['email' => $this->email]);
                session()->flash('error', __('messages.login.recaptchaError'));

                return;
            }
        }

        $email = Str::lower($this->email);

        $credentials = [
            'email' => $email,
            'password' => $this->password,
        ];

        if (Auth::attempt($credentials)) { // User Found
            RateLimiter::clear($throttleKey); // Clear attempts on success

            $user = Auth::user();

            // Use Laravel session to avoid native PHP session locking
            session(['user_id' => $user->id]);

            if (App::environment(['production', 'uat'])) {
                Auth::logoutOtherDevices($this->password); // Logout all other sessions
            }

            $this->clearForm(); // clear all form data

            if ($user->status != config('constants.user.status.key.active')) {
                // INACTIVE user error handling
                session()->flash('error', __('messages.login.unverified_account'));

                return;
            } else {
                session()->flash('success', __('messages.login.success'));
                $this->redirectRoute('dashboard', absolute: false, navigate: true); // force redirect to dashboard
            }
        } else {
            RateLimiter::hit($throttleKey); // Record failed attempt

            Helper::logInfo(static::class, __FUNCTION__, __('messages.login.invalid_credentials_error'), ['email' => $email]);
            session()->flash('error', __('messages.login.invalid_credentials_error'));
        }
    }

    public function render()
    {
        return view('livewire.auth.login')->title(__('messages.meta_titles.login'));
    }

    public function clearForm()
    {
        $this->email = '';
        $this->password = '';
    }
}
