@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Join a Classroom</h2>

    @if(session('success'))
        <p style="color: green;">{{ session('success') }}</p>
    @elseif(session('error'))
        <p style="color: red;">{{ session('error') }}</p>
    @elseif(session('info'))
        <p style="color: orange;">{{ session('info') }}</p>
    @endif

    <form method="POST" action="{{ route('student.join_class') }}">
        @csrf
        <label for="teacher_id">Teacher ID:</label>
        <input type="text" name="teacher_id" id="teacher_id" required placeholder="Enter teacher ID">
        <br><br>
        <button type="submit">Send Join Request</button>
    </form>

    <br>
    <a href="{{ route('student.dashboard') }}">← Back to Dashboard</a>
</div>
@endsection
