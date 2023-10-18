<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

Route::group( [ 'prefix' => 'tests', 'middleware' => [ 'auth:api' ] ], function()
{
    Route::get( '/', [ TestController::class, 'list' ] )->name( 'test.list' )->middleware( [ 'permission:test.list' ] );
	Route::get( '/{id}', [ TestController::class, 'show' ] )->name( 'test.show' )->middleware( [ 'permission:test.show' ] );
	Route::post( '/', [ TestController::class, 'store' ] )->name( 'test.store' )->middleware( [ 'permission:test.store' ] );
	Route::patch( '/{id}', [ TestController::class, 'update' ] )->name( 'test.update' )->middleware( [ 'permission:test.update' ] );
	Route::delete( '/{id}', [ TestController::class, 'delete' ] )->name( 'test.delete' )->middleware( [ 'permission:test.delete' ] );
});
