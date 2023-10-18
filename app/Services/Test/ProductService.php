<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ProductService extends MainService
{
	public function list( array $filters = [] ) : \Illuminate\Pagination\LengthAwarePaginator
	{
		return Product::paginate( $filters );
	}

	public function store( array $productData ) : Product
	{
		return Product::create( $productData );
	}

	public function update( array $productData, int $productId ) : Product
	{
		$product = $this->fetch( $productId );
        $product->update( $productData );
        return $product;
	}

	public function delete( int $productId ) : Product
	{
		$product = $this->fetch( $productId );
        $product->delete();
        return $product;
	}

	public function fetch( int $productId ) : Product
	{
		return Product::find( $productId );
	}

}
