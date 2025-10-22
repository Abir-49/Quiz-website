@extends('layouts.app')

@section('title', 'My Students')

@section('navbar')
<div class="navbar">
    <a href="{{ route('teacher.dashboard') }}" class="navbar-brand">📚 Quiz System</a>
    <div class="navbar-menu">
        <a href="{{ route('teacher.dashboard') }}">Dashboard</a>
        <a href="{{ route('teacher.quizList') }}">My Quizzes</a>
        <a href="{{ route('teacher.students') }}">Students</a>
        <a href="{{ route('teacher.logout') }}" class="logout-btn">Logout</a>
    </div>
</div>
@endsection

@section('content')
<style>
    .students-header {
        margin-bottom: 30px;
    }

    .students-header h1 {
        font-size: 32px;
        color: #333;
        margin-bottom: 10px;
    }

    .student-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.2s;
    }

    .student-card:hover {
        transform: translateX(5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    }

    .student-info {
        flex: 1;
    }

    .student-name {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    .student-details {
        display: flex;
        gap: 20px;
        color: #666;
        font-size: 14px;
        margin-top: 5px;
    }

    .student-actions {
        display: flex;
        gap: 10px;
    }

    .teacher-id-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 10px;
        margin-bottom: 30px;
        text-align: center;
    }

    .teacher-id-value {
        font-size: 36px;
        font-weight: bold;
        margin: 15px 0;
        letter-spacing: 2px;
    }
</style>

<div class="students-header">
    <h1>👥 My Students</h1>
    <p>Manage your classroom students</p>
</div>

<div class="teacher-id-card">
    <h3>Your Teacher ID</h3>
    <div class="teacher-id-value">{{ Session::get('teacher_id') }}</div>
    <p style="opacity: 0.9;">Share this ID with students to let them join your class</p>
</div>

<div class="card">
    <div class="card-header">
        Enrolled Students ({{ $students->total() }})
    </div>

    @if($students->count() > 0)
        @foreach($students as $classRecord)
        <div class="student-card">
            <div class="student-info">
                <div class="student-name">{{ $classRecord->student->name }}</div>
                <div class="student-details">
                    <span><strong>Roll:</strong> {{ $classRecord->student->roll }}</span>
                    <span><strong>Email:</strong> {{ $classRecord->student->email }}</span>
                    <span><strong>Joined:</strong> {{ $classRecord->responded_at->format('M d, Y') }}</span>
                </div>
            </div>
            <div class="student-actions">
                <form action="{{ route('teacher.removeStudent', $classRecord->student->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm" 
                            onclick="return confirm('Remove this student from classroom?')">
                        Remove
                    </button>
                </form>
            </div>
        </div>
        @endforeach

        <div style="margin-top: 20px;">
            {{ $students->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">👥</div>
            <h3>No Students Enrolled Yet</h3>
            <p>Share your Teacher ID with students to let them join your class</p>
        </div>
    @endif
</div>
@endsection