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

class TeacherController extends Controller
{
    //Authentication
    public function showLogin()
    {
        return view('teacher.teacher_login');
    }

    public function login(Request $request)
    {
        $teacher = Teacher::where('email', $request->email)->first();
        if ($teacher && Hash::check($request->password, $teacher->password)) {
            Session::put('teacher_id', $teacher->id);
            return redirect()->route('teacher.dashboard');
        }
        return back()->with('error', 'Invalid credentials');
    }

    public function showRegister()
    {
        return view('teacher.teacher_signup');
    }

    public function register(Request $request)
    {
        $teacher = Teacher::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        Session::put('teacher_id', $teacher->id);
        return redirect()->route('teacher.dashboard');
    }

    public function logout()
    {
        Session::forget('teacher_id');
        return redirect()->route('teacher.login');
    }

    //Dashboard
    public function dashboard()
    {
        $teacher_id = Session::get('teacher_id');
        $teacher = Teacher::with('quizzes', 'students')->find($teacher_id);

        $total_quizzes = $teacher->quizzes->count();
        $finished_quizzes = $teacher->quizzes()->where('expire_time','<', now())->get();
        $pending_quizzes = $teacher->quizzes()->where('expire_time','>=', now())->get();
        $students = $teacher->students;
        $requests = ClassModel::where('t_id', $teacher_id)->where('status','pending')->get();

        return view('teacher.teacher_dashboard', compact(
            'total_quizzes','finished_quizzes','pending_quizzes','students','requests'
        ));
    }

    //Make Quiz
    public function showMakeQuiz()
    {
        return view('teacher.make_quiz');
    }

    public function storeQuiz(Request $request)
    {
        $teacher_id = Session::get('teacher_id');

        $quiz = Quiz::create([
            't_id' => $teacher_id,
            'title' => $request->title,
            'duration' => $request->duration,
            'creation_time' => now(),
            'expire_time' => $request->expire_time,
        ]);

        // Add questions
        foreach ($request->questions as $q_no => $q) {
            Question::create([
                'quiz_id' => $quiz->id,
                'q_no' => $q_no + 1,
                'question' => $q['question'],
                'a' => $q['a'],
                'b' => $q['b'],
                'c' => $q['c'],
                'd' => $q['d'],
                'correct_answer' => $q['correct_answer'],
            ]);
        }

        return redirect()->route('teacher.dashboard')->with('success','Quiz created successfully');
    }

    //View Quiz Results
    public function viewQuizResults($quiz_id)
    {
        $quiz = Quiz::with('results.student','questions')->find($quiz_id);
        $total_students = $quiz->teacher->students->count();
        $participants = $quiz->results->count();
        $absent = $total_students - $participants;
        $average = $participants ? round($quiz->results->avg('score'),2) : 0;

        return view('teacher.class_quiz_result', compact('quiz','participants','absent','average'));
    }

    //Approve/Reject Class Request
    public function approveRequest($id)
    {
        $request = ClassModel::find($id);
        $request->status = 'approved';
        $request->save();
        return back()->with('success','Student approved');
    }

    public function rejectRequest($id)
    {
        $request = ClassModel::find($id);
        $request->status = 'rejected';
        $request->save();
        return back()->with('success','Student rejected');
    }

    //View Student Answer Script
    public function viewStudentAnswers($quiz_id, $student_id)
    {
        $answers = StudAnsEval::where('q_id',$quiz_id)->where('s_id',$student_id)->get();
        $student = Student::find($student_id);
        $quiz = Quiz::find($quiz_id);

        return view('teacher.view_student_answers', compact('answers','student','quiz'));
    }
}
