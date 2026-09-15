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
        Schema::table('game_invitations', function (Blueprint $table) {
            //
            $table->unique(['game_id', 'invitedUser_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('game_invitations', function (Blueprint $table) {
            //
            $table->dropUnique(['game_id', 'invitedUser_id']);
        });
    }
};
