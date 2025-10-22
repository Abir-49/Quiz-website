@extends('layouts.app')

@section('title', 'My Teachers')

@section('navbar')
<div class="navbar">
    <a href="{{ route('student.dashboard') }}" class="navbar-brand">📚 Quiz System</a>
    <div class="navbar-menu">
        <a href="{{ route('student.dashboard') }}">Dashboard</a>
        <a href="{{ route('student.myTeachers') }}" class="active">My Teachers</a>
        <a href="{{ route('student.myResults') }}">My Results</a>
        <a href="{{ route('student.join_class_form') }}" class="btn btn-success">Join New Class</a>
        <a href="{{ route('student.logout') }}" class="logout-btn">Logout</a>
    </div>
</div>
@endsection

@section('content')
<style>
    .teacher-card {
        background: white;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.2s;
    }

    .teacher-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
    }

    .teacher-info-section {
        flex: 1;
    }

    .teacher-name {
        font-size: 22px;
        font-weight: 600;
        color: #333;
        margin-bottom: 10px;
    }

    .teacher-details {
        display: flex;
        gap: 20px;
        color: #666;
        font-size: 14px;
        margin-top: 8px;
    }

    .teacher-actions {
        display: flex;
        gap: 10px;
    }

    .teacher-actions form button {
        padding: 8px 14px;
        border: none;
        border-radius: 6px;
        color: white;
        cursor: pointer;
        transition: 0.2s;
        font-size: 14px;
    }

    .view-btn {
        background: #007bff;
    }
    .view-btn:hover {
        background: #0056b3;
    }

    .leave-btn {
        background: #dc3545;
    }
    .leave-btn:hover {
        background: #b71c1c;
    }

    .no-teachers {
        padding: 30px;
        text-align: center;
        color: #777;
        font-size: 16px;
    }
</style>

<div style="margin-bottom: 30px;">
    <h1 style="font-size: 32px; color: #333; margin-bottom: 10px;">👥 My Teachers</h1>
    <p>Teachers you are currently enrolled with</p>
</div>

<div class="card">
    <div class="card-header" style="font-weight: 600; font-size: 18px;">
        Enrolled Teachers ({{ $teachers->total() }})
    </div>

    @if($teachers->count() > 0)
        @foreach($teachers as $classRecord)
            @php
                $teacher = $classRecord->teacher ?? null;
            @endphp

            @if($teacher)
            <div class="teacher-card">
                <div class="teacher-info-section">
                    <div class="teacher-name">{{ $teacher->name }}</div>
                    <div class="teacher-details">
                        <span>📧 {{ $teacher->email }}</span>
                        <span>🆔 Class Code: {{ $teacher->id }}</span>
                        <span>📅 Joined: {{ optional($classRecord->responded_at ?? $classRecord->created_at)->format('M d, Y') }}</span>
                    </div>
                </div>

                <div class="teacher-actions">
                    <form action="{{ route('student.leaveClass', $teacher->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to leave this class?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="leave-btn">Leave</button>
                    </form>
                </div>
            </div>
            @endif
        @endforeach

        <div style="margin-top: 20px;">
            {{ $teachers->links() }}
        </div>
    @else
        <div class="no-teachers">
            You are not enrolled in any classes yet.<br>
            <a href="{{ route('join_class_form') }}" class="btn btn-success" style="margin-top: 10px;">Join New Class</a>
        </div>
    @endif
</div>
@endsection
