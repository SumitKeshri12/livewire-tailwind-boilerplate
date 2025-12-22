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
        Schema::table('users', function (Blueprint $table) {
            // Drop existing columns that will be replaced
            $table->dropColumn(['name', 'email', 'password', 'status', 'profile_photo_path']);

            // Add new columns with the new schema
            $table->string('name', 191)->nullable()->after('id');
            $table->string('email', 191)->unique()->index()->nullable()->after('name');
            $table->string('password', 191)->nullable()->after('email');

            $table->unsignedInteger('role_id')->index()->nullable()->comment('Roles table ID')->after('password');
            $table->foreign('role_id')->references('id')->on('roles');

            $table->date('dob')->nullable()->after('role_id');
            $table->string('profile', 191)->nullable()->after('dob');

            $table->unsignedInteger('country_id')->nullable()->comment('Countries table ID')->after('profile');
            $table->foreign('country_id')->references('id')->on('countries');

            $table->unsignedInteger('state_id')->nullable()->comment('States table ID')->after('country_id');
            $table->foreign('state_id')->references('id')->on('states');

            $table->unsignedInteger('city_id')->nullable()->comment('Cities table ID')->after('state_id');
            $table->foreign('city_id')->references('id')->on('cities');

            $table->char('gender', 1)->nullable()->comment('F => Female, M => Male')->after('city_id');
            $table->char('status', 1)->index()->nullable()->default('N')->comment('Y => Active, N => Inactive')->after('gender');

            $table->string('description', 500)->nullable()->after('remember_token');
            $table->longText('hobbies')->nullable()->after('description');
            $table->longText('skills')->nullable()->after('hobbies');
            $table->longText('bg_color')->nullable()->after('skills');
            $table->string('timezone', 100)->nullable()->after('bg_color');
            $table->date('event_date')->nullable()->after('timezone');
            $table->dateTime('event_datetime')->nullable()->after('event_date');
            $table->time('event_time')->nullable()->after('event_datetime');
            $table->string('document', 255)->nullable()->after('event_time');
            $table->integer('age')->nullable()->after('document');
            $table->unsignedInteger('created_by')->nullable()->comment('')->after('age');
            $table->unsignedInteger('updated_by')->nullable()->comment('')->after('created_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['role_id']);
            $table->dropForeign(['country_id']);
            $table->dropForeign(['state_id']);
            $table->dropForeign(['city_id']);

            // Drop new columns
            $table->dropColumn([
                'name', 'email', 'password', 'role_id', 'dob', 'profile',
                'country_id', 'state_id', 'city_id', 'gender', 'status',
                'description', 'hobbies', 'skills', 'bg_color', 'timezone',
                'event_date', 'event_datetime', 'event_time', 'document',
                'age', 'created_by', 'updated_by',
            ]);

            // Restore original columns
            $table->string('name')->nullable();
            $table->string('email')->unique()->nullable();
            $table->string('password')->nullable();
            $table->char('status', 1)->default('Y');
            $table->string('profile_photo_path')->nullable();
        });
    }
};
