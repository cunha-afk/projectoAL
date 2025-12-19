<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_approved')
                  ->default(false)
                  ->after('email_verified_at');

            $table->string('phone', 20)
                  ->nullable()
                  ->after('is_approved');

            $table->timestamp('approved_at')
                  ->nullable()
                  ->after('phone');

            // se quiseres mesmo nif único (recomendado):
            $table->unique('nif');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nif']);
            $table->dropColumn([
                'approved_at',
                'phone',
                'is_approved',
            ]);
        });
    }
};
