@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('navbar')
<div class="navbar">
    <a href="{{ route('student.dashboard') }}" class="navbar-brand">📚 Quiz System</a>
    <div class="navbar-menu">
        <span>Welcome, {{ Session::get('student_name') }}</span>
        <a href="{{ route('student.dashboard') }}">Dashboard</a>
        <a href="{{ route('student.myTeachers') }}">My Teachers</a>
        <a href="{{ route('student.myResults') }}">My Results</a>
        <a href="{{ route('student.search_teachers') }}" class="btn btn-success">Join Class</a>
        <a href="{{ route('student.logout') }}" class="logout-btn">Logout</a>
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

    .quiz-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .quiz-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 5px solid #667eea;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .quiz-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }

    .quiz-card-title {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }

    .quiz-card-meta {
        font-size: 13px;
        color: #666;
        margin-bottom: 15px;
    }

    .quiz-card-teacher {
        font-size: 14px;
        color: #667eea;
        margin-bottom: 10px;
    }

    .teacher-card {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .teacher-info {
        flex: 1;
    }

    .teacher-name {
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }

    .teacher-email {
        font-size: 13px;
        color: #666;
    }

    .result-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 10px;
        transition: background 0.2s;
    }

    .result-item:hover {
        background: #e9ecef;
    }

    .result-info {
        flex: 1;
    }

    .result-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    .result-meta {
        font-size: 13px;
        color: #666;
    }

    .result-score {
        text-align: center;
        margin: 0 20px;
    }

    .score-value {
        font-size: 24px;
        font-weight: bold;
        color: #667eea;
    }

    .score-label {
        font-size: 12px;
        color: #666;
    }

    .pending-request {
        background: #fff3cd;
        padding: 15px;
        border-radius: 8px;
        border-left: 4px solid #ffc107;
        margin-bottom: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<div class="dashboard-header">
    <h1>👨‍🎓 Student Dashboard</h1>
    <p>Track your progress and take available quizzes</p>
    
    <div class="quick-actions">
        <a href="{{ route('student.search_teachers') }}" class="btn btn-primary">
            ➕ Join a Class
        </a>
        <a href="{{ route('student.myTeachers') }}" class="btn btn-info">
            👥 My Teachers
        </a>
        <a href="{{ route('student.myResults') }}" class="btn btn-secondary">
            📊 View All Results
        </a>
    </div>
</div>

<!-- Statistics -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-number">{{ number_format($averageScore, 1) }}%</div>
        <div class="stat-label">Average Score</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
        <div class="stat-number">{{ $totalQuizTaken }}</div>
        <div class="stat-label">Quizzes Taken</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
        <div class="stat-number">{{ $missedQuizzes }}</div>
        <div class="stat-label">Quizzes Missed</div>
    </div>
    <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
        <div class="stat-number">{{ $subscribedTeachers->count() }}</div>
        <div class="stat-label">Teachers Subscribed</div>
    </div>
</div>

<!-- Pending Requests -->
@if($pendingRequests->count() > 0)
<h2 class="section-title">⏳ Pending Join Requests</h2>
<div class="card">
    @foreach($pendingRequests as $request)
    <div class="pending-request">
        <div>
            <strong>{{ $request->teacher->name }}</strong>
            <div style="font-size: 13px; color: #666; margin-top: 3px;">
                Requested {{ $request->requested_at->diffForHumans() }}
            </div>
        </div>
        <form action="{{ route('student.cancelRequest', $request->t_id) }}" method="POST">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">
                Cancel Request
            </button>
        </form>
    </div>
    @endforeach
</div>
@endif

<!-- Available Quizzes -->
<h2 class="section-title">📝 Available Quizzes</h2>
@if($availableQuizzes->count() > 0)
    <div class="quiz-grid">
        @foreach($availableQuizzes as $quiz)
        <div class="quiz-card">
            <div class="quiz-card-title">{{ $quiz->title }}</div>
            <div class="quiz-card-teacher">
                👨‍🏫 {{ $quiz->teacher->name }}
            </div>
            <div class="quiz-card-meta">
                ⏱️ Duration: {{ $quiz->duration }} minutes<br>
                ⏰ Expires: {{ $quiz->expire_time->format('M d, Y h:i A') }}<br>
                <span class="{{ $quiz->expire_time->diffInHours(now()) < 24 ? 'text-danger' : 'text-success' }}">
                    {{ $quiz->expire_time->diffForHumans() }}
                </span>
            </div>
            <a href="{{ route('student.take_quiz', $quiz->id) }}" class="btn btn-primary" style="width: 100%;">
                Start Quiz
            </a>
        </div>
        @endforeach
    </div>
@else
    <div class="card">
        <div class="empty-state">
            <div class="empty-state-icon">📭</div>
            <h3>No Available Quizzes</h3>
            <p>Check back later for new quizzes from your teachers</p>
            @if($subscribedTeachers->count() === 0)
                <a href="{{ route('student.search_teachers') }}" class="btn btn-primary" style="margin-top: 15px;">
                    Join a Teacher's Class
                </a>
            @endif
        </div>
    </div>
@endif

<!-- Recent Results -->
<h2 class="section-title">📊 Recent Quiz Results</h2>
<div class="card">
    @if($quizzesTaken->count() > 0)
        @foreach($quizzesTaken as $result)
        <div class="result-item">
            <div class="result-info">
                <div class="result-title">{{ $result->quiz->title }}</div>
                <div class="result-meta">
                    👨‍🏫 {{ $result->quiz->teacher->name }} | 
                    📅 {{ $result->submitted_at->format('M d, Y') }}
                </div>
            </div>
            <div class="result-score">
                <div class="score-value">{{ number_format($result->percentage, 1) }}%</div>
                <div class="score-label">{{ $result->score }}/{{ $result->total_marks }}</div>
            </div>
            <div>
                <a href="{{ route('student.quiz_result', $result->quiz->id) }}" class="btn btn-info btn-sm">
                    View Details
                </a>
            </div>
        </div>
        @endforeach
        
        @if($totalQuizTaken > 5)
        <div style="text-align: center; margin-top: 15px;">
            <a href="{{ route('student.myResults') }}" class="btn btn-secondary">
                View All Results
            </a>
        </div>
        @endif
    @else
        <div class="empty-state">
            <div class="empty-state-icon">📊</div>
            <p>No quiz results yet. Take your first quiz!</p>
        </div>
    @endif
</div>

<!-- Subscribed Teachers -->
<h2 class="section-title">👥 My Teachers</h2>
<div class="card">
    @if($subscribedTeachers->count() > 0)
        @foreach($subscribedTeachers as $teacher)
        <div class="teacher-card">
            <div class="teacher-info">
                <div class="teacher-name">{{ $teacher->name }}</div>
                <div class="teacher-email">
                    {{ $teacher->email }} | 
                    Total Quizzes: {{ $teacher->quizzes_count }}
                </div>
            </div>
            <form action="{{ route('student.leaveClass', $teacher->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" 
                        onclick="return confirm('Leave this teacher\'s class?')">
                    Leave Class
                </button>
            </form>
        </div>
        @endforeach
        
        <div style="text-align: center; margin-top: 15px;">
            <a href="{{ route('student.myTeachers') }}" class="btn btn-secondary">
                View All Teachers
            </a>
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <h3>Not Enrolled in Any Class</h3>
            <p>Join a teacher's class to start taking quizzes</p>
            <a href="{{ route('student.search_teachers') }}" class="btn btn-primary" style="margin-top: 15px;">
                Search Teachers
            </a>
        </div>
    @endif
</div>
@endsection