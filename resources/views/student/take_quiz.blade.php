@extends('layouts.app')

@section('title', 'Take Quiz')

@section('navbar')
<div class="navbar">
    <a href="{{ route('student.dashboard') }}" class="navbar-brand">📚 Quiz System</a>
    <div class="navbar-menu">
        <span id="timer" style="font-size: 18px; font-weight: bold;">⏱️ Time Left: --:--</span>
        <a href="{{ route('student.dashboard') }}">Dashboard</a>
        <a href="{{ route('student.logout') }}" class="logout-btn">Logout</a>
    </div>
</div>
@endsection

@section('content')
<style>
    .quiz-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 30px;
        position: sticky;
        top: 20px;
        z-index: 100;
    }

    .quiz-header h1 {
        font-size: 28px;
        margin-bottom: 10px;
    }

    .quiz-info {
        display: flex;
        gap: 30px;
        font-size: 14px;
        opacity: 0.9;
    }

    .timer-display {
        font-size: 24px;
        font-weight: bold;
        text-align: center;
        padding: 15px;
        background: rgba(255,255,255,0.2);
        border-radius: 10px;
        margin-top: 15px;
    }

    .timer-warning {
        background: #dc3545 !important;
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.7; }
    }

    .question-container {
        background: white;
        border-radius: 10px;
        padding: 30px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 2px solid #eee;
    }

    .question-number {
        font-size: 20px;
        font-weight: bold;
        color: #667eea;
    }

    .question-type-badge {
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
    }

    .badge-mcq {
        background: #d1ecf1;
        color: #0c5460;
    }

    .badge-short {
        background: #d4edda;
        color: #155724;
    }

    .question-text {
        font-size: 18px;
        color: #333;
        line-height: 1.6;
        margin-bottom: 25px;
    }

    .mcq-options {
        display: grid;
        gap: 15px;
    }

    .mcq-option {
        position: relative;
        cursor: pointer;
    }

    .mcq-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        cursor: pointer;
    }

    .mcq-option-label {
        display: flex;
        align-items: center;
        padding: 15px 20px;
        background: #f8f9fa;
        border: 2px solid #ddd;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s;
    }

    .mcq-option input[type="radio"]:checked + .mcq-option-label {
        background: #e7f3ff;
        border-color: #667eea;
        font-weight: 600;
    }

    .mcq-option-label:hover {
        border-color: #667eea;
        transform: translateX(5px);
    }

    .option-letter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        background: #667eea;
        color: white;
        border-radius: 50%;
        font-weight: bold;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .mcq-option input[type="radio"]:checked + .mcq-option-label .option-letter {
        background: #5568d3;
    }

    .short-answer-input {
        width: 100%;
        padding: 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
        transition: border-color 0.3s;
    }

    .short-answer-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .submit-section {
        background: white;
        border-radius: 10px;
        padding: 30px;
        text-align: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        position: sticky;
        bottom: 20px;
    }

    .submit-warning {
        background: #fff3cd;
        color: #856404;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #ffc107;
    }

    .progress-bar {
        width: 100%;
        height: 10px;
        background: #e9ecef;
        border-radius: 5px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: width 0.3s;
    }

    .navigation-buttons {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin-top: 20px;
    }

    .question-nav {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 10px;
        margin-bottom: 20px;
    }

    .question-nav-item {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        border: 2px solid #ddd;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        transition: all 0.3s;
    }

    .question-nav-item:hover {
        border-color: #667eea;
    }

    .question-nav-item.answered {
        background: #d4edda;
        border-color: #28a745;
    }

    .question-nav-item.current {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }
</style>

<div class="quiz-header">
    <h1>📝 {{ $quiz->title }}</h1>
    <div class="quiz-info">
        <span>👨‍🏫 Teacher: {{ $quiz->teacher->name }}</span>
        <span>❓ Questions: {{ $quiz->questions->count() }}</span>
        <span>⏱️ Duration: {{ $quiz->duration }} minutes</span>
    </div>
    <div class="timer-display" id="quiz-timer">
        Time Remaining: <span id="time-left">{{ $quiz->duration }}:00</span>
    </div>
</div>

<!-- Question Navigation -->
<div class="card">
    <div class="card-header">Question Navigation</div>
   <div class="question-nav" id="question-nav">
    @foreach($quiz->questions as $index => $question)
        <button type="button" 
                class="question-nav-item" 
                data-question="{{ $index + 1 }}"
                onclick="scrollToQuestion(this.dataset.question)">
            {{ $index + 1 }}
        </button>
    @endforeach
</div>
    <div class="progress-bar">
        <div class="progress-fill" id="progress-fill" style="width: 0%"></div>
    </div>
    <div style="text-align: center; color: #666; font-size: 14px;">
        <span id="answered-count">0</span> of {{ $quiz->questions->count() }} questions answered
    </div>
</div>

<form action="{{ route('student.submit_quiz', $quiz->id) }}" method="POST" id="quiz-form">
    @csrf

    @foreach($quiz->questions as $question)
    <div class="question-container" id="question-{{ $question->q_no }}">
        <div class="question-header">
            <div class="question-number">Question {{ $question->q_no }}</div>
            <span class="question-type-badge {{ $question->type === 'mcq' ? 'badge-mcq' : 'badge-short' }}">
                {{ $question->type === 'mcq' ? '📋 Multiple Choice' : '✍️ Short Answer' }}
            </span>
        </div>

        <div class="question-text">
            {{ $question->question }}
        </div>

        @if($question->marks > 1)
        <div style="margin-bottom: 15px; color: #667eea; font-weight: 600;">
            Points: {{ $question->marks }}
        </div>
        @endif

        @if($question->type === 'mcq')
        <div class="mcq-options">
            <label class="mcq-option">
                <input type="radio" name="answers[{{ $question->q_no }}]" value="a" 
                       onchange="updateProgress()">
                <div class="mcq-option-label">
                    <span class="option-letter">A</span>
                    <span>{{ $question->a }}</span>
                </div>
            </label>

            <label class="mcq-option">
                <input type="radio" name="answers[{{ $question->q_no }}]" value="b" 
                       onchange="updateProgress()">
                <div class="mcq-option-label">
                    <span class="option-letter">B</span>
                    <span>{{ $question->b }}</span>
                </div>
            </label>

            <label class="mcq-option">
                <input type="radio" name="answers[{{ $question->q_no }}]" value="c" 
                       onchange="updateProgress()">
                <div class="mcq-option-label">
                    <span class="option-letter">C</span>
                    <span>{{ $question->c }}</span>
                </div>
            </label>

            <label class="mcq-option">
                <input type="radio" name="answers[{{ $question->q_no }}]" value="d" 
                       onchange="updateProgress()">
                <div class="mcq-option-label">
                    <span class="option-letter">D</span>
                    <span>{{ $question->d }}</span>
                </div>
            </label>
        </div>
        @else
        <div>
            <textarea name="answers[{{ $question->q_no }}]" 
                      class="short-answer-input" 
                      rows="4" 
                      placeholder="Type your answer here..."
                      oninput="updateProgress()"></textarea>
        </div>
        @endif
    </div>
    @endforeach

    <div class="submit-section">
        <div class="submit-warning">
            ⚠️ Make sure you've answered all questions before submitting. You cannot change your answers after submission!
        </div>
        <button type="submit" class="btn btn-primary" style="padding: 15px 50px; font-size: 18px;"
                onclick="return confirm('Are you sure you want to submit? You cannot change your answers after submission!')">
            📤 Submit Quiz
        </button>
        <div class="navigation-buttons">
            <a href="{{ route('student.dashboard') }}" class="btn btn-secondary"
               onclick="return confirm('Are you sure? Your progress will be lost!')">
                Cancel
            </a>
        </div>
    </div>
</form>

@section('scripts')
<script>
    // Timer functionality
    const duration = @json($quiz->duration ?? 0);
    let timeLeft = duration * 60; // Convert to seconds
    const timerDisplay = document.getElementById('time-left');
    const timerContainer = document.getElementById('quiz-timer');

    function updateTimer() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        timerDisplay.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

        // Warning when 5 minutes left
        if (timeLeft <= 300) {
            timerContainer.classList.add('timer-warning');
        }

        // Auto submit when time is up
        if (timeLeft <= 0) {
            alert('Time is up! Your quiz will be submitted automatically.');
            document.getElementById('quiz-form').submit();
        }

        timeLeft--;
    };

    // Update timer every second
   const timerInterval = setInterval(updateTimer, 1000);
    updateTimer();

    // Progress tracking
    function updateProgress() {
        const totalQuestions = @json($quiz->questions->count());
        let answeredCount = 0;

        const radioGroups = {};
        document.querySelectorAll('input[type="radio"]:checked').forEach(radio => {
            const name = radio.getAttribute('name');
            if (!radioGroups[name]) {
                radioGroups[name] = true;
                answeredCount++;
            }
        });

        document.querySelectorAll('textarea').forEach(textarea => {
            if (textarea.value.trim() !== '') {
                answeredCount++;
            }
        });

        const percentage = (answeredCount / totalQuestions) * 100;
        document.getElementById('progress-fill').style.width = percentage + '%';
        document.getElementById('answered-count').textContent = answeredCount;

        updateQuestionNav();
    }

    function updateQuestionNav() {
        const questions = document.querySelectorAll('.question-container');
        questions.forEach((container, index) => {
            const questionNumber = index + 1;
            const navItem = document.querySelector(`.question-nav-item[data-question="${questionNumber}"]`);
            const radioChecked = container.querySelector('input[type="radio"]:checked');
            const textareaFilled = container.querySelector('textarea')?.value.trim() !== '';

            if (radioChecked || textareaFilled) {
                navItem.classList.add('answered');
            } else {
                navItem.classList.remove('answered');
            }
        });
    }

    function scrollToQuestion(questionNumber) {
        const element = document.getElementById(`question-${questionNumber}`);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            document.querySelectorAll('.question-nav-item').forEach(item => item.classList.remove('current'));
            document.querySelector(`.question-nav-item[data-question="${questionNumber}"]`)
                .classList.add('current');
        }
    }

    window.addEventListener('beforeunload', e => {
        e.preventDefault();
        e.returnValue = '';
    });

    updateProgress();

    // Auto-save functionality (optional)
    setInterval(() => {
        // You can implement auto-save to localStorage here
        console.log('Auto-saving progress...');
    }, 30000); // Every 30 seconds
</script>
@endsection
@endsection