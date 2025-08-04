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
        Schema::create('ocurrences', function (Blueprint $table) {
            $table->id();
            $table->magellanPoint('location', 4326);
            $table->unsignedBigInteger('type_id');
            $table->unsignedBigInteger('user_id');
            $table->text('description');
            $table->text('address_name');
            $table->text('city')->nullable();
            $table->text('country')->nullable();
            $table->text('state')->nullable();
            $table->boolean('is_active')->default(1);
            $table->foreign('type_id')->references('id')->on('types_ocurrence')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ocurrences');
    }
};
