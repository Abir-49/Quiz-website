@extends('layouts.app')

@section('title', 'Quiz Result')

@section('content')
<div class="container">
    <h2>Quiz Result: {{ $quiz->title }}</h2>
    <p>Your Score: {{ $result->score }} / {{ $quiz->questions->count() }}</p>

    <h3>Questions and Correct Answers</h3>
    <ul>
        @foreach($quiz->questions as $q)
        <li>
            {{ $q->q_no }}. {{ $q->question }} <br>
            Correct Answer: {{ $q->correct_answer }}
        </li>
        @endforeach
    </ul>
</div>
@endsection
