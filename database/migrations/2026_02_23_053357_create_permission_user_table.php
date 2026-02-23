<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('permission_user', function (Blueprint $table) {
            $table->id();

            /**
             * Links a permission to a user
             * - One user can have many permissions
             * - One permission can belong to many users
             */
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('permission_id')
                ->constrained()
                ->cascadeOnDelete();

            /**
             * Prevent duplicates:
             * user_id = 1 + permission_id = 3 should only exist once
             */
            $table->unique(['user_id', 'permission_id']);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('permission_user');
    }
};
