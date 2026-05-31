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
        Schema::table('berita_website', function (Blueprint $table) {
            $table->string('wp_post_id')->nullable()->change();
            $table->string('detail_url')->nullable()->change();
            $table->string('website_url')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('berita_website', function (Blueprint $table) {
            $table->string('wp_post_id')->nullable(false)->change();
            $table->string('detail_url')->nullable(false)->change();
            $table->string('website_url')->nullable(false)->change();
        });
    }
};
