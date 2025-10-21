@extends('layouts.app')

@section('title', 'Teacher Login')

@section('content')
<div class="container">
    <h2>Teacher Login</h2>

    <form action="{{ route('teacher.login') }}" method="POST">
        @csrf
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Password:</label>
        <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>

    <p>Don't have an account? <a href="{{ route('teacher.register') }}">Signup here</a></p>
</div>
@endsection
