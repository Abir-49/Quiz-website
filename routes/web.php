<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;



// Teacher
Route::get('/teacher/login', [TeacherController::class,'showLogin'])->name('teacher.login');
Route::post('/teacher/login', [TeacherController::class,'login']);
Route::get('/teacher/register', [TeacherController::class,'showRegister'])->name('teacher.register');
Route::post('/teacher/register', [TeacherController::class,'register']);

// Student
Route::get('/student/login', [StudentController::class,'showLogin'])->name('student.login');
Route::post('/student/login', [StudentController::class,'login']);
Route::get('/student/register', [StudentController::class,'showRegister'])->name('student.register');
Route::post('/student/register', [StudentController::class,'register']);


// Teacher Middleware 

Route::middleware(['teacher.auth'])->group(function() {
    Route::get('/teacher/dashboard', [TeacherController::class,'dashboard'])->name('teacher.dashboard');
    Route::get('/teacher/logout', [TeacherController::class,'logout'])->name('teacher.logout');

    // Make quiz
    Route::get('/teacher/make-quiz', [TeacherController::class,'showMakeQuiz'])->name('teacher.make_quiz');
    Route::post('/teacher/make-quiz', [TeacherController::class,'storeQuiz']);

    // View quiz results
    Route::get('/teacher/quiz-results/{quiz_id}', [TeacherController::class,'viewQuizResults'])->name('teacher.quiz_results');

    // Approve/reject student requests
    Route::get('/teacher/request/approve/{id}', [TeacherController::class,'approveRequest'])->name('teacher.request.approve');
    Route::get('/teacher/request/reject/{id}', [TeacherController::class,'rejectRequest'])->name('teacher.request.reject');

    // View student answers
    Route::get('/teacher/quiz/{quiz_id}/student/{student_id}/answers', [TeacherController::class,'viewStudentAnswers'])->name('teacher.student_answers');
});


// Student Middleware 

Route::middleware(['student.auth'])->group(function() {
    Route::get('/student/dashboard', [StudentController::class,'dashboard'])->name('student.dashboard');
    Route::get('/student/logout', [StudentController::class,'logout'])->name('student.logout');

    // Join teacher class
    Route::get('/student/join/{teacher_id}', [StudentController::class,'requestJoin'])->name('student.join_class');

    // Take quiz
    Route::get('/student/quiz/{quiz_id}/take', [StudentController::class,'takeQuiz'])->name('student.take_quiz');
    Route::post('/student/quiz/{quiz_id}/submit', [StudentController::class,'submitQuiz'])->name('student.submit_quiz');

    // View quiz result
    Route::get('/student/quiz/{quiz_id}/result', [StudentController::class,'viewQuizResult'])->name('student.quiz_result');
});


// Home

Route::get('/', function() {
    return view('welcome');
})->name('home');
