<?php

namespace App\Livewire\Auth;

use App\Helper;
use App\Models\User;
use App\Rules\ReCaptcha;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class LoginOtp extends Component
{
    public $email;

    public $verify_otp_code;

    public $step = 'email'; // steps: email → otp

    public $timer = 90;

    public $recaptchaToken;

    protected $rules = [
        'email' => 'required|email|max:200',
    ];

    public function mount()
    {
        // Rate limiting for login page - 10 times in 60 seconds - Start
        $request = request();
        $visitorId = $request->cookie('visitor_id');

        if (! $visitorId) {
            $visitorId = bin2hex(random_bytes(16));
            // Set the cookie for 30 days
            cookie()->queue(cookie('visitor_id', $visitorId, 60 * 24 * 30));
        }
        $key = md5(($visitorId ?: $request->ip()) . '|' . $request->header('User-Agent'));

        if (RateLimiter::tooManyAttempts($key, 10)) {
            abort(429);
        }

        RateLimiter::hit($key, 60);
        // Rate limiting for login page - 10 times in 60 seconds - End

        $this->dispatch('autoFocusElement', elId: 'email');
    }

    public function login()
    {
        Helper::logInfo(static::class, __FUNCTION__, 'Start', [
            'email' => $this->email,
        ]);

        $this->verify_otp_code = '';
        $this->email = str_replace(' ', '', $this->email);

        $this->validate();

        if (! App::environment(['local']) && ! empty($this->recaptchaToken)) {
            $recaptchaResponse = ReCaptcha::verify($this->recaptchaToken);
            if (! $recaptchaResponse['success']) {
                $this->clearForm(); // clear all form data  
                session()->flash('error', __('messages.login.recaptchaError'));
                return;
            }
        }

        $email = Str::lower($this->email);
        $user = User::where('email', $email)->first();

        if ($user) { // User Found
            if ($user->status != config('constants.user.status.key.active')) {
                session()->flash('error', __('messages.login.unverified_account'));

                return;
            } else {
                if ($email == 'admin@gmail.com' || ! App::environment(['production'])) { // TODO When you try to dynamic OTP for non-production environment please remove this condition
                    $otp = '123456';                                                        // generate a random 6 digit OTP code
                } else {
                    $otp = User::getOTP(); // generate a random 6 digit OTP code
                }

                $user->otp = $otp;
                $user->otp_expires_at = Carbon::now();
                $user->password = bcrypt($email);
                $user->save();

                // self::sendUserOtp($user, $otp);
                session()->flash('success', __('messages.login.otp_successfully'));
                $this->step = 'otp';
                $this->dispatch('otp-start-timer');
                $this->dispatch('autoFocusElement', elId: 'verify_otp_code');
            }
        } else {
            Helper::logInfo(static::class, __FUNCTION__, __('messages.login.invalid_credentials_error'), ['email' => $email]);
            session()->flash('error', __('messages.login.invalid_credentials_error'));
        }
    }

    public function verifyOtp()
    {
        $this->validate([
            'verify_otp_code' => 'required|decimal:0|digits:6',
        ]);

        $email = Str::lower($this->email);
        $user = User::where('email', $email)->first();

        if ($user->status != config('constants.user.status.key.active')) {
            session()->flash('error', __('messages.login.unverified_account'));

            return;
        } else {
            if ($user->otp != $this->verify_otp_code) { // Requested OTP is mismatch
                session()->flash('error', __('messages.login.invalid_otp_error'));

                return;
            } elseif ($user->otp_expires_at >= Carbon::now()->subMinutes(10)) { // OTP is valid
                $credentials = [
                    'email' => $email,
                    'password' => $email,
                ];

                $authAttempt = Auth::attempt($credentials); // Authentication passed
                if (! $authAttempt) {
                    session()->flash('error', __('messages.login.invalid_credentials_error'));

                    return;
                }

                if (App::environment(['production', 'uat']) && $email != 'admin@gmail.com') {
                    Auth::logoutOtherDevices($email); // Logout all other sessions
                }

                $this->clearForm(); // clear all form data

                $user->otp = null;
                $user->otp_expires_at = null;
                $user->save();

                $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true); // redirect to dashboard
            } else { // OTP Valid for 10 minute -> OTP expired
                session()->flash('error', __('messages.login.expired_otp_error'));
            }
        }
    }

    public function back()
    {
        $this->step = 'email';
        $this->dispatch('autoFocusElement', elId: 'email');
    }

    public function render()
    {
        return view('livewire.auth.login-otp')->title(__('messages.meta_titles.login'));
    }

    public function clearForm()
    {
        $this->email = '';
        $this->verify_otp_code = '';
    }
}
