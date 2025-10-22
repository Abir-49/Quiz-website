<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Quiz;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\StudAnsEval;
use App\Models\Result;
use App\Models\Question;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class TeacherController extends Controller
{
    // ============ AUTHENTICATION ============
    
    public function showLogin()
    {
        if (Session::has('teacher_id')) {
            return redirect()->route('teacher.dashboard');
        }
        return view('teacher.teacher_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $teacher = Teacher::where('email', $request->email)->first();
        
        if ($teacher && Hash::check($request->password, $teacher->password)) {
            Session::put('teacher_id', $teacher->id);
            Session::put('teacher_name', $teacher->name);
            Session::put('teacher_email', $teacher->email);
            return redirect()->route('teacher.dashboard')->with('success', 'Welcome back!');
        }

        return back()->with('error', 'Invalid credentials')->withInput();
    }

    public function showRegister()
    {
        if (Session::has('teacher_id')) {
            return redirect()->route('teacher.dashboard');
        }
        return view('teacher.teacher_signup');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:teachers,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $teacher = Teacher::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Session::put('teacher_id', $teacher->id);
        Session::put('teacher_name', $teacher->name);
        Session::put('teacher_email', $teacher->email);

        return redirect()->route('teacher.dashboard')->with('success', 'Registration successful!');
    }

    public function logout()
    {
        Session::forget(['teacher_id', 'teacher_name', 'teacher_email']);
        return redirect()->route('teacher.login')->with('success', 'Logged out successfully');
    }

    // ============ DASHBOARD ============
    
    public function dashboard()
    {
        $teacher_id = Session::get('teacher_id');
        $teacher = Teacher::with(['quizzes', 'students'])->find($teacher_id);

        $total_quizzes = $teacher->quizzes->count();
        
        $finished_quizzes = $teacher->quizzes()
            ->where('expire_time', '<', now())
            ->withCount('results')
            ->orderBy('expire_time', 'desc')
            ->take(5)
            ->get();
        
        $pending_quizzes = $teacher->quizzes()
            ->where('expire_time', '>=', now())
            ->withCount('results')
            ->orderBy('expire_time', 'asc')
            ->take(5)
            ->get();
        
        $students = $teacher->students()
            ->wherePivot('status', 'approved')
            ->withCount('results')
            ->take(10)
            ->get();

        $requests = ClassModel::where('t_id', $teacher_id)
            ->where('status', 'pending')
            ->with('student')
            ->orderBy('requested_at', 'desc')
            ->get();

        $total_students = ClassModel::where('t_id', $teacher_id)
            ->where('status', 'approved')
            ->count();

        return view('teacher.teacher_dashboard', compact(
            'total_quizzes',
            'finished_quizzes',
            'pending_quizzes',
            'students',
            'requests',
            'teacher',
            'total_students'
        ));
    }

    // ============ QUIZ MANAGEMENT ============
    
    public function showMakeQuiz()
    {
        return view('teacher.make_quiz');
    }

    public function storeQuiz(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'duration' => 'required|integer|min:1|max:300',
            'expire_time' => 'required|date|after:now',
        ]);

        $teacher_id = Session::get('teacher_id');

        $quiz = Quiz::create([
            't_id' => $teacher_id,
            'title' => $request->title,
            'duration' => $request->duration,
            'creation_time' => now(),
            'expire_time' => $request->expire_time,
        ]);

        return redirect()
            ->route('teacher.addQuestions', ['quiz' => $quiz->id])
            ->with('success', 'Quiz created! Now add questions.');
    }

    public function showAddQuestions($quiz_id)
    {
        $teacher_id = Session::get('teacher_id');
        
        $quiz = Quiz::where('id', $quiz_id)
            ->where('t_id', $teacher_id)
            ->with('questions')
            ->firstOrFail();

        return view('teacher.add_questions', compact('quiz'));
    }

    public function storeQuestions(Request $request, $quiz_id)
    {
        $teacher_id = Session::get('teacher_id');
        
        $quiz = Quiz::where('id', $quiz_id)
            ->where('t_id', $teacher_id)
            ->firstOrFail();

        $request->validate([
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required|string',
            'questions.*.type' => 'required|in:mcq,short',
            'questions.*.correct_answer' => 'required|string',
            'questions.*.marks' => 'required|integer|min:1',
        ]);

        foreach ($request->questions as $index => $q) {
            Question::create([
                'q_id' => $quiz->id,
                'q_no' => $index + 1,
                'question' => $q['question'],
                'type' => $q['type'],
                'a' => $q['a'] ?? null,
                'b' => $q['b'] ?? null,
                'c' => $q['c'] ?? null,
                'd' => $q['d'] ?? null,
                'correct_answer' => $q['correct_answer'],
                'marks' => $q['marks'],
            ]);
        }

        return redirect()
            ->route('teacher.dashboard')
            ->with('success', 'Questions added successfully. Quiz is now active!');
    }

    public function deleteQuestion($quiz_id, $question_id)
    {
        $teacher_id = Session::get('teacher_id');
        
        $quiz = Quiz::where('id', $quiz_id)
            ->where('t_id', $teacher_id)
            ->firstOrFail();

        Question::where('id', $question_id)
            ->where('q_id', $quiz_id)
            ->delete();

        return back()->with('success', 'Question deleted successfully');
    }

    public function viewQuizList()
    {
        $teacher_id = Session::get('teacher_id');
        
        $quizzes = Quiz::where('t_id', $teacher_id)
            ->withCount(['questions', 'results'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('teacher.quiz_list', compact('quizzes'));
    }

    public function deleteQuiz($quiz_id)
    {
        $teacher_id = Session::get('teacher_id');
        
        $quiz = Quiz::where('id', $quiz_id)
            ->where('t_id', $teacher_id)
            ->firstOrFail();

        $quiz->delete();

        return redirect()
            ->route('teacher.dashboard')
            ->with('success', 'Quiz deleted successfully');
    }

    // ============ QUIZ RESULTS ============
    
    public function viewQuizResults($quiz_id)
    {
        $teacher_id = Session::get('teacher_id');
        
        $quiz = Quiz::where('id', $quiz_id)
            ->where('t_id', $teacher_id)
            ->with(['results.student', 'questions'])
            ->firstOrFail();

        // Get all approved students
        $allStudentIds = ClassModel::where('t_id', $teacher_id)
            ->where('status', 'approved')
            ->pluck('s_id');

        $participants = $quiz->results;
        $participantIds = $participants->pluck('s_id');
        
        // Absent students
        $absentStudentIds = $allStudentIds->diff($participantIds);
        $absentStudents = Student::whereIn('id', $absentStudentIds)->get();

        // Calculate statistics
        $totalParticipants = $participants->count();
        $absentCount = $absentStudents->count();
        $averagePercentage = $totalParticipants > 0 
            ? round($participants->avg('percentage'), 2) 
            : 0;

        return view('teacher.class_quiz_result', compact(
            'quiz',
            'participants',
            'absentStudents',
            'totalParticipants',
            'absentCount',
            'averagePercentage'
        ));
    }

    public function downloadResults($quiz_id)
    {
        $teacher_id = Session::get('teacher_id');
        
        $quiz = Quiz::where('id', $quiz_id)
            ->where('t_id', $teacher_id)
            ->with(['results.student'])
            ->firstOrFail();

        $filename = "quiz_{$quiz_id}_results_" . date('Y-m-d') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($quiz) {
            $file = fopen('php://output', 'w');
            
            // Header row
            fputcsv($file, ['Student Name', 'Roll', 'Email', 'Score', 'Total Marks', 'Percentage', 'Submitted At']);

            // Data rows
            foreach ($quiz->results as $result) {
                fputcsv($file, [
                    $result->student->name,
                    $result->student->roll,
                    $result->student->email,
                    $result->score,
                    $result->total_marks,
                    $result->percentage . '%',
                    $result->submitted_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function viewStudentAnswers($quiz_id, $student_id)
    {
        $teacher_id = Session::get('teacher_id');
        
        $quiz = Quiz::where('id', $quiz_id)
            ->where('t_id', $teacher_id)
            ->with('questions')
            ->firstOrFail();

        $student = Student::findOrFail($student_id);
        
        $result = Result::where('q_id', $quiz_id)
            ->where('s_id', $student_id)
            ->firstOrFail();

        $answers = StudAnsEval::where('q_id', $quiz_id)
            ->where('s_id', $student_id)
            ->get()
            ->keyBy('q_no');

        return view('teacher.view_student_answers', compact(
            'quiz',
            'student',
            'result',
            'answers'
        ));
    }

    // ============ CLASS MANAGEMENT ============
    
    public function approveRequest($id)
    {
        $teacher_id = Session::get('teacher_id');
        
        $request = ClassModel::where('id', $id)
            ->where('t_id', $teacher_id)
            ->where('status', 'pending')
            ->firstOrFail();

        $request->status = 'approved';
        $request->responded_at = now();
        $request->save();

        return back()->with('success', 'Student approved successfully');
    }

    public function rejectRequest($id)
    {
        $teacher_id = Session::get('teacher_id');
        
        $request = ClassModel::where('id', $id)
            ->where('t_id', $teacher_id)
            ->where('status', 'pending')
            ->firstOrFail();

        $request->status = 'rejected';
        $request->responded_at = now();
        $request->save();

        return back()->with('info', 'Request rejected');
    }

    public function viewStudents()
    {
        $teacher_id = Session::get('teacher_id');
        
        $students = ClassModel::where('t_id', $teacher_id)
            ->where('status', 'approved')
            ->with('student')
            ->orderBy('responded_at', 'desc')
            ->paginate(15);

        return view('teacher.students_list', compact('students'));
    }

    public function removeStudent($student_id)
    {
        $teacher_id = Session::get('teacher_id');
        
        ClassModel::where('t_id', $teacher_id)
            ->where('s_id', $student_id)
            ->delete();

        return back()->with('success', 'Student removed from classroom');
    }
}