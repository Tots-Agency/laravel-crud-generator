<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductProductTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create( 'product_product_tag', function (Blueprint $table) {
			$table->id();
			$table->bigInteger( 'product_id' );
			$table->bigInteger( 'tagid' );
			$table->timestamps();
			$table->softDeletes();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists( 'product_product_tag' );
    }
}
