<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('training_books', function (Blueprint $table) {
            // Supprime d'abord les anciennes foreign keys
            $table->dropForeign(['training_id']);
            $table->dropForeign(['book_id']);

            // Recrée les foreign keys avec onDelete('cascade')
            $table->foreign('training_id')
                ->references('id')
                ->on('trainings')
                ->onDelete('cascade');

            $table->foreign('book_id')
                ->references('id')
                ->on('books')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('training_books', function (Blueprint $table) {
            // Supprime les foreign keys avec cascade
            $table->dropForeign(['training_id']);
            $table->dropForeign(['book_id']);

            // Recrée les foreign keys sans cascade
            $table->foreign('training_id')
                ->references('id')
                ->on('trainings');

            $table->foreign('book_id')
                ->references('id')
                ->on('books');
        });
    }
};