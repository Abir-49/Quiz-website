@extends('layouts.app')

@section('title', 'Leaderboard')

@section('content')
<div class="container">
    <h2>Leaderboard</h2>

    <table>
        <tr>
            <th>Rank</th>
            <th>Student</th>
            <th>Average Score (%)</th>
        </tr>

        @foreach($leaderboard as $index => $student)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $student->name }}</td>
            <td>{{ $student->average_score }}</td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
