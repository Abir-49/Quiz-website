@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Welcome, {{ Session::get('student_name') }}</h2>

    <p>Select an option below:</p>

    {{-- Join Classroom Section --}}
   <a href="{{ route('student.join_class_form') }}">Join a Classroom</a>


   {{-- Take Quiz Section --}}
<div class="card" style="margin: 20px 0; padding: 20px; border:1px solid #ccc;">
    <h3>Available Quizzes</h3>

    @if($availableQuizzes->isEmpty())
        <p>No quizzes available yet.</p>
    @else
        <table border="1" cellpadding="8">
            <tr>
                <th>Quiz Title</th>
                <th>Teacher</th>
                <th>Action</th>
            </tr>
            @foreach($availableQuizzes as $quiz)
            <tr>
                <td>{{ $quiz->title }}</td>
                <td>{{ $quiz->teacher->name }}</td>
                <td>
                    <a href="{{ route('student.take_quiz', $quiz->id) }}">Take Quiz</a>
                </td>
            </tr>
            @endforeach
        </table>
    @endif
</div>

</div>
@endsection
