<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WishlistResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $product = $this->product;
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'name' => $product?->name,
            'product_id' => $product?->id,
            'description' => $product?->description,
            'price' => $product?->price,
            'created_at' => $product?->created_at,
            'updated_at' => $product?->updated_at,
            'deleted_at' => $product?->deleted_at,
        ];
    }
}
