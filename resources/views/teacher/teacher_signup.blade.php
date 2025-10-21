@extends('layouts.app')

@section('title', 'Teacher Signup')

@section('content')
<div class="container">
    <h2>Teacher Signup</h2>

    <form action="{{ route('teacher.register') }}" method="POST">
        @csrf
        <label>Name:</label>
        <input type="text" name="name" required>
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Signup</button>
    </form>

    <p>Already have an account? <a href="{{ route('teacher.login') }}">Login here</a></p>
</div>
@endsection
