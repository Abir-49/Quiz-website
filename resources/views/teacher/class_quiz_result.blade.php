@extends('layouts.app')

@section('title', 'Quiz Results')

@section('content')
<div class="container">
    <h2>Quiz Results: {{ $quiz->title }}</h2>
    <p>Total Participants: {{ $participants->count() }}</p>
    <p>Absent Students: {{ $absent_students->count() }}</p>
    <p>Average Score: {{ $average_score }}%</p>

    <h3>All Participant Results</h3>
    <table>
        <tr>
            <th>Student</th>
            <th>Score</th>
            <th>Action</th>
        </tr>
        @foreach($participants as $student)
        <tr>
            <td>{{ $student->name }}</td>
            <td>{{ $student->pivot->score ?? 0 }}</td>
            <td>
                <a href="{{ route('teacher.student_answers', ['quiz_id'=>$quiz->id, 'student_id'=>$student->id]) }}">View Answers</a>
            </td>
        </tr>
        @endforeach
    </table>

    <form action="{{ route('teacher.download_results', $quiz->id) }}" method="POST">
        @csrf
        <button type="submit">Download CSV</button>
    </form>
</div>
@endsection
