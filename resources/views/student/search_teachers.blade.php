@extends('layouts.app')

@section('title', 'Search Teachers')

@section('navbar')
<div class="navbar">
    <a href="{{ route('student.dashboard') }}" class="navbar-brand">📚 Quiz System</a>
    <div class="navbar-menu">
        <a href="{{ route('student.dashboard') }}">Dashboard</a>
        <a href="{{ route('student.myTeachers') }}">My Teachers</a>
        <a href="{{ route('student.myResults') }}">My Results</a>
        <a href="{{ route('student.logout') }}" class="logout-btn">Logout</a>
    </div>
</div>
@endsection

@section('content')
<style>
    .search-section {
        margin-bottom: 30px;
    }

    .search-form {
        display: flex;
        gap: 10px;
        max-width: 600px;
        margin: 0 auto;
    }

    .search-input {
        flex: 1;
        padding: 15px;
        border: 2px solid #ddd;
        border-radius: 8px;
        font-size: 16px;
    }

    .search-input:focus {
        outline: none;
        border-color: #667eea;
    }

    .search-btn {
        padding: 15px 30px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
    }

    .search-btn:hover {
        background: #5568d3;
    }

    .teacher-list-item {
        background: white;
        border-radius: 10px;
        padding: 25px;
        margin-bottom: 15px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .teacher-details {
        flex: 1;
    }

    .teacher-name {
        font-size: 22px;
        font-weight: 600;
        color: #333;
        margin-bottom: 8px;
    }

    .teacher-info {
        display: flex;
        gap: 20px;
        color: #666;
        font-size: 14px;
    }

    .teacher-stats {
        text-align: center;
        margin: 0 30px;
    }

    .stat-number {
        font-size: 28px;
        font-weight: bold;
        color: #667eea;
    }

    .stat-text {
        font-size: 12px;
        color: #666;
    }

    .join-section {
        min-width: 150px;
        text-align: right;
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        display: inline-block;
    }

    .status-approved {
        background: #d4edda;
        color: #155724;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }
</style>

<div class="card">
    <div class="card-header">
        🔍 Search for Teachers
    </div>

    <div class="search-section">
        <form action="{{ route('student.search_teachers') }}" method="GET" class="search-form">
            <input type="text" name="search" class="search-input" 
                   placeholder="Search by name, email, or Teacher ID..." 
                   value="{{ $search }}">
            <button type="submit" class="search-btn">🔍 Search</button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        @if($search)
            Search Results for "{{ $search }}"
        @else
            All Teachers
        @endif
    </div>

    @if($teachers->count() > 0)
        @foreach($teachers as $teacher)
        <div class="teacher-list-item">
            <div class="teacher-details">
                <div class="teacher-name">{{ $teacher->name }}</div>
                <div class="teacher-info">
                    <span>📧 {{ $teacher->email }}</span>
                    <span>🆔 Teacher ID: <strong>{{ $teacher->id }}</strong></span>
                </div>
            </div>

            <div class="teacher-stats">
                <div class="stat-number">{{ $teacher->students_count }}</div>
                <div class="stat-text">Students</div>
            </div>

            <div class="join-section">
                @if($teacher->join_status === 'approved')
                    <span class="status-badge status-approved">✓ Enrolled</span>
                    <form action="{{ route('student.leaveClass', $teacher->id) }}" method="POST" style="margin-top: 10px;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Leave this class?')">
                            Leave Class
                        </button>
                    </form>
                @elseif($teacher->join_status === 'pending')
                    <span class="status-badge status-pending">⏳ Pending</span>
                    <form action="{{ route('student.cancelRequest', $teacher->id) }}" method="POST" style="margin-top: 10px;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-secondary btn-sm">
                            Cancel Request
                        </button>
                    </form>
                @elseif($teacher->join_status === 'rejected')
                    <span class="status-badge status-rejected">✗ Rejected</span>
                    <form action="{{ route('student.join_class') }}" method="POST" style="margin-top: 10px;">
                        @csrf
                        <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                        <button type="submit" class="btn btn-primary btn-sm">
                            Request Again
                        </button>
                    </form>
                @else
                    <form action="{{ route('student.join_class') }}" method="POST">
                        @csrf
                        <input type="hidden" name="teacher_id" value="{{ $teacher->id }}">
                        <button type="submit" class="btn btn-primary">
                            Join Class
                        </button>
                    </form>
                @endif
            </div>
        </div>
        @endforeach

        <div style="margin-top: 20px;">
            {{ $teachers->appends(['search' => $search])->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">🔍</div>
            <h3>No Teachers Found</h3>
            <p>Try searching with different keywords</p>
        </div>
    @endif
</div>
@endsection