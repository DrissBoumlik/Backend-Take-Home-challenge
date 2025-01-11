<?php

namespace Domain\User\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'sources' => $this->sources,
            'categories' => $this->categories,
            'authors' => $this->authors,
        ];
    }
}
