<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('import_csv_logs', function (Blueprint $table) {
            $table->id()->comment('AUTO_INCREMENT');
            $table->string('file_name')->nullable()->comment('Original filename of the imported file');
            $table->string('file_path')->nullable()->comment('Storage path of the imported file');
            $table->string('model_name')->nullable()->comment('Model being imported (users, roles, etc.)');
            $table->unsignedBigInteger('user_id')->nullable()->comment('User table id');
            $table->char('status', 1)->nullable()->comment('Y = Success, N = Fail, P = Pending, S = Processing');
            $table->char('import_flag', 1)->nullable()->comment('P = Pending, Y = Success');
            $table->string('voucher_email', 191)->nullable()->comment('Email for voucher notifications');
            $table->string('redirect_link', 191)->nullable()->comment('Redirect link after import completion');
            $table->unsignedInteger('no_of_rows')->nullable()->comment('No of csv rows');
            $table->longText('error_log')->nullable()->comment('Detailed error log for failed imports');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable()->comment('User who initiated the import');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('User who last updated the import');
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('User who deleted the import');

            // Foreign key constraints
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('deleted_by')->references('id')->on('users')->onDelete('set null');

            // Indexes
            $table->index('id', 'import_csv_logs_id_index');
            $table->index('user_id', 'import_csv_logs_user_id_index');
            $table->index('import_flag', 'import_csv_logs_import_flag_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_csv_logs');
    }
};
