<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePreferencesRequest;
use App\Http\Resources\PreferencesResource;
use App\Http\Responses\SuccessResponse;
use App\Services\PreferencesService;
use Illuminate\Http\Request;

class MeController extends Controller
{
    public function __construct(private readonly PreferencesService $service) {}

    public function getPreferences(Request $request): SuccessResponse
    {
        $user = $this->service->get($request->user());
        return new SuccessResponse(new PreferencesResource($user));
    }

    public function updatePreferences(UpdatePreferencesRequest $request): SuccessResponse
    {
        $user = $this->service->update($request->user(), $request->preferences());
        return new SuccessResponse(new PreferencesResource($user), 'Preferences updated successfully.');
    }
}
