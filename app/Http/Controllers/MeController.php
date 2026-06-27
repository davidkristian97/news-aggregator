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

    /**
     * @OA\Get(
     *     path="/me/preferences",
     *     tags={"Me"},
     *     summary="Get the authenticated user's preferences",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User preferences",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Resource retrieved successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="sources", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="BBC News")
     *                 )),
     *                 @OA\Property(property="categories", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="authors", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated")
     * )
     */
    public function getPreferences(Request $request): SuccessResponse
    {
        $user = $this->service->get($request->user());
        return new SuccessResponse(new PreferencesResource($user));
    }

    /**
     * @OA\Put(
     *     path="/me/preferences",
     *     tags={"Me"},
     *     summary="Update the authenticated user's preferences",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="source_ids", type="array", @OA\Items(type="integer"), example={1,2}),
     *             @OA\Property(property="category_ids", type="array", @OA\Items(type="integer"), example={3}),
     *             @OA\Property(property="author_ids", type="array", @OA\Items(type="integer"), example={4,5})
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Preferences updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Preferences updated successfully.")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function updatePreferences(UpdatePreferencesRequest $request): SuccessResponse
    {
        $user = $this->service->update($request->user(), $request->preferences());
        return new SuccessResponse(new PreferencesResource($user), 'Preferences updated successfully.');
    }
}
