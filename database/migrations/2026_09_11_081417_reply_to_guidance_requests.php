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
        Schema::table('guidance_requests', function (Blueprint $table) {
            // add is replied 
            $table->boolean('is_replied')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guidance_requests', function (Blueprint $table) {
            //
            $table->dropColumn('is_replied');
        });
    }
};
