<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PreferencesResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'sources' => $this->sources->map(fn ($s) => [
                'id' => $s->id,
                'name' => $s->name,
            ]),
            'categories' => $this->categories->map(fn ($c) => [
                'id' => $c->id,
                'name' => $c->name,
            ]),
            'authors' => $this->authors->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->name,
            ]),
        ];
    }
}
