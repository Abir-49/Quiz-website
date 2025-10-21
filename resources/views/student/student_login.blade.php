@extends('layouts.app')

@section('title', 'Student Login')

@section('content')
<div class="container">
    <h2>Student Login</h2>

    <form action="{{ route('student.login') }}" method="POST">
        @csrf
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="{{ route('student.register') }}">Signup here</a></p>
</div>
@endsection
