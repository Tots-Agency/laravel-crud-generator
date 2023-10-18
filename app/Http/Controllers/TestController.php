<?php

namespace App\Http\Controllers;

use App\Http\Requests\Test\ListRequest;
use App\Http\Requests\Test\StoreRequest;
use App\Http\Requests\Test\UpdateRequest;
use App\Http\Resources\Test\TestCollection;
use App\Http\Resources\Test\TestResource;
use App\Services\TestService;

class TestController extends Controller
{
	public function __construct( private TestService $testService )
	{
		
	}

	public function list( ListRequest $request ) : \Illuminate\Http\JsonResponse
	{
		$tests = $this->testService->list( $request->validated() );
        return response()->json( [
            'data' => new TestCollection( $tests ),
        ] );
	}

	public function show( int $testId ) : \Illuminate\Http\JsonResponse
	{
		$test = $this->testService->fetch( $testId );
        return $test ?

        response()->json( [
            'data' => TestResource::make( $test ),
        ] ):

        response()->json( [
            'error' => 'Test not found',
        ], 404 );
	}

	public function store( StoreRequest $request ) : \Illuminate\Http\JsonResponse
	{
		$test = $this->testService->store( $request->validated() );
        return response()->json( [
            'data' => TestResource::make( $test ),
        ], 201 );
	}

	public function update( UpdateRequest $request, int $testId ) : \Illuminate\Http\JsonResponse
	{
		$test = $this->testService->update( $request->validated(), $testId );
        return response()->json( [
            'data' => TestResource::make( $test ),
        ] );
	}

	public function delete( int $testId ) : \Illuminate\Http\JsonResponse
	{
		$test = $this->testService->delete( $testId );
        return $test ?

        response()->json( [
            'data' => TestResource::make( $test ),
        ] ):

        response()->json( [
            'error' => 'Test not found',
        ], 404 );
	}

}
