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

    public function index(IndexArticlesRequest $request): PaginatedDataResponse
    {
        $paginator = $this->service->list($request->filters(), $request->perPage());
        return new PaginatedDataResponse(new ArticleCollection($paginator));
    }

    public function show(Article $article): SuccessResponse
    {
        return new SuccessResponse(new ArticleResource($article));
    }
}
