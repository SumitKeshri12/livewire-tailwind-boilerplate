<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_details', function (Blueprint $table) {
            $table->increments('id')->unique()->index()->comment("AUTO_INCREMENT");
            
                    $table->unsignedInteger('brand_id')->index()->nullable()->comment("Brands table ID")
;
                    $table->foreign('brand_id')->references('id')->on('brands');
            $table->string('description',500)->nullable();
            $table->char('status',1)->index()->nullable()->comment("Y => Active, N => Inactive");
            $table->string('brand_image',191)->nullable();
            $table->unsignedInteger('created_by')->nullable()->comment('');
            $table->unsignedInteger('updated_by')->nullable()->comment('');
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
        Schema::dropIfExists('brand_details');
    }
};
