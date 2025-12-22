<?php
namespace App\Services;

class SMSMessage
{
    // Property to hold the message content
    public $message;

    // Method to set the message content
    public function message($message)
    {
        $this->message = $message; // Assign the passed message to the message property
        return $this;              // Return the instance for method chaining
    }

}
