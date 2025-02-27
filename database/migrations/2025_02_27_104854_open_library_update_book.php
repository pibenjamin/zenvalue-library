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
        // remove  open_library_parsed 
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('open_library_parsed');
        });

        // add ol_key
        Schema::table('books', function (Blueprint $table) {
            $table->string('ol_key')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // add open_library_parsed
        Schema::table('books', function (Blueprint $table) {
            $table->boolean('open_library_parsed')->default(false);
            });

        // remove ol_key
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('ol_key');
        });
    }
};
