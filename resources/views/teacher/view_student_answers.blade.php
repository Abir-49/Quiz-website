@extends('layouts.app')

@section('title', 'Student Answer Script')

@section('navbar')
<div class="navbar">
    <a href="{{ route('teacher.dashboard') }}" class="navbar-brand">📚 Quiz System</a>
    <div class="navbar-menu">
        <a href="{{ route('teacher.dashboard') }}">Dashboard</a>
        <a href="{{ route('teacher.quiz_results', $quiz->id) }}">Back to Results</a>
        <a href="{{ route('teacher.logout') }}" class="logout-btn">Logout</a>
    </div>
</div>
@endsection

@section('content')
<style>
    .answer-script-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .answer-script-header h1 {
        font-size: 28px;
        margin-bottom: 15px;
    }

    .student-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin-top: 15px;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .result-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 15px;
        margin-bottom: 30px;
    }

    .summary-card {
        background: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .summary-value {
        font-size: 28px;
        font-weight: bold;
        color: #667eea;
        margin-bottom: 5px;
    }

    .summary-label {
        font-size: 13px;
        color: #666;
    }

    .answer-item {
        background: white;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 5px solid #ddd;
    }

    .answer-item.correct {
        border-left-color: #28a745;
        background: #f0fff4;
    }

    .answer-item.incorrect {
        border-left-color: #dc3545;
        background: #fff5f5;
    }

    .answer-item.unanswered {
        border-left-color: #ffc107;
        background: #fffbf0;
    }

    .answer-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .question-number {
        font-size: 18px;
        font-weight: bold;
        color: #667eea;
    }

    .question-text {
        font-size: 16px;
        color: #333;
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .answer-section {
        background: white;
        padding: 15px;
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

    .mcq-options {
        display: grid;
        gap: 10px;
        margin-top: 10px;
    }

    .mcq-option {
        padding: 10px 15px;
        background: #f8f9fa;
        border-radius: 5px;
        border-left: 3px solid #ddd;
    }

    .mcq-option.selected {
        border-left-color: #667eea;
        background: #e7f3ff;
        font-weight: 600;
    }

    .mcq-option.correct {
        border-left-color: #28a745;
        background: #d4edda;
        font-weight: 600;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
        display: inline-block;
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

    .marks-info {
        text-align: center;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 5px;
        font-weight: 600;
    }

    .print-button {
        position: fixed;
        bottom: 30px;
        right: 30px;
        padding: 15px 25px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 50px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        transition: all 0.3s;
    }

    .print-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
    }

    @media print {
        .navbar, .print-button {
            display: none;
        }
    }
</style>

<div class="answer-script-header">
    <h1>📋 Answer Script</h1>
    <div class="student-info-grid">
        <div class="info-item">
            <strong>Student:</strong> {{ $student->name }}
        </div>
        <div class="info-item">
            <strong>Roll:</strong> {{ $student->roll }}
        </div>
        <div class="info-item">
            <strong>Quiz:</strong> {{ $quiz->title }}
        </div>
        <div class="info-item">
            <strong>Submitted:</strong> {{ $result->submitted_at->format('M d, Y h:i A') }}
        </div>
    </div>
</div>

<!-- Result Summary -->
<div class="result-summary">
    <div class="summary-card">
        <div class="summary-value">{{ $result->score }}</div>
        <div class="summary-label">Total Score</div>
    </div>
    <div class="summary-card">
        <div class="summary-value">{{ $result->total_marks }}</div>
        <div class="summary-label">Total Marks</div>
    </div>
    <div class="summary-card">
        <div class="summary-value" style="color: <?php echo $result->percentage >= 60 ? '#28a745' : '#dc3545'; ?>">
            {{ number_format($result->percentage, 1) }}%
        </div>
        <div class="summary-label">Percentage</div>
    </div>
</div>

<!-- Questions and Answers -->
<div class="card">
    <div class="card-header">
        📝 Detailed Answer Script
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

        <div class="answer-item {{ $statusClass }}">
            <div class="answer-header">
                <div class="question-number">Question {{ $question->q_no }}</div>
                <span class="status-badge {{ $statusBadgeClass }}">
                    @if($studentAnswer === null)
                        ⚠️ {{ $statusText }}
                    @elseif($isCorrect)
                        ✓ {{ $statusText }}
                    @else
                        ✗ {{ $statusText }}
                    @endif
                </span>
            </div>

            <div class="question-text">
                {{ $question->question }}
            </div>

            <div class="marks-info">
                Marks: {{ $answer->marks_obtained ?? 0 }} / {{ $question->marks }}
            </div>

            @if($question->type === 'mcq')
                <!-- MCQ Options Display -->
                <div style="margin-top: 15px;">
                    <div class="answer-label">Options:</div>
                    <div class="mcq-options">
                        <div class="mcq-option {{ strtolower($studentAnswer) === 'a' ? 'selected' : '' }} {{ strtolower($question->correct_answer) === 'a' ? 'correct' : '' }}">
                            A. {{ $question->a }}
                            @if(strtolower($question->correct_answer) === 'a')
                                <span style="color: #28a745; font-weight: bold;"> ✓ Correct Answer</span>
                            @endif
                        </div>
                        <div class="mcq-option {{ strtolower($studentAnswer) === 'b' ? 'selected' : '' }} {{ strtolower($question->correct_answer) === 'b' ? 'correct' : '' }}">
                            B. {{ $question->b }}
                            @if(strtolower($question->correct_answer) === 'b')
                                <span style="color: #28a745; font-weight: bold;"> ✓ Correct Answer</span>
                            @endif
                        </div>
                        <div class="mcq-option {{ strtolower($studentAnswer) === 'c' ? 'selected' : '' }} {{ strtolower($question->correct_answer) === 'c' ? 'correct' : '' }}">
                            C. {{ $question->c }}
                            @if(strtolower($question->correct_answer) === 'c')
                                <span style="color: #28a745; font-weight: bold;"> ✓ Correct Answer</span>
                            @endif
                        </div>
                        <div class="mcq-option {{ strtolower($studentAnswer) === 'd' ? 'selected' : '' }} {{ strtolower($question->correct_answer) === 'd' ? 'correct' : '' }}">
                            D. {{ $question->d }}
                            @if(strtolower($question->correct_answer) === 'd')
                                <span style="color: #28a745; font-weight: bold;"> ✓ Correct Answer</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div style="margin-top: 15px;">
                    <div class="answer-section">
                        <div class="answer-label">Student's Answer:</div>
                        <div class="answer-value">
                            {{ $studentAnswer ? strtoupper($studentAnswer) : 'No answer provided' }}
                        </div>
                    </div>
                </div>
            @else
                <!-- Short Answer Display -->
                <div style="margin-top: 15px;">
                    <div class="answer-section">
                        <div class="answer-label">Student's Answer:</div>
                        <div class="answer-value">
                            {{ $studentAnswer ?: 'No answer provided' }}
                        </div>
                    </div>

                    <div class="answer-section">
                        <div class="answer-label">Correct Answer:</div>
                        <div class="answer-value" style="border-left-color: #28a745;">
                            {{ $question->correct_answer }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>

<button class="print-button" onclick="window.print()">
    🖨️ Print Answer Script
</button>
@endsection