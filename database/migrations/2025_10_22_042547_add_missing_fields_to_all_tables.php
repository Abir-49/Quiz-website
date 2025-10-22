<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Add fields to classes table
        Schema::table('classes', function (Blueprint $table) {
            $table->timestamp('requested_at')->nullable()->after('status');
            $table->timestamp('responded_at')->nullable()->after('requested_at');
        });

        // Add fields to questions table
        Schema::table('questions', function (Blueprint $table) {
            $table->enum('type', ['mcq', 'short'])->default('mcq')->after('question');
            $table->integer('marks')->default(1)->after('correct_answer');
        });
        
        // Make columns nullable for short answer questions
        Schema::table('questions', function (Blueprint $table) {
            $table->string('a')->nullable()->change();
            $table->string('b')->nullable()->change();
            $table->string('c')->nullable()->change();
            $table->string('d')->nullable()->change();
            $table->text('correct_answer')->change();
        });

        // Add fields to results table
        Schema::table('results', function (Blueprint $table) {
            $table->integer('total_marks')->default(0)->after('score');
            $table->decimal('percentage', 5, 2)->default(0)->after('total_marks');
            $table->timestamp('submitted_at')->nullable()->after('percentage');
        });

        // Add fields to stud_ans_evals table
        Schema::table('stud_ans_evals', function (Blueprint $table) {
            $table->integer('marks_obtained')->default(0)->after('evaluation');
        });
        
        // Make ans column text type for short answers
        Schema::table('stud_ans_evals', function (Blueprint $table) {
            $table->text('ans')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('classes', function (Blueprint $table) {
            $table->dropColumn(['requested_at', 'responded_at']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn(['type', 'marks']);
        });

        Schema::table('results', function (Blueprint $table) {
            $table->dropColumn(['total_marks', 'percentage', 'submitted_at']);
        });

        Schema::table('stud_ans_evals', function (Blueprint $table) {
            $table->dropColumn('marks_obtained');
        });
    }
};