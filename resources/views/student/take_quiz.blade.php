@extends('layouts.app')

@section('title', 'Take Quiz')

@section('content')
<div class="container">
    <h2>{{ $quiz->title }}</h2>
    <form action="{{ route('student.submit_quiz',$quiz->id) }}" method="POST">
        @csrf
        @foreach($quiz->questions as $question)
            <div class="question-block">
                <p>{{ $question->q_no }}. {{ $question->question }}</p>
                <input type="radio" name="answers[{{ $question->q_no }}]" value="a" required> {{ $question->a }} <br>
                <input type="radio" name="answers[{{ $question->q_no }}]" value="b"> {{ $question->b }} <br>
                <input type="radio" name="answers[{{ $question->q_no }}]" value="c"> {{ $question->c }} <br>
                <input type="radio" name="answers[{{ $question->q_no }}]" value="d"> {{ $question->d }} <br>
            </div>
        @endforeach
        <button type="submit">Submit Quiz</button>
    </form>
</div>
@endsection
