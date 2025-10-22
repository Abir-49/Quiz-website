@extends('layouts.app')

@section('title', 'Teacher Signup')

@section('content')
<style>
    .auth-container {
        max-width: 450px;
        margin: 50px auto;
    }

    .auth-card {
        background: white;
        border-radius: 15px;
        padding: 40px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }

    .auth-header {
        text-align: center;
        margin-bottom: 30px;
    }

    .auth-header-icon {
        font-size: 64px;
        margin-bottom: 15px;
    }

    .auth-header h2 {
        font-size: 28px;
        color: #333;
        margin-bottom: 10px;
    }

    .auth-header p {
        color: #666;
    }

    .auth-links {
        text-align: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .auth-links a {
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
    }

    .auth-links a:hover {
        text-decoration: underline;
    }
</style>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <div class="auth-header-icon">👨‍🏫</div>
            <h2>Teacher Signup</h2>
            <p>Create your account to get started</p>
        </div>

        <form action="{{ route('teacher.register') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" 
                       value="{{ old('name') }}" required autofocus 
                       placeholder="Enter your full name">
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="{{ old('email') }}" required 
                       placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" 
                       class="form-control" required 
                       placeholder="Enter password (min 6 characters)">
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input type="password" id="password_confirmation" 
                       name="password_confirmation" class="form-control" required 
                       placeholder="Confirm your password">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Create Account
            </button>
        </form>

        <div class="auth-links">
            <p>Already have an account? 
                <a href="{{ route('teacher.login') }}">Login here</a>
            </p>
            <p style="margin-top: 10px;">
                <a href="{{ route('home') }}">← Back to Home</a>
            </p>
        </div>
    </div>
</div>
@endsection