<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const ADMIN_USERS_TABLE = 'admin_users';
    private const TABLE_USERS = 'users';
    private const TABLE_PASSWORD_RESETS = 'password_resets';
    private const TABLE_PAPERS = 'papers';

    public function up(): void
    {
        Schema::create(self::ADMIN_USERS_TABLE, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create(self::TABLE_USERS, function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create(self::TABLE_PASSWORD_RESETS, function (Blueprint $table) {
            $table->string('email')->index();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create(self::TABLE_PAPERS, function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->string('title');
            $table->string('body');
            $table->timestamps();

            $table->foreign('user_id')
                ->references('id')
                ->on(self::TABLE_USERS)
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists(self::ADMIN_USERS_TABLE);
        Schema::dropIfExists(self::TABLE_USERS);
        Schema::dropIfExists(self::TABLE_PASSWORD_RESETS);
        Schema::dropIfExists(self::TABLE_PAPERS);
    }
};
