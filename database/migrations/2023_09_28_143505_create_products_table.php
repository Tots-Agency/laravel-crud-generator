<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'products', function (Blueprint $table) {
			$table->id( 'idproduct' );
			$table->nullableMorphs( 'imageable' );
			$table->nullableMorphs( 'testeable' );
			$table->string( 'name' )->unique()->default( 'Default Test Product' );
			$table->text( 'description' )->nullable();
			$table->bigInteger( 'category_id' );
			$table->timestamps();
			$table->softDeletes();

			// BelongsTo
			$table->foreign( 'brand_id' )->references( 'id' )->on( 'brands' );
			$table->foreign( 'category_id' )->references( 'idcategory' )->on( 'product_categories' );
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'products' );
    }
}
