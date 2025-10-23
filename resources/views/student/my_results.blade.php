@extends('layouts.app')

@section('title', 'My Results')

@section('navbar')
<div class="navbar">
    <a href="{{ route('student.dashboard') }}" class="navbar-brand">📚 Quiz System</a>
    <div class="navbar-menu">
        <a href="{{ route('student.dashboard') }}">Dashboard</a>
        <a href="{{ route('student.myTeachers') }}">My Teachers</a>
        <a href="{{ route('student.myResults') }}" class="active">My Results</a>
        <a href="{{ route('student.search_teachers') }}" class="btn btn-success">Join New Class</a>
        <a href="{{ route('student.logout') }}" class="logout-btn">Logout</a>
    </div>
</div>
@endsection

@section('content')
<style>
      .quiz-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        justify-content: flex-end;
    }

    .quiz-actions .btn {
        min-width: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
    }

    .quiz-actions .btn-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        border: none;
        color: white;
        padding: 8px 15px;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .quiz-actions .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(79, 172, 254, 0.3);
    }
    .result-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        transition: transform 0.2s;
    }

    .result-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }
    .result-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }
    .quiz-title {
        font-size: 20px;
        font-weight: 600;
        color: #333;
    }
    .teacher-name {
        font-size: 14px;
        color: #666;
    }
    .result-stats {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        font-size: 15px;
        color: #444;
    }
    .btn-view {
        background: #007bff;
        color: white;
        border-radius: 6px;
        padding: 6px 12px;
        text-decoration: none;
        transition: background 0.2s;
    }
    .btn-view:hover {
        background: #0056b3;
    }
    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }
    .pagination .page-link {
        color: #007bff;
        padding: 8px 12px;
        margin: 0 4px;
        border-radius: 5px;
        border: 1px solid #ddd;
        text-decoration: none;
    }
    .pagination .active .page-link {
        background: #007bff;
        color: white;
        border-color: #007bff;
    }
</style>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 32px; color: #333; margin-bottom: 10px;">📊 My Quiz Results</h1>
    <p>Your recent quiz performances</p>
</div>

@if($results->count() > 0)
    @foreach($results as $result)
        <div class="result-card">
            <div class="result-header">
                <div>
                    <div class="quiz-title">{{ $result->quiz->title ?? 'Untitled Quiz' }}</div>
                    
                    <div class="teacher-name">
                        Teacher: {{ $result->quiz->teacher->name ?? 'N/A' }}
                    </div>
                    @if($result->quiz->expired)
                    <div class="quiz-actions">
                <a href="{{ route('student.quiz_result', $result->$quiz->id) }}" class="btn btn-info btn-sm">
                    Answer Script
                </a>
           
                <a href="{{ route('student.class_result', ['quiz' => $result->$quiz->id, 't_id' => $result->$quiz->t_id]) }}" class="btn btn-info btn-sm">
    View Result
                </a>
            </div>
                
                    @endif
            <div class="result-stats">
                <div><strong>Score:</strong> {{ $result->score }} / {{ $result->total_marks }}</div>
                <div><strong>Percentage:</strong> {{ $result->percentage }}%</div>
                <div><strong>Submitted:</strong> {{ \Carbon\Carbon::parse($result->submitted_at)->format('d M Y, h:i A') }}</div>
            </div>
        </div>
    @endforeach

    <div class="pagination">
        {{ $results->links('pagination::bootstrap-4') }}
    </div>
@else
    <div style="text-align:center; padding: 40px; color:#666;">
        <h3>No results found yet</h3>
        <p>Once you take quizzes, your results will appear here.</p>
    </div>
@endif
@endsection
