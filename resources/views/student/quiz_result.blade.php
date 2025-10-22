@extends('layouts.app')

@section('title', 'Quiz Result')

@section('navbar')
<div class="navbar">
    <a href="{{ route('student.dashboard') }}" class="navbar-brand">📚 Quiz System</a>
    <div class="navbar-menu">
        <a href="{{ route('student.dashboard') }}">Dashboard</a>
        <a href="{{ route('student.myResults') }}">My Results</a>
        <a href="{{ route('student.logout') }}" class="logout-btn">Logout</a>
    </div>
</div>
@endsection

@section('content')
<style>
    .result-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px;
        border-radius: 10px;
        margin-bottom: 30px;
        text-align: center;
    }

    .result-header h1 {
        font-size: 32px;
        margin-bottom: 15px;
    }

    .score-display {
        font-size: 64px;
        font-weight: bold;
        margin: 20px 0;
    }

    .score-label {
        font-size: 18px;
        opacity: 0.9;
    }

    .result-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .stat-icon {
        font-size: 36px;
        margin-bottom: 10px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: bold;
        color: #667eea;
        margin-bottom: 5px;
    }

    .stat-label {
        font-size: 14px;
        color: #666;
    }

    .answer-review {
        background: white;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .answer-review.correct {
        border-left: 5px solid #28a745;
        background: #f0fff4;
    }

    .answer-review.incorrect {
        border-left: 5px solid #dc3545;
        background: #fff5f5;
    }

    .answer-review.unanswered {
        border-left: 5px solid #ffc107;
        background: #fffbf0;
    }

    .review-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #eee;
    }

    .review-question-number {
        font-size: 18px;
        font-weight: bold;
        color: #667eea;
    }

    .review-status {
        padding: 5px 15px;
        border-radius: 15px;
        font-size: 13px;
        font-weight: 600;
    }

    .status-correct {
        background: #d4edda;
        color: #155724;
    }

    .status-incorrect {
        background: #f8d7da;
        color: #721c24;
    }

    .status-unanswered {
        background: #fff3cd;
        color: #856404;
    }

    .review-question-text {
        font-size: 16px;
        color: #333;
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .answer-box {
        padding: 15px;
        background: white;
        border-radius: 8px;
        margin-bottom: 10px;
    }

    .answer-label {
        font-size: 13px;
        color: #666;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .answer-value {
        font-size: 15px;
        color: #333;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
        border-left: 3px solid #667eea;
    }

    .correct-answer {
        border-left-color: #28a745 !important;
        background: #d4edda !important;
    }

    .your-answer {
        border-left-color: #dc3545 !important;
        background: #f8d7da !important;
    }

    .performance-badge {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 25px;
        font-size: 16px;
        font-weight: 600;
        margin-top: 10px;
    }

    .grade-excellent {
        background: #d4edda;
        color: #155724;
    }

    .grade-good {
        background: #d1ecf1;
        color: #0c5460;
    }

    .grade-average {
        background: #fff3cd;
        color: #856404;
    }

    .grade-poor {
        background: #f8d7da;
        color: #721c24;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: center;
        margin-top: 30px;
    }
</style>

<div class="result-header">
    <h1>📊 Quiz Results</h1>
    <div style="font-size: 18px; opacity: 0.9; margin-bottom: 10px;">
        {{ $quiz->title }}
    </div>
    <div class="score-display">
        {{ number_format($result->percentage, 1) }}%
    </div>
    <div class="score-label">
        {{ $result->score }} out of {{ $result->total_marks }} marks
    </div>
    
    @php
        $percentage = $result->percentage;
        if ($percentage >= 80) {
            $grade = 'Excellent!';
            $gradeClass = 'grade-excellent';
            $emoji = '🌟';
        } elseif ($percentage >= 60) {
            $grade = 'Good Job!';
            $gradeClass = 'grade-good';
            $emoji = '👍';
        } elseif ($percentage >= 40) {
            $grade = 'Keep Practicing';
            $gradeClass = 'grade-average';
            $emoji = '📚';
        } else {
            $grade = 'Need Improvement';
            $gradeClass = 'grade-poor';
            $emoji = '💪';
        }
    @endphp
    
    <div class="performance-badge {{ $gradeClass }}">
        {{ $emoji }} {{ $grade }}
    </div>
    
    <div style="margin-top: 15px; font-size: 14px; opacity: 0.9;">
        Submitted on {{ $result->submitted_at->format('M d, Y h:i A') }}
    </div>
</div>

<div class="result-stats">
    <div class="stat-card">
        <div class="stat-icon">✅</div>
        <div class="stat-value">{{ $result->score }}</div>
        <div class="stat-label">Total Score</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📊</div>
        <div class="stat-value">{{ $result->total_marks }}</div>
        <div class="stat-label">Total Marks</div>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📈</div>
        <div class="stat-value">{{ number_format($result->percentage, 1) }}%</div>
        <div class="stat-label">Percentage</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        📝 Detailed Answer Review
    </div>

    @foreach($quiz->questions as $question)
        @php
            $answer = $answers->get($question->q_no);
            $studentAnswer = $answer->ans ?? null;
            $isCorrect = $answer->evaluation ?? null;
            
            if ($studentAnswer === null) {
                $statusClass = 'unanswered';
                $statusText = 'Unanswered';
                $statusBadgeClass = 'status-unanswered';
            } elseif ($isCorrect) {
                $statusClass = 'correct';
                $statusText = 'Correct';
                $statusBadgeClass = 'status-correct';
            } else {
                $statusClass = 'incorrect';
                $statusText = 'Incorrect';
                $statusBadgeClass = 'status-incorrect';
            }
        @endphp

        <div class="answer-review {{ $statusClass }}">
            <div class="review-header">
                <div class="review-question-number">Question {{ $question->q_no }}</div>
                <span class="review-status {{ $statusBadgeClass }}">
                    @if($studentAnswer === null)
                        ⚠️ {{ $statusText }}
                    @elseif($isCorrect)
                        ✓ {{ $statusText }}
                    @else
                        ✗ {{ $statusText }}
                    @endif
                </span>
            </div>

            <div class="review-question-text">
                {{ $question->question }}
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                <span style="font-size: 14px; color: #666;">
                    Type: {{ $question->type === 'mcq' ? '📋 Multiple Choice' : '✍️ Short Answer' }}
                </span>
                <span style="font-size: 14px; font-weight: 600; color: #667eea;">
                    Points: {{ $answer->marks_obtained ?? 0 }} / {{ $question->marks }}
                </span>
            </div>

            <div class="answer-box">
                <div class="answer-label">Your Answer:</div>
                <div class="answer-value {{ $isCorrect ? 'correct-answer' : 'your-answer' }}">
                    @if($studentAnswer)
                        @if($question->type === 'mcq')
                            {{ strtoupper($studentAnswer) }}. 
                            {{ $question->{strtolower($studentAnswer)} }}
                        @else
                            {{ $studentAnswer }}
                        @endif
                    @else
                        <em style="color: #999;">No answer provided</em>
                    @endif
                </div>
            </div>

            @if(!$isCorrect)
            <div class="answer-box">
                <div class="answer-label">Correct Answer:</div>
                <div class="answer-value correct-answer">
                    @if($question->type === 'mcq')
                        {{ strtoupper($question->correct_answer) }}. 
                        {{ $question->{strtolower($question->correct_answer)} }}
                    @else
                        {{ $question->correct_answer }}
                    @endif
                </div>
            </div>
            @endif
        </div>
    @endforeach
</div>

<div class="action-buttons">
    <a href="{{ route('student.dashboard') }}" class="btn btn-primary">
        🏠 Back to Dashboard
    </a>
    <a href="{{ route('student.myResults') }}" class="btn btn-secondary">
        📊 View All Results
    </a>
    <button onclick="window.print()" class="btn btn-info">
        🖨️ Print Results
    </button>
</div>

<style>
    @media print {
        .navbar, .action-buttons {
            display: none;
        }
    }
</style>
@endsection