@extends('layouts.app')

@section('title', 'Student Login')

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
            <div class="auth-header-icon">👨‍🎓</div>
            <h2>Student Login</h2>
            <p>Welcome back! Please login to your account</p>
        </div>

        <form action="{{ route('student.login') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="{{ old('email') }}" required autofocus 
                       placeholder="Enter your email">
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" 
                       class="form-control" required 
                       placeholder="Enter your password">
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%;">
                Login
            </button>
        </form>

        <div class="auth-links">
            <p>Don't have an account? 
                <a href="{{ route('student.register') }}">Sign up here</a>
            </p>
            <p style="margin-top: 10px;">
                <a href="{{ route('home') }}">← Back to Home</a>
            </p>
        </div>
    </div>
</div>
@endsection