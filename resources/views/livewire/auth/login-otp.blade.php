<div class="flex flex-col gap-6">
    <x-session-message />

    @if ($step === 'email')
    <x-auth-header :title="__('messages.login.title')" :description="__('Enter your email to receive an OTP')" />

    <form wire:submit="login" method="POST" class="flex flex-col gap-6">
        <!-- Email -->
        <flux:input wire:model="email" :label="__('messages.login.label_email')" type="email" required autofocus autocomplete="email" placeholder="email@example.com" onblur="value=value.trim()" id="email" />

        <input type="hidden" id="recaptcha-token" name="recaptcha_token" wire:model="recaptchaToken">

        <div class="flex items-center justify-end">
            <flux:button variant="primary" class="w-full" type="submit" wire:loading.attr="disabled" data-test="login-button" wire:loading.class="opacity-50" wire:target="login" id="login-button">
                {{ __('messages.submit_button_text') }}
            </flux:button>
        </div>
    </form>
    @endif

    @if ($step === 'otp')
    <x-auth-header :title="__('messages.login.verify_otp_title')" :description="$email" :showBack="true" />

    <form wire:submit="verifyOtp" class="flex flex-col gap-6">
        <!-- OTP -->
        <flux:input wire:model="verify_otp_code" :label="__('messages.login.label_verify_otp')" type="text" maxlength="6" required placeholder="{{ __('messages.login.label_verify_otp') }}" id="verify_otp_code" />

        <!-- Timer display -->
        <div class="text-sm text-center text-zinc-600 dark:text-zinc-400">
            <span id="otp-timer">
                Resend OTP in {{ $timer }} seconds
            </span>
        </div>

        <div class="flex items-center justify-end">
            <flux:button variant="primary" type="submit" class="w-full" data-test="verify-otp-button">
                {{ __('messages.verify_otp_button_text') }}
            </flux:button>
        </div>
    </form>
    @endif
</div>

@push('scripts')
<script src="https://www.google.com/recaptcha/api.js?render={{ config('constants.google_recaptcha_key') }}"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('otp-start-timer', () => {
            let timer = 90;
            let resendClickMethod = 'login';
            setTimeout(() => {
                const timerEl = document.getElementById('otp-timer');
                if (!timerEl) return; // ✅ prevent null error

                const countdown = setInterval(() => {
                    if (timer <= 0) {
                        clearInterval(countdown);
                        timerEl.innerHTML =
                            `<span class="text-muted me-1">Did not receive the OTP ?</span><a href="#" class="link-primary fs-5 me-1" wire:click.prevent="${resendClickMethod}">Resend</a>`;
                    } else {
                        timerEl.textContent = `Resend OTP in ${timer} seconds`;
                    }
                    timer--;
                }, 1000);
            }, 50);
        });
    });

    $("body").delegate("#login-button", "click", function() {
        event.preventDefault(); // Prevent the form from submitting immediately
        grecaptcha.ready(function() {
            grecaptcha.execute("{{ config('constants.google_recaptcha_key') }}", {
                action: 'login'
            }).then(function(token) {
                @this.set('recaptchaToken', token).then(function() {
                    @this.call('login'); // Call the Livewire method to submit the form
                });
            });
        });
    });

</script>
@endpush
