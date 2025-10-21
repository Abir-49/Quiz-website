@extends('layouts.app')

@section('title', 'Student Signup')

@section('content')
<div class="container">
    <h2>Student Signup</h2>

    <form action="{{ route('student.register') }}" method="POST">
        @csrf
        <label>Name:</label>
        <input type="text" name="name" required>
        <label>Roll:</label>
        <input type="text" name="roll" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Signup</button>
    </form>

    <p>Already have an account? <a href="{{ route('student.login') }}">Login here</a></p>
</div>
@endsection
