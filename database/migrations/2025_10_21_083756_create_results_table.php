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
    Schema::create('results', function (Blueprint $table) {
        $table->id();
        $table->foreignId('s_id')->constrained('students')->onDelete('cascade');
        $table->foreignId('q_id')->constrained('quizzes')->onDelete('cascade');
        $table->integer('score');
        $table->timestamps();
        
        $table->unique(['s_id', 'q_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('results');
    }
};
