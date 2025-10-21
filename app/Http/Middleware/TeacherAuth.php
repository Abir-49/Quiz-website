<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TeacherAuth
{
    public function handle(Request $request, Closure $next)
    {
        if (!session()->has('teacher_id')) {
            return redirect()->route('teacher.login')->with('error', 'Please login first');
        }
        return $next($request);
    }
}