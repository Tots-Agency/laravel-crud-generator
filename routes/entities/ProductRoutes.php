<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::group( [ 'prefix' => 'products', 'middleware' => [ 'auth:api' ] ], function()
{
    Route::get( '/', [ ProductController::class, 'list' ] )->name( 'product.list' )->middleware( [ 'permission:product.list' ] );
	Route::post( '/', [ ProductController::class, 'store' ] )->name( 'product.store' )->middleware( [ 'permission:product.store' ] );
	Route::delete( '/{id}', [ ProductController::class, 'delete' ] )->name( 'product.delete' )->middleware( [ 'permission:product.delete' ] );
	Route::post( '/test1', [ ProductController::class, 'test1' ] )->name( 'product.test1' )->middleware( [ 'permission:product.test1' ] );
	Route::post( '/test2', [ ProductController::class, 'test2' ] )->name( 'product.test2' )->middleware( [ 'permission:product.test2' ] );
});
