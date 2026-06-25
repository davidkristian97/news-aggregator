<?php

namespace App\Http\Controllers;

use App\Http\Requests\IndexArticlesRequest;
use App\Http\Resources\ArticleCollection;
use App\Services\ArticleService;

class ArticleController extends Controller
{
    public function __construct(private readonly ArticleService $service) {}

    public function index(IndexArticlesRequest $request): ArticleCollection
    {
        $paginator = $this->service->list($request->filters(), $request->perPage());
        return new ArticleCollection($paginator);
    }
}