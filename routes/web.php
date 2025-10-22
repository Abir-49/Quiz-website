<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\StudentController;
use App\Models\Student;

// ============ HOME ============
Route::get('/', function() {
    return view('welcome');
})->name('home');

// ============ TEACHER ROUTES ============
Route::prefix('teacher')->name('teacher.')->group(function() {
    
    // Guest routes
    Route::middleware('guest')->group(function() {
        Route::get('/login', [TeacherController::class, 'showLogin'])->name('login');
        Route::post('/login', [TeacherController::class, 'login']);
        Route::get('/register', [TeacherController::class, 'showRegister'])->name('register');
        Route::post('/register', [TeacherController::class, 'register']);
    });

    // Authenticated routes
    Route::middleware(['teacher.auth'])->group(function() {
        Route::get('/dashboard', [TeacherController::class, 'dashboard'])->name('dashboard');
        Route::get('/logout', [TeacherController::class, 'logout'])->name('logout');

        // Quiz Management
        Route::get('/make-quiz', [TeacherController::class, 'showMakeQuiz'])->name('makeQuiz');
        Route::post('/store-quiz', [TeacherController::class, 'storeQuiz'])->name('storeQuiz');
        Route::get('/quiz/{quiz}/add-questions', [TeacherController::class, 'showAddQuestions'])->name('addQuestions');
        Route::post('/quiz/{quiz}/store-questions', [TeacherController::class, 'storeQuestions'])->name('storeQuestions');
        Route::delete('/quiz/{quiz}/question/{question}', [TeacherController::class, 'deleteQuestion'])->name('deleteQuestion');
        Route::get('/quizzes', [TeacherController::class, 'viewQuizList'])->name('quizList');
        Route::delete('/quiz/{quiz}', [TeacherController::class, 'deleteQuiz'])->name('deleteQuiz');

        // Quiz Results
        Route::get('/quiz-results/{quiz}', [TeacherController::class, 'viewQuizResults'])->name('quiz_results');
        Route::get('/quiz/{quiz}/download-results', [TeacherController::class, 'downloadResults'])->name('download_results_teach');
        Route::get('/quiz/{quiz}/student/{student}/answers', [TeacherController::class, 'viewStudentAnswers'])->name('student_answers');

        // Class Management
        Route::get('/request/approve/{id}', [TeacherController::class, 'approveRequest'])->name('request.approve');
        Route::get('/request/reject/{id}', [TeacherController::class, 'rejectRequest'])->name('request.reject');
        Route::get('/students', [TeacherController::class, 'viewStudents'])->name('students');
        Route::delete('/student/{student}/remove', [TeacherController::class, 'removeStudent'])->name('removeStudent');
    });
});

// ============ STUDENT ROUTES ============
Route::prefix('student')->name('student.')->group(function() {
    
    // Guest routes
    Route::middleware('guest')->group(function() {
        Route::get('/login', [StudentController::class, 'showLogin'])->name('login');
        Route::post('/login', [StudentController::class, 'login']);
        Route::get('/register', [StudentController::class, 'showRegister'])->name('register');
        Route::post('/register', [StudentController::class, 'register']);
    });

    // Authenticated routes
    Route::middleware(['student.auth'])->group(function() {
        Route::get('/dashboard', [StudentController::class, 'dashboard'])->name('dashboard');
        Route::get('/logout', [StudentController::class, 'logout'])->name('logout');
        Route::get('/class_result/{quiz}/{t_id}', [StudentController::class, 'showQuizResults'])->name('class_result');
        Route::get('/quiz/{quiz}/{t_id}/download-results', [StudentController::class, 'downloadResults'])->name('download_results_stud');

        // Class Management
        Route::get('/join-class', [StudentController::class, 'showJoinClass'])->name('join_class_form');
        Route::get('/search-teachers', [StudentController::class, 'searchTeachers'])->name('search_teachers');
        Route::post('/join', [StudentController::class, 'requestJoin'])->name('join_class');
        Route::delete('/cancel-request/{teacher}', [StudentController::class, 'cancelRequest'])->name('cancelRequest');
        Route::delete('/leave-class/{teacher}', [StudentController::class, 'leaveClass'])->name('leaveClass');
        Route::get('/my-teachers', [StudentController::class, 'myTeachers'])->name('myTeachers');

        // Quiz Taking
        Route::get('/quiz/{quiz}/take', [StudentController::class, 'takeQuiz'])->name('take_quiz');
        Route::post('/quiz/{quiz}/submit', [StudentController::class, 'submitQuiz'])->name('submit_quiz');
        Route::get('/quiz/{quiz}/result', [StudentController::class, 'viewQuizResult'])->name('quiz_result');
        Route::get('/my-results', [StudentController::class, 'myResults'])->name('myResults');
    });
});