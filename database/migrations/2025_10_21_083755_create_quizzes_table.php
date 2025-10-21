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
    Schema::create('quizzes', function (Blueprint $table) {
        $table->id();
        $table->foreignId('t_id')->constrained('teachers')->onDelete('cascade');
        $table->string('title');
        $table->integer('duration'); // in minutes
        $table->timestamp('creation_time')->useCurrent();
        $table->timestamp('expire_time')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quizzes');
    }
};
