@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="container">
    <h1>Welcome to the Quiz App</h1>
    <p>Please login or register to start using the platform.</p>

    <div class="links">
        <a href="{{ route('teacher.login') }}">Teacher Login</a> |
        <a href="{{ route('teacher.register') }}">Teacher Signup</a> |
        <a href="{{ route('student.login') }}">Student Login</a> |
        <a href="{{ route('student.register') }}">Student Signup</a>
    </div>
</div>
@endsection
