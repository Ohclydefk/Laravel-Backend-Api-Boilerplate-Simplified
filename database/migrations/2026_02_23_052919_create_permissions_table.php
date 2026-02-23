<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permissions', function (Blueprint $table) {
            $table->id();

            /**
             * Unique permission name
             * Example:
             * - products.view
             * - products.create
             * - products.update
             * - products.delete
             */
            $table->string('name')->unique();

            /**
             * Optional readable label
             * Example:
             * - View Products
             */
            $table->string('label')->nullable();

            /**
             * Optional grouping field
             * Example:
             * - products
             * - users
             */
            $table->string('group')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permissions');
    }
};