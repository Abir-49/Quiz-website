<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>@yield('title') | Quiz App</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav>
        <ul>
            @if(session()->has('teacher_id'))
                <li><a href="{{ route('teacher.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('teacher.logout') }}">Logout</a></li>
            @elseif(session()->has('student_id'))
                <li><a href="{{ route('student.dashboard') }}">Dashboard</a></li>
                <li><a href="{{ route('student.logout') }}">Logout</a></li>
            @else
                <li><a href="{{ route('home') }}">Home</a></li>
                <li><a href="{{ route('teacher.login') }}">Teacher Login</a></li>
                <li><a href="{{ route('student.login') }}">Student Login</a></li>
            @endif
        </ul>
    </nav>

    <div class="container">
        @if(session('success'))
            <p style="color:green">{{ session('success') }}</p>
        @endif
        @if(session('error'))
            <p style="color:red">{{ session('error') }}</p>
        @endif

        @yield('content')
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
