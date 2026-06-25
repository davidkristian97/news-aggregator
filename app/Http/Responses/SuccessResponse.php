<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class SuccessResponse extends JsonResponse
{
    public function __construct(mixed $data, string $message = 'Resource retrieved successfully.', int $status = 200)
    {
        parent::__construct([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }
}
