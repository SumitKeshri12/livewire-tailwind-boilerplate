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
        Schema::create('brands', function (Blueprint $table) {
            $table->increments('id')->unique()->index()->comment("AUTO_INCREMENT");
            
                    $table->unsignedInteger('country_id')->nullable()->comment("Countries table ID")
;
                    $table->foreign('country_id')->references('id')->on('countries');
            $table->char('status',1)->index()->nullable()->default("N")->comment("Y => Active, N => Inactive");
            $table->dateTime('bob')->nullable();
            $table->date('start_date')->nullable();
            $table->time('start_time')->nullable();
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
        Schema::dropIfExists('brands');
    }
};
