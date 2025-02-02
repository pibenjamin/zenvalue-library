<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained()->onDelete('cascade');
            $table->foreignId('borrower_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('borrowed_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('to_be_returned_at')->nullable();
            $table->timestamp('return_signaled_at')->nullable();
            $table->foreignId('return_confirmed_by')->nullable()->constrained('users');
            $table->string('return_confirmation_token')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loans');
    }
} 