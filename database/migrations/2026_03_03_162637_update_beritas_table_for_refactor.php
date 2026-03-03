<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('beritas', function (Blueprint $table) {
            $table->dropForeign(['website_id']);
            $table->dropColumn(['website_id', 'website_url', 'detail_url']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beritas', function (Blueprint $table) {
            $table->foreignId('website_id')->nullable()->constrained('websites')->onDelete('cascade');
            $table->string('website_url')->nullable();
            $table->string('detail_url')->nullable();
        });
    }
};
