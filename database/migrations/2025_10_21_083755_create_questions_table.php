<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('questions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('q_id')->constrained('quizzes')->onDelete('cascade');
        $table->integer('q_no');
        $table->text('question');
        $table->string('a');
        $table->string('b');
        $table->string('c');
        $table->string('d');
        $table->string('correct_answer');
        $table->timestamps();
        
        $table->unique(['q_id', 'q_no']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
