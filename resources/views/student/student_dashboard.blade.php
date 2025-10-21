@extends('layouts.app')

@section('title', 'Student Dashboard')

@section('content')
<div class="container">
    <h2>Welcome, {{ session('student_name') ?? '' }}</h2>

    <div class="stats">
        <p>Total Quizzes Taken: {{ $total_quiz_taken }}</p>
        <p>Average Score: {{ $average_score }}%</p>
    </div>

    <h3>Pending Quizzes</h3>
    <ul>
        @foreach($pending_quizzes as $quiz)
        <li>
            {{ $quiz->title }} - <a href="{{ route('student.take_quiz',$quiz->id) }}">Take Quiz</a>
        </li>
        @endforeach
    </ul>

    <h3>Quizzes Taken</h3>
    <ul>
        @foreach($taken_quizzes as $quiz)
        <li>
            {{ $quiz->title }} - Score: {{ $quiz->pivot->score }}% 
            - <a href="{{ route('student.quiz_result',$quiz->id) }}">View Result</a>
        </li>
        @endforeach
    </ul>

    <h3>Class Join Requests</h3>
    <ul>
        @foreach($class_requests as $req)
        <li>
            Teacher: {{ $req->teacher->name }} - Status: {{ $req->status }}
        </li>
        @endforeach
    </ul>
</div>
@endsection
