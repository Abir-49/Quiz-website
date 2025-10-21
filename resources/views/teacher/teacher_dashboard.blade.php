@extends('layouts.app')

@section('title', 'Teacher Dashboard')

@section('content')
<div class="container">
    <h2>Welcome, {{ session('teacher_name') ?? '' }}</h2>

    <div class="stats">
        <p>Total Quizzes: {{ $total_quizzes }}</p>
        <p>Finished Quizzes: {{ $finished_quizzes->count() }}</p>
        <p>Pending Quizzes: {{ $pending_quizzes->count() }}</p>
    </div>

    <h3>Classroom Students</h3>
    <table>
        <tr><th>Name</th><th>Email</th><th>Status</th></tr>
        @foreach($students as $student)
        <tr>
            <td>{{ $student->name }}</td>
            <td>{{ $student->email }}</td>
            <td>{{ $student->pivot->status }}</td>
        </tr>
        @endforeach
    </table>

    <h3>Student Requests</h3>
    <table>
        <tr><th>Name</th><th>Email</th><th>Action</th></tr>
        @foreach($requests as $req)
        <tr>
            <td>{{ $req->student->name }}</td>
            <td>{{ $req->student->email }}</td>
            <td>
                <a href="{{ route('teacher.request.approve',$req->id) }}">Approve</a> |
                <a href="{{ route('teacher.request.reject',$req->id) }}">Reject</a>
            </td>
        </tr>
        @endforeach
    </table>

    <a href="{{ route('teacher.make_quiz') }}">Create New Quiz</a>
</div>
@endsection
