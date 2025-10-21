@extends('layouts.app')

@section('title', 'Create Quiz')

@section('content')
<div class="container">
    <h2>Create New Quiz</h2>

    <form action="{{ route('teacher.make_quiz') }}" method="POST">
        @csrf
        <label>Title:</label>
        <input type="text" name="title" required>
        <label>Duration (minutes):</label>
        <input type="number" name="duration" required>
        <label>Expire Time:</label>
        <input type="datetime-local" name="expire_time">

        <h3>Questions</h3>
        <div id="questions-container">
            <div class="question-block">
                <label>Question:</label>
                <input type="text" name="questions[0][question]" required>
                <label>Option A:</label>
                <input type="text" name="questions[0][a]" required>
                <label>Option B:</label>
                <input type="text" name="questions[0][b]" required>
                <label>Option C:</label>
                <input type="text" name="questions[0][c]" required>
                <label>Option D:</label>
                <input type="text" name="questions[0][d]" required>
                <label>Correct Answer:</label>
                <input type="text" name="questions[0][correct_answer]" required>
            </div>
        </div>

        <button type="button" id="add-question">Add More Questions</button>
        <button type="submit">Create Quiz</button>
    </form>
</div>

<script>
let questionIndex = 1;
document.getElementById('add-question').addEventListener('click', function() {
    const container = document.getElementById('questions-container');
    const html = `
    <div class="question-block">
        <label>Question:</label>
        <input type="text" name="questions[${questionIndex}][question]" required>
        <label>Option A:</label>
        <input type="text" name="questions[${questionIndex}][a]" required>
        <label>Option B:</label>
        <input type="text" name="questions[${questionIndex}][b]" required>
        <label>Option C:</label>
        <input type="text" name="questions[${questionIndex}][c]" required>
        <label>Option D:</label>
        <input type="text" name="questions[${questionIndex}][d]" required>
        <label>Correct Answer:</label>
        <input type="text" name="questions[${questionIndex}][correct_answer]" required>
    </div>`;
    container.insertAdjacentHTML('beforeend', html);
    questionIndex++;
});
</script>
