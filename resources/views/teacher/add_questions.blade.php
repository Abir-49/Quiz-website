@extends('layouts.app')

@section('title', 'Add Questions')

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
    .questions-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .quiz-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 30px;
    }

    .quiz-info h1 {
        font-size: 28px;
        margin-bottom: 10px;
    }

    .question-block {
        background: white;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border-left: 4px solid #667eea;
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

    .remove-question-btn {
        background: #dc3545;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
    }

    .remove-question-btn:hover {
        background: #c82333;
    }

    .question-type-selector {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }

    .type-option {
        flex: 1;
        padding: 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        cursor: pointer;
        text-align: center;
        transition: all 0.3s;
    }

    .type-option:hover {
        border-color: #667eea;
    }

    .type-option.active {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .type-option input[type="radio"] {
        display: none;
    }

    .mcq-options {
        display: grid;
        gap: 10px;
        margin-top: 15px;
    }

    .option-row {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .option-label {
        min-width: 80px;
        font-weight: 600;
        color: #333;
    }

    .add-question-btn {
        width: 100%;
        padding: 15px;
        background: #28a745;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 20px;
        transition: background 0.3s;
    }

    .add-question-btn:hover {
        background: #218838;
    }

    .submit-section {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 10px;
        text-align: center;
    }

    .existing-questions {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 30px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .existing-question-item {
        padding: 15px;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .existing-question-item:last-child {
        border-bottom: none;
    }

    .question-content {
        flex: 1;
    }

    .question-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    .question-meta {
        font-size: 13px;
        color: #666;
    }
</style>

<div class="questions-container">
    <div class="quiz-info">
        <h1>📝 {{ $quiz->title }}</h1>
        <p>Duration: {{ $quiz->duration }} minutes | Expires: {{ $quiz->expire_time->format('M d, Y h:i A') }}</p>
    </div>

    @if($quiz->questions->count() > 0)
    <div class="existing-questions">
        <h3 style="margin-bottom: 15px;">✅ Existing Questions ({{ $quiz->questions->count() }})</h3>
        @foreach($quiz->questions as $q)
        <div class="existing-question-item">
            <div class="question-content">
                <div class="question-title">Q{{ $q->q_no }}. {{ $q->question }}</div>
                <div class="question-meta">
                    Type: {{ ucfirst($q->type) }} | Marks: {{ $q->marks }} | 
                    Correct Answer: <strong>{{ $q->correct_answer }}</strong>
                </div>
            </div>
            <form action="{{ route('teacher.deleteQuestion', [$quiz->id, $q->id]) }}" 
                  method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm" 
                        onclick="return confirm('Delete this question?')">
                    Delete
                </button>
            </form>
        </div>
        @endforeach
    </div>
    @endif

    <form action="{{ route('teacher.storeQuestions', $quiz->id) }}" method="POST" id="questionsForm">
        @csrf
        
        <div id="questions-wrapper"></div>

        <button type="button" class="add-question-btn" onclick="addQuestion()">
            ➕ Add Question
        </button>

        <div class="submit-section">
            <p style="margin-bottom: 15px; color: #666;">
                You have added <strong id="question-count">0</strong> new question(s)
            </p>
            <button type="submit" class="btn btn-primary" style="padding: 15px 40px; font-size: 16px;">
                💾 Save All Questions
            </button>
            <a href="{{ route('teacher.dashboard') }}" class="btn btn-secondary" 
               style="padding: 15px 40px; font-size: 16px; margin-left: 10px;">
                Finish Later
            </a>
        </div>
    </form>
</div>

@section('scripts')
<script>
let questionIndex = 0;

function addQuestion() {
    const wrapper = document.getElementById('questions-wrapper');
    const questionBlock = document.createElement('div');
    questionBlock.className = 'question-block';
    questionBlock.id = `question-${questionIndex}`;
    
    questionBlock.innerHTML = `
        <div class="question-header">
            <div class="question-number">Question ${questionIndex + 1}</div>
            <button type="button" class="remove-question-btn" onclick="removeQuestion(${questionIndex})">
                🗑️ Remove
            </button>
        </div>

        <div class="form-group">
            <label>Question Type *</label>
            <div class="question-type-selector">
                <label class="type-option active" onclick="setQuestionType(${questionIndex}, 'mcq', this)">
                    <input type="radio" name="questions[${questionIndex}][type]" value="mcq" checked required>
                    <div style="font-size: 24px; margin-bottom: 5px;">📋</div>
                    <div>Multiple Choice</div>
                </label>
                <label class="type-option" onclick="setQuestionType(${questionIndex}, 'short', this)">
                    <input type="radio" name="questions[${questionIndex}][type]" value="short" required>
                    <div style="font-size: 24px; margin-bottom: 5px;">✍️</div>
                    <div>Short Answer</div>
                </label>
            </div>
        </div>

        <div class="form-group">
            <label>Question Text *</label>
            <textarea name="questions[${questionIndex}][question]" class="form-control" 
                      rows="3" required placeholder="Enter your question here"></textarea>
        </div>

        <div id="mcq-section-${questionIndex}" class="mcq-options">
            <div class="option-row">
                <div class="option-label">Option A:</div>
                <input type="text" name="questions[${questionIndex}][a]" 
                       class="form-control mcq-required" placeholder="Enter option A">
            </div>
            <div class="option-row">
                <div class="option-label">Option B:</div>
                <input type="text" name="questions[${questionIndex}][b]" 
                       class="form-control mcq-required" placeholder="Enter option B">
            </div>
            <div class="option-row">
                <div class="option-label">Option C:</div>
                <input type="text" name="questions[${questionIndex}][c]" 
                       class="form-control mcq-required" placeholder="Enter option C">
            </div>
            <div class="option-row">
                <div class="option-label">Option D:</div>
                <input type="text" name="questions[${questionIndex}][d]" 
                       class="form-control mcq-required" placeholder="Enter option D">
            </div>
        </div>

        <div class="form-group" style="margin-top: 15px;">
            <label>Correct Answer *</label>
            <input type="text" name="questions[${questionIndex}][correct_answer]" 
                   class="form-control" required 
                   placeholder="For MCQ: enter a, b, c, or d. For short answer: enter the correct text">
            <div class="form-help">
                <span id="answer-help-${questionIndex}">Enter the correct option (a, b, c, or d)</span>
            </div>
        </div>

        <div class="form-group">
            <label>Marks *</label>
            <input type="number" name="questions[${questionIndex}][marks]" 
                   class="form-control" value="1" required min="1" max="100"
                   style="max-width: 150px;">
        </div>
    `;
    
    wrapper.appendChild(questionBlock);
    questionIndex++;
    updateQuestionCount();
    
    // Scroll to new question
    questionBlock.scrollIntoView({ behavior: 'smooth', block: 'center' });
}

function removeQuestion(index) {
    const questionBlock = document.getElementById(`question-${index}`);
    if (questionBlock) {
        questionBlock.remove();
        updateQuestionCount();
        renumberQuestions();
    }
}

function setQuestionType(index, type, element) {
    // Update active state
    const parent = element.parentElement;
    parent.querySelectorAll('.type-option').forEach(opt => opt.classList.remove('active'));
    element.classList.add('active');
    
    // Show/hide MCQ options
    const mcqSection = document.getElementById(`mcq-section-${index}`);
    const mcqInputs = mcqSection.querySelectorAll('.mcq-required');
    const answerHelp = document.getElementById(`answer-help-${index}`);
    
    if (type === 'mcq') {
        mcqSection.style.display = 'grid';
        mcqInputs.forEach(input => input.required = true);
        answerHelp.textContent = 'Enter the correct option (a, b, c, or d)';
    } else {
        mcqSection.style.display = 'none';
        mcqInputs.forEach(input => {
            input.required = false;
            input.value = '';
        });
        answerHelp.textContent = 'Enter the exact correct answer text';
    }
}

function updateQuestionCount() {
    const count = document.querySelectorAll('.question-block').length;
    document.getElementById('question-count').textContent = count;
}

function renumberQuestions() {
    const questions = document.querySelectorAll('.question-block');
    questions.forEach((question, index) => {
        const numberDiv = question.querySelector('.question-number');
        if (numberDiv) {
            numberDiv.textContent = `Question ${index + 1}`;
        }
    });
}

// Add first question automatically
document.addEventListener('DOMContentLoaded', function() {
    addQuestion();
});

// Form validation
document.getElementById('questionsForm').addEventListener('submit', function(e) {
    const questionCount = document.querySelectorAll('.question-block').length;
    if (questionCount === 0) {
        e.preventDefault();
        alert('Please add at least one question!');
        return false;
    }
});
</script>
@endsection
@endsection