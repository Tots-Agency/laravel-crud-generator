<?php

namespace App\Http\Controllers;

use App\Http\Requests\Product\ListRequest;
use App\Http\Requests\Product\StoreRequest;
use App\Http\Requests\Product\Test1Request;
use App\Http\Requests\Product\Test2Request;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
	public function __construct( private ProductService $productService )
	{
		
	}

	public function list( ListRequest $request ) : \Illuminate\Http\JsonResponse
	{
		$products = $this->productService->list( $request->validated() );
        return response()->json( [
            'data' => new ProductCollection( $products ),
        ] );
	}

	public function store( StoreRequest $request ) : \Illuminate\Http\JsonResponse
	{
		$product = $this->productService->store( $request->validated() );
        return response()->json( [
            'data' => ProductResource::make( $product ),
        ], 201 );
	}

	public function delete( int $productId ) : \Illuminate\Http\JsonResponse
	{
		$product = $this->productService->delete( $productId );
        return $product ?

        response()->json( [
            'data' => ProductResource::make( $product ),
        ] ):

        response()->json( [
            'error' => 'Product not found',
        ], 404 );
	}

	public function test1( Test1Request $request ) : \Illuminate\Http\JsonResponse
	{
		// TO DO
        return response()->json( [
            'data' => []
        ] );
	}

	public function test2( Test2Request $request ) : \Illuminate\Http\JsonResponse
	{
		// TO DO
        return response()->json( [
            'data' => []
        ] );
	}

}
