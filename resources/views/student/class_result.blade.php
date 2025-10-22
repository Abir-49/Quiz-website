@extends('layouts.app')

@section('title', 'Leaderboard')

@section('navbar')
<div class="navbar">
    <a href="{{ route('student.dashboard') }}" class="navbar-brand">📚 Quiz System</a>
    <div class="navbar-menu">
        <a href="{{ route('student.dashboard') }}">Dashboard</a>
        <a href="{{ route('student.myTeachers') }}">My Teachers</a>
        <a href="{{ route('student.myResults') }}">My Results</a>
        <a href="{{ route('student.search_teachers') }}" class="btn btn-success">Join New Class</a>
        <a href="{{ route('student.logout') }}" class="logout-btn">Logout</a>
    </div>
</div>
@endsection

@section('content')


<style>
    .results-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .results-header h1 {
        font-size: 32px;
        margin-bottom: 15px;
    }

    .results-meta {
        display: flex;
        gap: 30px;
        font-size: 14px;
        opacity: 0.9;
    }

    .results-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .result-stat-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .result-stat-icon {
        font-size: 36px;
        margin-bottom: 10px;
    }

    .result-stat-value {
        font-size: 32px;
        font-weight: bold;
        color: #667eea;
        margin-bottom: 5px;
    }

    .result-stat-label {
        color: #666;
        font-size: 14px;
    }

    .participant-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 20px;
        background: white;
        border-radius: 8px;
        margin-bottom: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        transition: transform 0.2s;
    }

    .participant-item:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .participant-info {
        flex: 1;
    }

    .participant-name {
        font-size: 18px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    .participant-details {
        font-size: 14px;
        color: #666;
    }

    .participant-score {
        text-align: center;
        margin: 0 20px;
    }

    .score-value {
        font-size: 28px;
        font-weight: bold;
        color: #667eea;
    }

    .score-label {
        font-size: 12px;
        color: #666;
    }

    .percentage-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }

    .percentage-excellent {
        background: #d4edda;
        color: #155724;
    }

    .percentage-good {
        background: #d1ecf1;
        color: #0c5460;
    }

    .percentage-average {
        background: #fff3cd;
        color: #856404;
    }

    .percentage-poor {
        background: #f8d7da;
        color: #721c24;
    }

    .absent-list {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
    }

    .absent-item {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
    }

    .absent-item:last-child {
        border-bottom: none;
    }
</style>

<div class="results-header">
    <h1>📊 Quiz Results: {{ $quiz->title }}</h1>
    <div class="results-meta">
        <span>👨‍🏫 Created by: {{ $quiz->teacher->name }}</span>
        <span>⏱️ Duration: {{ $quiz->duration }} minutes</span>
        <span>📅 Expired: {{ $quiz->expire_time->format('M d, Y h:i A') }}</span>
        <span>❓ Total Questions: {{ $quiz->questions->count() }}</span>
    </div>
</div>

<div class="results-stats">
    <div class="result-stat-card">
        <div class="result-stat-icon">👥</div>
        <div class="result-stat-value">{{ $totalParticipants }}</div>
        <div class="result-stat-label">Total Participants</div>
    </div>
    <div class="result-stat-card">
        <div class="result-stat-icon">❌</div>
        <div class="result-stat-value">{{ $absentCount }}</div>
        <div class="result-stat-label">Absent Students</div>
    </div>
    <div class="result-stat-card">
        <div class="result-stat-icon">📈</div>
        <div class="result-stat-value">{{ $averagePercentage }}%</div>
        <div class="result-stat-label">Average Score</div>
    </div>
    <div class="result-stat-card">
        <div class="result-stat-icon">📥</div>
        <div class="result-stat-value">
            <a href="{{ route('student.download_results_stud', ['quiz' => $quiz->id, 't_id' => $quiz->t_id]) }}" 
               class="btn btn-success btn-sm" style="font-size: 14px;">
                Download CSV
            </a>
        </div>
        <div class="result-stat-label">Export Results</div>
    </div>
</div>
<div class="result-stat-card">
        <div class="result-stat-icon">🏅</div>
        <div class="result-stat-value">
            {{ $position }}{{ substr(date("jS", mktime(0, 0, 0, 1, $position, 2000)), -2) }}
        </div>
        <div class="result-stat-label">Your Position</div>
    </div>
<!-- Participants -->
<div class="card">
    <div class="card-header">
        ✅ Participants ({{ $totalParticipants }})
    </div>
    
    @if($participants->count() > 0)
        @foreach($leaderboard as $result)
        @php
            $percentage = $result->percentage;
            if ($percentage >= 80) {
                $badgeClass = 'percentage-excellent';
            } elseif ($percentage >= 60) {
                $badgeClass = 'percentage-good';
            } elseif ($percentage >= 40) {
                $badgeClass = 'percentage-average';
            } else {
                $badgeClass = 'percentage-poor';
            }
        @endphp
        <div class="participant-item">
            <div class="participant-info">
                <div class="participant-name">{{ $result->student->name }}</div>
                <div class="participant-details">
                    Roll: {{ $result->student->roll }} | 
                    Email: {{ $result->student->email }} |
                    Submitted: {{ $result->submitted_at->format('M d, Y h:i A') }}
                </div>
            </div>
            <div class="participant-score">
                <div class="score-value">{{ $result->score }}/{{ $result->total_marks }}</div>
                <div class="score-label">Score</div>
            </div>
            <div>
                <span class="percentage-badge {{ $badgeClass }}">
                    {{ number_format($result->percentage, 1) }}%
                </span>
            </div>
        </div>
        @endforeach
    @else
        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <p>No students have taken this quiz</p>
        </div>
    @endif
</div>

<!-- Absent Students -->
@if($absentStudents->count() > 0)
<div class="card absent-list">
    <div class="card-header">
        ❌ Absent Students ({{ $absentCount }})
    </div>
    
    @foreach($absentStudents as $student)
    <div class="absent-item">
        <strong>{{ $student->name }}</strong> ({{ $student->roll }}) - {{ $student->email }}
    </div>
    @endforeach
</div>
@endif
@endsection
