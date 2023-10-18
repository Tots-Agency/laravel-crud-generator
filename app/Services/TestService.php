<?php

namespace App\Services;

use App\Models\Test;

class TestService
{
	public function list( array $filters = [] ) : \Illuminate\Pagination\LengthAwarePaginator
	{
		return Test::paginate( $filters );
	}

	public function fetch( int $testId ) : Test
	{
		return Test::find( $testId );
	}

	public function store( array $testData ) : Test
	{
		return Test::create( $testData );
	}

	public function update( array $testData, int $testId ) : Test
	{
		$test = $this->fetch( $testId );
        $test->update( $testData );
        return $test;
	}

	public function delete( int $testId ) : Test
	{
		$test = $this->fetch( $testId );
        $test->delete();
        return $test;
	}

}
