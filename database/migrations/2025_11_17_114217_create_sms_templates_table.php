<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sms_templates', function (Blueprint $table) {
            $table->increments('id')->unique()->index()->comment("AUTO_INCREMENT");
            $table->string('template_name',100)->nullable()->comment("Template Name");
            $table->longText('message')->nullable()->comment("Message");
            $table->string('template_id',200)->nullable()->comment("DLT Template ID");
            $table->char('type',3)->index()->nullable()->comment("OTP => OTP Verification");
            $table->char('status',1)->index()->nullable()->comment("Y =>  Active, N => Inactive");
            $table->unsignedInteger('created_by')->nullable()->comment('User table ID');
            $table->unsignedInteger('updated_by')->nullable()->comment('User table ID');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sms_templates');
    }
};
