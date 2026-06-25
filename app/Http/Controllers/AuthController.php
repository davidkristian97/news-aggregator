<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\SuccessResponse;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private readonly AuthService $service) {}

    public function register(RegisterRequest $request): SuccessResponse
    {
        $result = $this->service->register($request->credentials());

        return new SuccessResponse([
            'token' => $result['token'],
            'user'  => new UserResource($result['user']),
        ], 'Registered successfully.', 201);
    }

    public function login(LoginRequest $request): SuccessResponse
    {
        $result = $this->service->login($request->credentials());

        return new SuccessResponse([
            'token' => $result['token'],
            'user'  => new UserResource($result['user']),
        ], 'Login successful.');
    }

    public function logout(Request $request): SuccessResponse
    {
        $this->service->logout($request->user());

        return new SuccessResponse(null, 'Logged out successfully.');
    }
}
