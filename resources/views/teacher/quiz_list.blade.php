@extends('layouts.app')

@section('title', 'My Quizzes')

@section('navbar')
<div class="navbar">
    <a href="{{ route('teacher.dashboard') }}" class="navbar-brand">📚 Quiz System</a>
    <div class="navbar-menu">
        <a href="{{ route('teacher.dashboard') }}">Dashboard</a>
        <a href="{{ route('teacher.quizList') }}">My Quizzes</a>
        <a href="{{ route('teacher.students') }}">Students</a>
        <a href="{{ route('teacher.makeQuiz') }}" class="btn btn-success">Create Quiz</a>
        <a href="{{ route('teacher.logout') }}" class="logout-btn">Logout</a>
    </div>
</div>
@endsection

@section('content')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-header h1 {
        font-size: 32px;
        color: #333;
    }

    .quiz-grid {
        display: grid;
        gap: 20px;
    }

    .quiz-card {
        background: white;
        border-radius: 10px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 5px solid #667eea;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .quiz-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }

    .quiz-card.expired {
        border-left-color: #6c757d;
        opacity: 0.8;
    }

    .quiz-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .quiz-title {
        font-size: 22px;
        font-weight: 600;
        color: #333;
        flex: 1;
    }

    .quiz-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin: 15px 0;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
    }

    .stat-item {
        text-align: center;
    }

    .stat-value {
        font-size: 24px;
        font-weight: bold;
        color: #667eea;
    }

    .stat-label {
        font-size: 13px;
        color: #666;
        margin-top: 5px;
    }

    .quiz-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }
</style>

<div class="page-header">
    <h1>📚 My Quizzes</h1>
    <a href="{{ route('teacher.makeQuiz') }}" class="btn btn-primary">
        ➕ Create New Quiz
    </a>
</div>

@if($quizzes->count() > 0)
    <div class="quiz-grid">
        @foreach($quizzes as $quiz)
        <div class="quiz-card {{ $quiz->expire_time < now() ? 'expired' : '' }}">
            <div class="quiz-card-header">
                <div class="quiz-title">{{ $quiz->title }}</div>
                @if($quiz->expire_time < now())
                    <span class="badge badge-danger">Expired</span>
                @else
                    <span class="badge badge-success">Active</span>
                @endif
            </div>

            <div style="color: #666; font-size: 14px; margin-bottom: 10px;">
                <div>⏱️ Duration: <strong>{{ $quiz->duration }} minutes</strong></div>
                <div>📅 Created: {{ $quiz->creation_time->format('M d, Y') }}</div>
                <div>⏰ Expires: {{ $quiz->expire_time->format('M d, Y h:i A') }}</div>
                <div>
                    @if($quiz->expire_time < now())
                        <span style="color: #dc3545;">⏰ Expired {{ $quiz->expire_time->diffForHumans() }}</span>
                    @else
                        <span style="color: #28a745;">⏰ Expires {{ $quiz->expire_time->diffForHumans() }}</span>
                    @endif
                </div>
            </div>

            <div class="quiz-stats">
                <div class="stat-item">
                    <div class="stat-value">{{ $quiz->questions_count }}</div>
                    <div class="stat-label">Questions</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">{{ $quiz->results_count }}</div>
                    <div class="stat-label">Participants</div>
                </div>
            </div>

            <div class="quiz-actions">
                <a href="{{ route('teacher.quiz_results', $quiz->id) }}" class="btn btn-info btn-sm">
                    📊 View Results
                </a>
                <a href="{{ route('teacher.addQuestions', $quiz->id) }}" class="btn btn-secondary btn-sm">
                    ✏️ Manage Questions
                </a>
                <form action="{{ route('teacher.deleteQuiz', $quiz->id) }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" 
                            onclick="return confirm('Are you sure? This will delete the quiz and all related data!')">
                        🗑️ Delete
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>

    <div style="margin-top: 30px;">
        {{ $quizzes->links() }}
    </div>
@else
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">📝</div>
            <h3>No Quizzes Created Yet</h3>
            <p>Create your first quiz to get started!</p>
            <a href="{{ route('teacher.makeQuiz') }}" class="btn btn-primary" style="margin-top: 20px;">
                Create Your First Quiz
            </a>
        </div>
    </div>
@endif
@endsection