<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class FlashMessage extends Component
{
    public $show = false;

    public $type = '';

    public $message = '';

    // Constants for session keys
    private const SESSION_TYPE_KEY = 'livewire_flash_type';
    private const SESSION_MESSAGE_KEY = 'livewire_flash_message';

    public function mount()
    {
        // Check for session flash message data on component mount
        if (session()->has(self::SESSION_TYPE_KEY) && session()->has(self::SESSION_MESSAGE_KEY)) {
            $this->type = session(self::SESSION_TYPE_KEY);
            $this->message = session(self::SESSION_MESSAGE_KEY);
            $this->show = true;

            // Auto-hide after 5 seconds
            $this->js('setTimeout(() => { $wire.hideMessage(); }, 5000);');
        }
    }

    #[On('show-flash-message')]
    public function showFlashMessage($data)
    {
        $this->type = $data['type'] ?? 'info';
        $this->message = $data['message'] ?? '';
        $this->show = true;

        // Store in session for persistence across redirects
        session()->put(self::SESSION_TYPE_KEY, $this->type);
        session()->put(self::SESSION_MESSAGE_KEY, $this->message);

        // Auto-hide after 5 seconds
        $this->js('setTimeout(() => { $wire.hideMessage(); }, 5000);');
    }

    // $this->dispatch('show-flash-message', [
    //     'type' => 'error',
    //     'message' => 'test',
    // ]);

    // Global event listener that stores data immediately when event is dispatched
    public function boot()
    {
        $this->js('
            document.addEventListener("livewire:init", () => {
                Livewire.on("show-flash-message", (data) => {
                    // Store in session immediately via AJAX to ensure persistence
                    fetch("/store-flash-message", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]").getAttribute("content"),
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify(data)
                    }).catch(error => {
                        console.warn("Flash message storage failed:", error);
                    });
                });
            });
        ');
    }

    public function hideMessage()
    {
        $this->show = false;

        // Clear session data when message is hidden
        session()->forget([self::SESSION_TYPE_KEY, self::SESSION_MESSAGE_KEY]);
    }

    public function render()
    {
        return view('livewire.flash-message');
    }
}
