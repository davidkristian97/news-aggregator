<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchFilterRequest;
use App\Http\Responses\SuccessResponse;
use App\Services\FilterService;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function __construct(private readonly FilterService $service) {}

    /**
     * @OA\Get(
     *     path="/filters/sources",
     *     tags={"Filters"},
     *     summary="Search sources by name",
     *     @OA\Parameter(name="q", in="query", required=false, @OA\Schema(type="string"), example="BBC"),
     *     @OA\Response(
     *         response=200,
     *         description="Matching sources",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="BBC News")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function sources(SearchFilterRequest $request): SuccessResponse
    {
        return new SuccessResponse($this->service->sources($request->searchTerm()));
    }

    /**
     * @OA\Get(
     *     path="/filters/categories",
     *     tags={"Filters"},
     *     summary="Get all categories",
     *     @OA\Response(
     *         response=200,
     *         description="All categories",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="Technology")
     *             ))
     *         )
     *     )
     * )
     */
    public function categories(Request $request): SuccessResponse
    {
        return new SuccessResponse($this->service->categories());
    }

    /**
     * @OA\Get(
     *     path="/filters/authors",
     *     tags={"Filters"},
     *     summary="Search authors by name",
     *     @OA\Parameter(name="q", in="query", required=false, @OA\Schema(type="string"), example="John"),
     *     @OA\Response(
     *         response=200,
     *         description="Matching authors",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe")
     *             ))
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function authors(SearchFilterRequest $request): SuccessResponse
    {
        return new SuccessResponse($this->service->authors($request->searchTerm()));
    }
}
