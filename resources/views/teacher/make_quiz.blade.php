@extends('layouts.app')

@section('title', 'Create Quiz')

@section('navbar')
<div class="navbar">
    <a href="{{ route('teacher.dashboard') }}" class="navbar-brand">📚 Quiz System</a>
    <div class="navbar-menu">
        <a href="{{ route('teacher.dashboard') }}">Dashboard</a>
        <a href="{{ route('teacher.quizList') }}">My Quizzes</a>
        <a href="{{ route('teacher.logout') }}" class="logout-btn">Logout</a>
    </div>
</div>
@endsection

@section('content')
<style>
    .form-container {
        max-width: 700px;
        margin: 0 auto;
    }

    .form-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .form-header h1 {
        font-size: 32px;
        color: #333;
        margin-bottom: 10px;
    }

    .form-card {
        background: white;
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .form-help {
        font-size: 13px;
        color: #666;
        margin-top: 5px;
    }
</style>

<div class="form-container">
    <div class="form-header">
        <h1>📝 Create New Quiz</h1>
        <p>Fill in the details to create a new quiz for your students</p>
    </div>

    <div class="form-card">
        <form action="{{ route('teacher.storeQuiz') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="title">Quiz Title *</label>
                <input type="text" id="title" name="title" class="form-control" 
                       value="{{ old('title') }}" required 
                       placeholder="e.g., Mathematics Quiz Chapter 1">
                <div class="form-help">Give your quiz a descriptive title</div>
            </div>

            <div class="form-group">
                <label for="duration">Duration (in minutes) *</label>
                <input type="number" id="duration" name="duration" class="form-control" 
                       value="{{ old('duration', 30) }}" required min="1" max="300"
                       placeholder="30">
                <div class="form-help">How long should students have to complete this quiz?</div>
            </div>

            <div class="form-group">
                <label for="expire_time">Expiry Date & Time *</label>
                <input type="datetime-local" id="expire_time" name="expire_time" 
                       class="form-control" value="{{ old('expire_time') }}" required
                       min="{{ now()->format('Y-m-d\TH:i') }}">
                <div class="form-help">After this time, students won't be able to take the quiz</div>
            </div>

            <div style="display: flex; gap: 15px; margin-top: 30px;">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    Continue to Add Questions →
                </button>
                <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@section('scripts')
<script>
    // Set minimum datetime to current time
    document.addEventListener('DOMContentLoaded', function() {
        const now = new Date();
        now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
        document.getElementById('expire_time').min = now.toISOString().slice(0, 16);
    });
</script>
@endsection
@endsection