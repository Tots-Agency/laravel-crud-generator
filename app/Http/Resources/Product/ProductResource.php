<?php

namespace App\Http\Resources\Product;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray( $request )
    {
        return [
			'id' => $this->resource->id,
			'name' => $this->resource->name,
			'description' => $this->resource->description,
			'category_id' => $this->resource->category_id,
		];
    }
}
