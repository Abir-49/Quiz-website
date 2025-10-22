@extends('layouts.app')

@section('title', 'Welcome - Quiz Management System')

@section('content')
<style>
    .welcome-hero {
        text-align: center;
        padding: 60px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        margin: -30px -30px 30px -30px;
        border-radius: 15px 15px 0 0;
    }

    .welcome-hero h1 {
        font-size: 48px;
        margin-bottom: 20px;
    }

    .welcome-hero p {
        font-size: 20px;
        opacity: 0.9;
    }

    .login-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 40px;
    }

    .login-card {
        background: white;
        border-radius: 15px;
        padding: 40px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .login-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    }

    .login-card-icon {
        font-size: 64px;
        margin-bottom: 20px;
    }

    .login-card h2 {
        font-size: 28px;
        margin-bottom: 15px;
        color: #333;
    }

    .login-card p {
        color: #666;
        margin-bottom: 25px;
    }

    .login-card .btn {
        width: 100%;
        margin-bottom: 10px;
    }
</style>

<div class="welcome-hero">
    <h1>🎓 Quiz Management System</h1>
    <p>A comprehensive platform for teachers and students</p>
</div>

<div class="login-cards">
    <div class="login-card">
        <div class="login-card-icon">👨‍🏫</div>
        <h2>Teacher Portal</h2>
        <p>Create quizzes, manage students, and track performance</p>
        <a href="{{ route('teacher.login') }}" class="btn btn-primary">Teacher Login</a>
        <a href="{{ route('teacher.register') }}" class="btn btn-secondary">Teacher Signup</a>
    </div>

    <div class="login-card">
        <div class="login-card-icon">👨‍🎓</div>
        <h2>Student Portal</h2>
        <p>Take quizzes, view results, and track your progress</p>
        <a href="{{ route('student.login') }}" class="btn btn-primary">Student Login</a>
        <a href="{{ route('student.register') }}" class="btn btn-secondary">Student Signup</a>
    </div>
</div>

<div class="card mt-20">
    <h3 class="text-center">Features</h3>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
        <div style="text-align: center; padding: 20px;">
            <div style="font-size: 36px; margin-bottom: 10px;">📝</div>
            <h4>Create Quizzes</h4>
            <p>Teachers can create custom quizzes with MCQ and short answer questions</p>
        </div>
        <div style="text-align: center; padding: 20px;">
            <div style="font-size: 36px; margin-bottom: 10px;">📊</div>
            <h4>Track Performance</h4>
            <p>Monitor student progress and analyze quiz results</p>
        </div>
        <div style="text-align: center; padding: 20px;">
            <div style="font-size: 36px; margin-bottom: 10px;">👥</div>
            <h4>Class Management</h4>
            <p>Manage student enrollments and classroom activities</p>
        </div>
        <div style="text-align: center; padding: 20px;">
            <div style="font-size: 36px; margin-bottom: 10px;">⏱️</div>
            <h4>Timed Quizzes</h4>
            <p>Set durations and deadlines for quizzes</p>
        </div>
    </div>
</div>
@endsection