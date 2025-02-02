<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthorBooksTable extends Migration
{
    public function up()
    {
        Schema::create('author_books', function (Blueprint $table) {
            $table->foreignId('author_id')->constrained()->onDelete('cascade');
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->primary(['author_id', 'book_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('author_books');
    }
};