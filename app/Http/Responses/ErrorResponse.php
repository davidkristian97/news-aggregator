<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class ErrorResponse extends JsonResponse
{
    public function __construct(string $message, int $status, array $details = [])
    {
        parent::__construct([
            'success' => false,
            'error' => [
                'message' => $message,
                'details' => $details,
            ],
        ], $status);
    }
}
