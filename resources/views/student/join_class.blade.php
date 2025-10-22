@extends('layouts.app')

@section('title', 'Join Class')

@section('navbar')
<div class="navbar">
    <a href="{{ route('student.dashboard') }}" class="navbar-brand">📚 Quiz System</a>
    <div class="navbar-menu">
        <a href="{{ route('student.dashboard') }}">Dashboard</a>
        <a href="{{ route('student.myTeachers') }}">My Teachers</a>
        <a href="{{ route('student.logout') }}" class="logout-btn">Logout</a>
    </div>
</div>
@endsection

@section('content')
<style>
    .join-container {
        max-width: 600px;
        margin: 50px auto;
    }

    .join-card {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .join-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .join-header-icon {
        font-size: 64px;
        margin-bottom: 15px;
    }

    .join-header h2 {
        font-size: 28px;
        color: #333;
        margin-bottom: 10px;
    }

    .join-header p {
        color: #666;
    }

    .help-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 10px;
        margin-top: 30px;
    }

    .help-section h4 {
        color: #333;
        margin-bottom: 10px;
    }

    .help-section p {
        color: #666;
        font-size: 14px;
        line-height: 1.6;
    }
</style>

<div class="join-container">
    <div class="join-card">
        <div class="join-header">
            <div class="join-header-icon">🎓</div>
            <h2>Join a Teacher's Class</h2>
            <p>Enter the Teacher ID to send a join request</p>
        </div>

        <form action="{{ route('student.join_class') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="teacher_id">Teacher ID *</label>
                <input type="text" id="teacher_id" name="teacher_id" 
                       class="form-control" required 
                       placeholder="Enter teacher's ID (e.g., 1, 2, 3...)">
                <div class="form-help">
                    Ask your teacher for their ID to join their class
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 20px;">
                Send Join Request
            </button>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="{{ route('student.search_teachers') }}" class="btn btn-secondary">
                Or Browse All Teachers
            </a>
        </div>

        <div class="help-section">
            <h4>💡 How it works:</h4>
            <p>
                1. Get the Teacher ID from your teacher<br>
                2. Enter the ID and click "Send Join Request"<br>
                3. Wait for the teacher to approve your request<br>
                4. Once approved, you can take quizzes from that teacher
            </p>
        </div>
    </div>
</div>
@endsection