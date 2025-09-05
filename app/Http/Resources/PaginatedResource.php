<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PaginatedResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function __construct($resource, public $resourceClass = null)
    {
        parent::__construct($resource);
    }

    public function collect($resource)
    {
        return $this->resourceClass::collection($resource);
    }

    public function toArray($request): array
    {
        return [
            'data' => $this->collect($this->items()),
            'meta' => [
                'currentPage' => $this->currentPage(),
                'from' => $this->firstItem(),
                'lastPage' => $this->lastPage(),
                'path' => $this->path(),
                'perPage' => $this->perPage(),
                'to' => $this->lastItem(),
                'total' => $this->total(),
            ]

        ];
    }
}
