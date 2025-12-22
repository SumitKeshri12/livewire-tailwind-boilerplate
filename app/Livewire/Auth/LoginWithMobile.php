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
class LoginWithMobile extends Component
{
    public $mobile_number;

    public $verify_otp_code;

    public $step = 'mobile'; // steps: mobile → otp

    public $timer = 90;

    public $recaptchaToken;

    protected $rules = [
        'mobile_number' => 'required|digits:10|regex:/^[0-9]{10}$/',
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

        $this->dispatch('autoFocusElement', elId: 'mobile_number');
    }

    public function login()
    {
        Helper::logInfo(static::class, __FUNCTION__, 'Start', [
            'mobile_number' => $this->mobile_number,
        ]);

        $this->verify_otp_code = '';
        $this->mobile_number = str_replace(' ', '', $this->mobile_number);

        $this->validate();

        if (! App::environment(['local']) && ! empty($this->recaptchaToken)) {
            $recaptchaResponse = ReCaptcha::verify($this->recaptchaToken);
            if (! $recaptchaResponse['success']) {
                $this->clearForm(); // clear all form data
                session()->flash('error', __('messages.login.recaptchaError'));
                return;
            }
        }

        $mobile_number = Str::lower($this->mobile_number);
        $user = User::where('mobile_number', $mobile_number)->first();

        if ($user) { // User Found
            if ($user->status != config('constants.user.status.key.active')) {
                session()->flash('error', __('messages.login.unverified_account'));
                return;
            } else {
                if (! App::environment(['production'])) {
                    $otp = '123456'; // generate a random 6 digit OTP code
                } else {
                    $otp = User::getOTP(); // generate a random 6 digit OTP code
                }

                $user->otp = $otp;
                $user->otp_expires_at = Carbon::now();
                $user->password = bcrypt($mobile_number);
                $user->save();

                User::sendUserOtpBySMS($user, $otp);

                session()->flash('success', __('messages.login.otp_successfully'));
                $this->step = 'otp';
                $this->dispatch('otp-start-timer');
                $this->dispatch('autoFocusElement', elId: 'verify_otp_code');
            }
        } else {
            Helper::logInfo(static::class, __FUNCTION__, __('messages.login.invalid_credentials_error'), ['mobile_number' => $mobile_number]);
            session()->flash('error', __('messages.login.invalid_credentials_error'));
        }
    }

    public function verifyOtp()
    {
        $this->validate([
            'verify_otp_code' => 'required|decimal:0|digits:6',
        ]);

        $mobile_number = Str::lower($this->mobile_number);
        $user = User::where('mobile_number', $mobile_number)->first();

        if ($user->status != config('constants.user.status.key.active')) {
            session()->flash('error', __('messages.login.unverified_account'));

            return;
        } else {
            if ($user->otp != $this->verify_otp_code) { // Requested OTP is mismatch
                session()->flash('error', __('messages.login.invalid_otp_error'));
                return;
            } elseif ($user->otp_expires_at >= Carbon::now()->subSeconds(90)) { // OTP is valid

                $credentials = [
                    'mobile_number' => $mobile_number,
                    'password' => $mobile_number,
                ];

                $authAttempt = Auth::attempt($credentials); // Authentication passed
                if (! $authAttempt) {
                    session()->flash('error', __('messages.login.invalid_credentials_error'));
                    return;
                }

                if (!App::environment(['local'])) {
                    Auth::logoutOtherDevices($mobile_number); // Logout all other sessions
                }

                $this->clearForm(); // clear all form data

                $user->otp = null;
                $user->otp_expires_at = null;
                $user->save();

                $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true); // redirect to dashboard

            } else {
                // OTP is expired
                session()->flash('error', __('messages.login.expired_otp_error'));
                return;
            }
        }
    }

    public function back()
    {
        $this->step = 'mobile';
        $this->dispatch('autoFocusElement', elId: 'mobile_number');
    }

    public function render()
    {
        return view('livewire.auth.login-with-mobile')->title(__('messages.meta_titles.login'));
    }

    public function clearForm()
    {
        $this->mobile_number = '';
        $this->verify_otp_code = '';
    }
}
