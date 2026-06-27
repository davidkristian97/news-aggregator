<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexArticlesRequest;
use App\Http\Resources\ArticleCollection;
use App\Http\Resources\ArticleResource;
use App\Http\Responses\PaginatedDataResponse;
use App\Http\Responses\SuccessResponse;
use App\Models\Article;
use App\Services\ArticleService;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $service) {}

    /**
     * @OA\Get(
     *     path="/articles",
     *     tags={"Articles"},
     *     summary="List articles with optional filters",
     *     description="Returns paginated articles. When authenticated, preferred sources/categories/authors are boosted to the top.",
     *     security={{}, {"bearerAuth":{}}},
     *     @OA\Parameter(name="source_ids[]", in="query", @OA\Schema(type="array", @OA\Items(type="integer")), example={}),
     *     @OA\Parameter(name="category_ids[]", in="query", @OA\Schema(type="array", @OA\Items(type="integer")), example={}),
     *     @OA\Parameter(name="author_ids[]", in="query", @OA\Schema(type="array", @OA\Items(type="integer")), example={}),
     *     @OA\Parameter(name="q", in="query", @OA\Schema(type="string"), example=""),
     *     @OA\Parameter(name="from", in="query", @OA\Schema(type="string", format="date"), example="2026-01-01"),
     *     @OA\Parameter(name="to", in="query", @OA\Schema(type="string", format="date"), example="2026-06-30"),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", default=20)),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated list of articles",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="pagination", type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="per_page", type="integer", example=20),
     *                 @OA\Property(property="total_pages", type="integer", example=5),
     *                 @OA\Property(property="total_items", type="integer", example=98),
     *                 @OA\Property(property="links", type="object",
     *                     @OA\Property(property="next", type="string", nullable=true),
     *                     @OA\Property(property="prev", type="string", nullable=true)
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(IndexArticlesRequest $request): PaginatedDataResponse
    {
        $paginator = $this->service->list($request->filters(), auth('sanctum')->user(), $request->perPage());
        return new PaginatedDataResponse(new ArticleCollection($paginator));
    }

    /**
     * @OA\Get(
     *     path="/articles/{id}",
     *     tags={"Articles"},
     *     summary="Get a single article",
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), example=1),
     *     @OA\Response(
     *         response=200,
     *         description="Article retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Resource retrieved successfully."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="title", type="string", example="Breaking News"),
     *                 @OA\Property(property="description", type="string"),
     *                 @OA\Property(property="url", type="string"),
     *                 @OA\Property(property="published_at", type="string", format="datetime"),
     *                 @OA\Property(property="source", type="object"),
     *                 @OA\Property(property="category", type="object", nullable=true),
     *                 @OA\Property(property="authors", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(response=404, description="Article not found")
     * )
     */
    public function show(Article $article): SuccessResponse
    {
        return new SuccessResponse(new ArticleResource($article));
    }
}
