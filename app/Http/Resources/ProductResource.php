<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'code' => $this->code,
            'category' => new CategoryResource($this->category),
            'stock' => $this->stock->stock,
            'images'   => $this->productImages->map(function ($image) {
                return  $image->path;
            }),
        ];
    }
}
