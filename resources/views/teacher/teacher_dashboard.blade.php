@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('navbar')
<div class="navbar">
    <a href="{{ route('teacher.dashboard') }}" class="navbar-brand">📚 Quiz System</a>
    <div class="navbar-menu">
        <span>Welcome, {{ Session::get('teacher_name') }}</span>
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
    .dashboard-header {
        margin-bottom: 30px;
    }

    .dashboard-header h1 {
        font-size: 32px;
        color: #333;
        margin-bottom: 10px;
    }

    .quick-actions {
        display: flex;
        gap: 15px;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .section-title {
        font-size: 22px;
        font-weight: 600;
        color: #333;
        margin: 30px 0 15px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #667eea;
    }

    .empty-state {
        text-align: center;
        padding: 40px;
        color: #999;
    }

    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 15px;
    }

    .quiz-item {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 15px;
        border-left: 4px solid #667eea;
    }

    .quiz-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .quiz-item-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
    }

    .quiz-item-meta {
        display: flex;
        gap: 20px;
        color: #666;
        font-size: 14px;
        margin-bottom: 10px;
    }

    .quiz-item-actions {
        display: flex;
        gap: 10px;
    }

    .request-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: #fff3cd;
        border-radius: 8px;
        margin-bottom: 10px;
        border-left: 4px solid #ffc107;
    }

    .request-info {
        flex: 1;
    }

    .request-actions {
        display: flex;
        gap: 10px;
    }
</style>

<div class="dashboard-header">
    <h1>👨‍🏫 Teacher Dashboard</h1>
    <p>Manage your quizzes, students, and classroom activities</p>
    
    <div class="quick-actions">
        <a href="{{ route('teacher.makeQuiz') }}" class="btn btn-primary">
            ➕ Create New Quiz
        </a>
        <a href="{{ route('teacher.quizList') }}" class="btn btn-info">
            📋 View All Quizzes
        </a>
        <a href="{{ route('teacher.students') }}" class="btn btn-secondary">
            👥 Manage Students
        </a>
    </div>
</div>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number">{{ $total_quizzes }}</div>
        <div class="stat-label">Total Quizzes Created</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
        <div class="stat-number">{{ $pending_quizzes->count() }}</div>
        <div class="stat-label">Active Quizzes</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
        <div class="stat-number">{{ $finished_quizzes->count() }}</div>
        <div class="stat-label">Finished Quizzes</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
        <div class="stat-number">{{ $total_students }}</div>
        <div class="stat-label">Total Students</div>
    </div>
</div>

<!-- Student Requests -->
@if($requests->count() > 0)
<div class="card">
    <div class="card-header">
        🔔 Pending Student Requests ({{ $requests->count() }})
    </div>
    
    @foreach($requests as $req)
    <div class="request-item">
        <div class="request-info">
            <strong>{{ $req->student->name }}</strong> ({{ $req->student->roll }})
            <div style="font-size: 13px; color: #666; margin-top: 5px;">
                {{ $req->student->email }} • Requested {{ $req->requested_at->diffForHumans() }}
            </div>
        </div>
        <div class="request-actions">
            <a href="{{ route('teacher.request.approve', $req->id) }}" 
               class="btn btn-success"
               onclick="return confirm('Approve this student?')">
                ✓ Approve
            </a>
            <a href="{{ route('teacher.request.reject', $req->id) }}" 
               class="btn btn-danger"
               onclick="return confirm('Reject this request?')">
                ✗ Reject
            </a>
        </div>
    </div>
    @endforeach
</div>
@endif

<!-- Active Quizzes -->
<h2 class="section-title">📝 Active Quizzes</h2>
<div class="card">
    @if($pending_quizzes->count() > 0)
        @foreach($pending_quizzes as $quiz)
        <div class="quiz-item">
            <div class="quiz-item-header">
                <div class="quiz-item-title">{{ $quiz->title }}</div>
                <span class="badge badge-success">Active</span>
            </div>
            <div class="quiz-item-meta">
                <span>⏱️ Duration: {{ $quiz->duration }} mins</span>
                <span>📅 Expires: {{ $quiz->expire_time->format('M d, Y h:i A') }}</span>
                <span>👥 Participants: {{ $quiz->results_count }}</span>
            </div>
            <div class="quiz-item-actions">
                <a href="{{ route('teacher.quiz_results', $quiz->id) }}" class="btn btn-info btn-sm">
                    View Results
                </a>
                <a href="{{ route('teacher.addQuestions', $quiz->id) }}" class="btn btn-secondary btn-sm">
                    Manage Questions
                </a>
            </div>
        </div>
        @endforeach
        
        @if($pending_quizzes->count() > 5)
        <div style="text-align: center; margin-top: 15px;">
            <a href="{{ route('teacher.quizList') }}" class="btn btn-secondary">
                View All Quizzes
            </a>
        </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-state-icon">📭</div>
            <p>No active quizzes. Create your first quiz!</p>
            <a href="{{ route('teacher.makeQuiz') }}" class="btn btn-primary" style="margin-top: 15px;">
                Create Quiz
            </a>
        </div>
    @endif
</div>

<!-- Finished Quizzes -->
<h2 class="section-title">✅ Recently Finished Quizzes</h2>
<div class="card">
    @if($finished_quizzes->count() > 0)
        @foreach($finished_quizzes as $quiz)
        <div class="quiz-item" style="border-left-color: #6c757d;">
            <div class="quiz-item-header">
                <div class="quiz-item-title">{{ $quiz->title }}</div>
                <span class="badge badge-danger">Expired</span>
            </div>
            <div class="quiz-item-meta">
                <span>⏱️ Duration: {{ $quiz->duration }} mins</span>
                <span>📅 Expired: {{ $quiz->expire_time->format('M d, Y h:i A') }}</span>
                <span>👥 Participants: {{ $quiz->results_count }}</span>
            </div>
            <div class="quiz-item-actions">
                <a href="{{ route('teacher.quiz_results', $quiz->id) }}" class="btn btn-info btn-sm">
                    View Results
                </a>
            </div>
        </div>
        @endforeach
    @else
        <div class="empty-state">
            <div class="empty-state-icon">📂</div>
            <p>No finished quizzes yet</p>
        </div>
    @endif
</div>

<!-- Classroom Students Preview -->
<h2 class="section-title">👥 Classroom Students</h2>
<div class="card">
    @if($students->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Roll</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Quizzes Taken</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                <tr>
                    <td><strong>{{ $student->roll }}</strong></td>
                    <td>{{ $student->name }}</td>
                    <td>{{ $student->email }}</td>
                    <td>{{ $student->results_count }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        @if($students->count() >= 10)
        <div style="text-align: center; margin-top: 15px;">
            <a href="{{ route('teacher.students') }}" class="btn btn-secondary">
                View All Students
            </a>
        </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <p>No students enrolled yet</p>
            <p style="font-size: 14px; margin-top: 10px;">
                Your Teacher ID: <strong style="color: #667eea;">{{ Session::get('teacher_id') }}</strong>
                <br>
                <small>Share this ID with students to let them join your class</small>
            </p>
        </div>
    @endif
</div>
@endsection