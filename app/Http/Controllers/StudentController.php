<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Quiz;
use App\Models\StudAnsEval;
use App\Models\Result;
use App\Models\ClassModel;
use App\Models\Question;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class StudentController extends Controller
{
    // ============ AUTHENTICATION ============
    
    public function showLogin()
    {
        if (Session::has('student_id')) {
            return redirect()->route('student.dashboard');
        }
        return view('student.student_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $student = Student::where('email', $request->email)->first();
        
        if ($student && Hash::check($request->password, $student->password)) {
            Session::put('student_id', $student->id);
            Session::put('student_name', $student->name);
            Session::put('student_roll', $student->roll);
            Session::put('student_email', $student->email);
            return redirect()->route('student.dashboard')->with('success', 'Welcome back!');
        }

        return back()->with('error', 'Invalid credentials')->withInput();
    }

    public function showRegister()
    {
        if (Session::has('student_id')) {
            return redirect()->route('student.dashboard');
        }
        return view('student.student_signup');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'roll' => 'required|string|unique:students,roll',
            'email' => 'required|email|unique:students,email',
            'password' => 'required|min:6|confirmed',
        ]);

        $student = Student::create([
            'name' => $request->name,
            'roll' => $request->roll,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Session::put('student_id', $student->id);
        Session::put('student_name', $student->name);
        Session::put('student_roll', $student->roll);
        Session::put('student_email', $student->email);

        return redirect()->route('student.dashboard')->with('success', 'Registration successful!');
    }

    public function logout()
    {
        Session::forget(['student_id', 'student_name', 'student_roll', 'student_email']);
        return redirect()->route('student.login')->with('success', 'Logged out successfully');
    }

    // ============ DASHBOARD ============
    
    public function dashboard()
    {
        $student_id = Session::get('student_id');
        $student = Student::with(['teachers', 'results'])->find($student_id);

        // Get approved teacher IDs
        $approvedTeacherIds = ClassModel::where('s_id', $student_id)
            ->where('status', 'approved')
            ->pluck('t_id');

        // Total quizzes taken
        $totalQuizTaken = $student->results->count();

        // Average score percentage
        $averageScore = $totalQuizTaken > 0 
            ? round($student->results->avg('percentage'), 2) 
            : 0;

        // Get all quizzes from approved teachers
        $allQuizzes = Quiz::whereIn('t_id', $approvedTeacherIds)
            ->where('expire_time', '>=', Carbon::now('Asia/Dhaka'))
            ->with('teacher')
            ->get();

        // Get taken quiz IDs
        $takenQuizIds = $student->results->pluck('q_id')->toArray();

        // Available quizzes (not taken yet)
        $availableQuizzes = $allQuizzes->whereNotIn('id', $takenQuizIds);

        // Quizzes taken with results
        $quizzesTaken = Result::where('s_id', $student_id)
            ->with('quiz.teacher')
            ->orderBy('submitted_at', 'desc')
            ->take(5)
            ->get();

        // Total missed quizzes (expired but not taken)
        $missedQuizzes = Quiz::whereIn('t_id', $approvedTeacherIds)
            ->where('expire_time', '<', Carbon::now('Asia/Dhaka'))
            ->whereNotIn('id', $takenQuizIds)
            ->count();

        // Subscribed teachers
        $subscribedTeachers = Teacher::whereIn('id', $approvedTeacherIds)
            ->withCount('quizzes')
            ->get();

        // Pending class requests
        $pendingRequests = ClassModel::where('s_id', $student_id)
            ->where('status', 'pending')
            ->with('teacher')
            ->get();

        return view('student.student_dashboard', compact(
            'student',
            'totalQuizTaken',
            'averageScore',
            'availableQuizzes',
            'quizzesTaken',
            'missedQuizzes',
            'subscribedTeachers',
            'pendingRequests'
        ));
    }

    // ============ CLASS MANAGEMENT ============
    
    public function showJoinClass()
    {
        return view('student.join_class');
    }

    public function searchTeachers(Request $request)
    {
        $student_id = Session::get('student_id');
        $search = $request->get('search', '');

        $teachers = Teacher::when($search, function($query) use ($search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('id', $search);
            })
            ->withCount('students')
            ->paginate(10);

        // Add join status for each teacher
        foreach ($teachers as $teacher) {
            $classRecord = ClassModel::where('t_id', $teacher->id)
                ->where('s_id', $student_id)
                ->first();
            
            $teacher->join_status = $classRecord ? $classRecord->status : null;
        }

        return view('student.search_teachers', compact('teachers', 'search'));
    }

    public function requestJoin(Request $request)
    {
        $student_id = Session::get('student_id');
        
        $request->validate([
            'teacher_id' => 'required|exists:teachers,id',
        ]);

        $teacher_id = $request->teacher_id;

        // Check if already exists
        $existing = ClassModel::where('s_id', $student_id)
            ->where('t_id', $teacher_id)
            ->first();

        if ($existing) {
            if ($existing->status === 'pending') {
                return back()->with('info', 'Your join request is already pending');
            } elseif ($existing->status === 'approved') {
                return back()->with('info', 'You are already enrolled in this teacher\'s class');
            } else {
                // Rejected - allow to request again
                $existing->status = 'pending';
                $existing->requested_at = Carbon::now('Asia/Dhaka');
                $existing->responded_at = null;
                $existing->save();
                return back()->with('success', 'Join request sent again');
            }
        }

        ClassModel::create([
            't_id' => $teacher_id,
            's_id' => $student_id,
            'status' => 'pending',
            'requested_at' => Carbon::now('Asia/Dhaka'),
        ]);

        return back()->with('success', 'Join request sent successfully');
    }

    public function cancelRequest($teacher_id)
    {
        $student_id = Session::get('student_id');

        ClassModel::where('t_id', $teacher_id)
            ->where('s_id', $student_id)
            ->where('status', 'pending')
            ->delete();

        return back()->with('success', 'Request cancelled successfully');
    }

    public function leaveClass($teacher_id)
    {
        $student_id = Session::get('student_id');

        ClassModel::where('t_id', $teacher_id)
            ->where('s_id', $student_id)
            ->delete();

        return back()->with('success', 'You have left the class');
    }

    public function myTeachers()
    {
        $student_id = Session::get('student_id');

        $teachers = ClassModel::where('s_id', $student_id)
            ->where('status', 'approved')
            ->with('teacher')
            ->orderBy('responded_at', 'desc')
            ->paginate(10);

        return view('student.my_teachers', compact('teachers'));
    }

    // ============ QUIZ TAKING ============
    
    public function takeQuiz($quiz_id)
    {
        $student_id = Session::get('student_id');
        
        $quiz = Quiz::with('questions')->findOrFail($quiz_id);

        // Check if student is approved for this teacher
        $isApproved = ClassModel::where('t_id', $quiz->t_id)
            ->where('s_id', $student_id)
            ->where('status', 'approved')
            ->exists();

        if (!$isApproved) {
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'You are not enrolled in this teacher\'s class');
        }

        // Check if quiz is still active
        if ($quiz->expire_time < Carbon::now('Asia/Dhaka')) {
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'This quiz has expired');
        }

        // Check if already taken
        $alreadyTaken = Result::where('s_id', $student_id)
            ->where('q_id', $quiz_id)
            ->exists();

        if ($alreadyTaken) {
            return redirect()
                ->route('student.quiz_result', $quiz_id)
                ->with('info', 'You have already taken this quiz');
        }

        // Check if quiz has questions
        if ($quiz->questions->count() === 0) {
            return redirect()
                ->route('student.dashboard')
                ->with('error', 'This quiz has no questions yet');
        }

        return view('student.take_quiz', compact('quiz'));
    }

    public function submitQuiz(Request $request, $quiz_id)
    {
        $student_id = Session::get('student_id');
        
        $quiz = Quiz::with('questions')->findOrFail($quiz_id);

        // Check if already submitted
        $alreadySubmitted = Result::where('s_id', $student_id)
            ->where('q_id', $quiz_id)
            ->exists();

        if ($alreadySubmitted) {
            return redirect()
                ->route('student.quiz_result', $quiz_id)
                ->with('info', 'Quiz already submitted');
        }

        $totalScore = 0;
        $totalMarks = 0;
        $correctAnswers = 0;
        $wrongAnswers = 0;

        foreach ($quiz->questions as $question) {
            $totalMarks += $question->marks;
            $studentAnswer = $request->answers[$question->q_no] ?? null;

            $isCorrect = false;
            $marksObtained = 0;

            if ($studentAnswer !== null) {
                // For MCQ, direct comparison
                if ($question->type === 'mcq') {
                    $isCorrect = (strtolower(trim($studentAnswer)) === strtolower(trim($question->correct_answer)));
                } else {
                    // For short answers, case-insensitive comparison
                    $isCorrect = (strtolower(trim($studentAnswer)) === strtolower(trim($question->correct_answer)));
                }

                if ($isCorrect) {
                    $marksObtained = $question->marks;
                    $totalScore += $marksObtained;
                    $correctAnswers++;
                } else {
                    $wrongAnswers++;
                }
            } else {
                // Unanswered
            }

            // Store student answer
            StudAnsEval::create([
                's_id' => $student_id,
                'q_id' => $quiz_id,
                'q_no' => $question->q_no,
                'ans' => $studentAnswer,
                'evaluation' => $isCorrect,
                'marks_obtained' => $marksObtained,
            ]);
        }

        // Calculate percentage
        $percentage = $totalMarks > 0 ? ($totalScore / $totalMarks) * 100 : 0;

        // Store result
        Result::create([
            's_id' => $student_id,
            'q_id' => $quiz_id,
            'score' => $totalScore,
            'total_marks' => $totalMarks,
            'percentage' => round($percentage, 2),
            'submitted_at' => Carbon::now('Asia/Dhaka'),
        ]);

        return redirect()
            ->route('student.quiz_result', $quiz_id)
            ->with('success', 'Quiz submitted successfully!');
    }

    public function viewQuizResult($quiz_id)
    {
        $student_id = Session::get('student_id');
        
        $quiz = Quiz::with('questions')->findOrFail($quiz_id);
        
        $result = Result::where('s_id', $student_id)
            ->where('q_id', $quiz_id)
            ->firstOrFail();

        $answers = StudAnsEval::where('s_id', $student_id)
            ->where('q_id', $quiz_id)
            ->get()
            ->keyBy('q_no');

        return view('student.quiz_result', compact('quiz', 'result', 'answers'));
    }

    public function myResults()
    {
        $student_id = Session::get('student_id');
        
        $results = Result::where('s_id', $student_id)
            ->with('quiz.teacher')
            ->orderBy('submitted_at', 'desc')
            ->paginate(10);

        return view('student.my_results', compact('results'));
    }
}