@extends('layouts.app')

@section('title', 'Student Answers')

@section('content')
<div class="container">
    <h2>Student: {{ $student->name }}</h2>
    <h3>Quiz: {{ $quiz->title }}</h3>

    <table>
        <tr>
            <th>Q.No</th>
            <th>Question</th>
            <th>Answer</th>
            <th>Correct Answer</th>
            <th>Evaluation</th>
        </tr>
        @foreach($answers as $ans)
            <tr>
                <td>{{ $ans->q_no }}</td>
                <td>{{ $ans->question->question ?? 'N/A' }}</td>
                <td>{{ $ans->ans ?? 'No Answer' }}</td>
                <td>{{ $ans->question->correct_answer ?? 'N/A' }}</td>
                <td>
                    @if($ans->evaluation === null)
                        Pending
                    @elseif($ans->evaluation)
                        Correct
                    @else
                        Wrong
                    @endif
                </td>
            </tr>
        @endforeach
    </table>
</div>
@endsection
