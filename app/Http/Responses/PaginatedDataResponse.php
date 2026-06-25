<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\ResourceCollection;

class PaginatedDataResponse extends JsonResponse
{
    public function __construct(ResourceCollection $collection)
    {
        $paginator = $collection->resource;

        parent::__construct([
            'success' => true,
            'data' => $collection->collection,
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total_pages' => $paginator->lastPage(),
                'total_items' => $paginator->total(),
                'links' => [
                    'next' => $paginator->nextPageUrl(),
                    'prev' => $paginator->previousPageUrl(),
                ],
            ],
        ]);
    }
}
