<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchFilterRequest;
use App\Http\Responses\SuccessResponse;
use App\Services\FilterService;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function __construct(private readonly FilterService $service) {}

    public function sources(SearchFilterRequest $request): SuccessResponse
    {
        return new SuccessResponse($this->service->sources($request->searchTerm()));
    }

    public function categories(Request $request): SuccessResponse
    {
        return new SuccessResponse($this->service->categories());
    }

    public function authors(SearchFilterRequest $request): SuccessResponse
    {
        return new SuccessResponse($this->service->authors($request->searchTerm()));
    }
}
