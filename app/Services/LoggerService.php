<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Support\Facades\Log;
use Throwable;

class LoggerService
{
    /**
     * Log validation errors.
     */
    public function logValidationError(string $controller_name, string $function_name, Validator $validator, $user = null, string $channel = 'validation'): void
    {
        try {
            Log::channel($channel)->error("$controller_name: $function_name: Validation error occurred. :", [
                "errors_message" => $validator->errors()->all(),
                "key_failed" => $validator->failed(),
                "all_request" => $this->sanitize(request()->all()),
                "default_auth_detail" => $user,
                "all_headers" => $this->sanitize(request()->headers->all()),
                "ip_address" => request()->ip(),
            ]);
        } catch (Throwable $th) {
            $this->logInternalError($th, $controller_name, $function_name, [], $channel);
        }
    }

    /**
     * Log exceptions or errors with stack trace.
     */
    public function logCatchError(Throwable $th, string $controller_name, string $function_name, array $extra_param = [], $user = null, ?string $channel = null): void
    {
        try {
            $dataArray = [
                "Exception" => $th->getMessage(),
                "TraceAsString" => $th->getTraceAsString(),
            ] + $this->getContext($extra_param, $user);

            Log::channel($channel)->error("$controller_name: $function_name: Throwable:", $dataArray);
        } catch (Throwable $innerTh) {
            $this->logInternalError($innerTh, $controller_name, $function_name, $extra_param, $channel);
        }
    }

    /**
     * Log general error messages.
     */
    public function logError(string $controller_name, string $function_name, string $message, array $extra_param = [], $user = null, ?string $channel = null): void
    {
        try {
            $loggerMessage = "$controller_name: $function_name: $message";
            $dataArray = $this->getContext($extra_param, $user);

            Log::channel($channel)->error($loggerMessage, $dataArray);
        } catch (Throwable $th) {
            $this->logInternalError($th, $controller_name, $function_name, $extra_param, $channel);
        }
    }

    /**
     * Log informational messages.
     */
    public function logInfo(string $controller_name, string $function_name, string $message, array $extra_param = [], $user = null, ?string $channel = null): void
    {
        try {
            $loggerMessage = "$controller_name: $function_name: $message";
            $dataArray = $this->getContext($extra_param, $user);

            Log::channel($channel)->info($loggerMessage, $dataArray);
        } catch (Throwable $th) {
            $this->logInternalError($th, $controller_name, $function_name, $extra_param, $channel);
        }
    }

    /**
     * Internal method to log errors that occur within the logging service itself.
     */
    private function logInternalError(Throwable $th, string $controller_name, string $function_name, array $extra_param, ?string $channel): void
    {
        Log::error(static::class . ': ' . __FUNCTION__ . ': Throwable', [
            'Message' => $th->getMessage(),
            'TraceAsString' => $th->getTraceAsString(),
            'controller_name' => $controller_name,
            'function_name' => $function_name,
            'extra_param' => $this->sanitize($extra_param),
            'channel' => $channel,
        ]);
    }

    /**
     * Sanitize sensitive data from arrays.
     */
    private function sanitize(array $data): array
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'token',
            'otp',
            'secret',
            'authorization',
            'credit_card',
            'cvv'
        ];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->sanitize($value);
            } elseif (is_string($key) && in_array(strtolower($key), $sensitiveKeys)) {
                $data[$key] = '********';
            }
        }

        return $data;
    }

    private function getContext(array $extra_param, $user): array
    {
        return [
            "ExtraParam" => $this->sanitize($extra_param),
            "all_request" => $this->sanitize(request()->all()),
            "default_auth_detail" => $user, // User object might contain sensitive info, but usually it's model attributes. Ideally we'd pick only ID/email.
            "all_headers" => $this->sanitize(request()->headers->all()),
            "ip_address" => request()->ip(),
        ];
    }
}
