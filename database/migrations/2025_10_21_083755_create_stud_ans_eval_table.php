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
    Schema::create('stud_ans_evals', function (Blueprint $table) {
        $table->foreignId('s_id')->constrained('students')->onDelete('cascade');
        $table->unsignedBigInteger('q_id');
        $table->integer('q_no');
        $table->string('ans')->nullable();
        $table->boolean('evaluation')->nullable(); // 1 for correct, 0 for wrong
        $table->timestamps();
        
        $table->primary(['s_id', 'q_id', 'q_no']);
        
        $table->foreign(['q_id', 'q_no'])
              ->references(['q_id', 'q_no'])
              ->on('questions')
              ->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stud_ans_eval');
    }
};
