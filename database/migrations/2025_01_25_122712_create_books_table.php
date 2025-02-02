<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBooksTable extends Migration
{
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->string('author')->nullable();
            $table->string('cover_url')->nullable();
            $table->string('google_api_page')->nullable();
            $table->string('isbn')->nullable();
            $table->boolean('is_borrowed')->default(false);
            $table->boolean('open_library_parsed')->default(false);
            $table->string('original_filename')->nullable();
            $table->foreignId('owner_id')->constrained('users');
            $table->integer('pages')->nullable();
            $table->date('published_at')->nullable();
            $table->string('publisher')->nullable();
            $table->integer('quantity')->default(1);
            $table->foreignId('support_id')->constrained('supports');
            $table->foreignId('theme_id')->constrained('themes');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('books');
    }
};