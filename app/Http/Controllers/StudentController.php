<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Quiz;
use App\Models\StudAnsEval;
use App\Models\Result;
use App\Models\ClassModel;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class StudentController extends Controller
{
    //Authentication 
    public function showLogin()
    {
        return view('student.student_login');
    }

    public function login(Request $request)
    {
        $student = Student::where('email', $request->email)->first();
        if ($student && Hash::check($request->password, $student->password)) {
            Session::put('student_id', $student->id);
            return redirect()->route('student.dashboard');
        }
        return back()->with('error','Invalid credentials');
    }

    public function showRegister()
    {
        return view('student.student_signup');
    }

    public function register(Request $request)
    {
        $student = Student::create([
            'name' => $request->name,
            'roll' => $request->roll,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        Session::put('student_id', $student->id);
        return redirect()->route('student.dashboard');
    }

    public function logout()
    {
        Session::forget('student_id');
        return redirect()->route('student.login');
    }

    //Dashboard
    public function dashboard()
    {
        $student_id = Session::get('student_id');
        $student = Student::with('teachers','results')->find($student_id);

        $total_quiz_taken = $student->results->count();
        $average_score = $total_quiz_taken ? round($student->results->avg('score'),2) : 0;

        $pending_quizzes = Quiz::whereHas('teacher', function($q) use($student_id){
            $q->whereHas('students', function($q2) use($student_id){
                $q2->where('s_id',$student_id)->where('status','approved');
            });
        })->whereDoesntHave('results', function($q) use($student_id){
            $q->where('s_id',$student_id);
        })->get();

        $taken_quizzes = $student->quizzesTaken;

        $class_requests = ClassModel::where('s_id', $student_id)->where('status','pending')->get();

        return view('student.student_dashboard', compact(
            'total_quiz_taken','average_score','pending_quizzes','taken_quizzes','class_requests'
        ));
    }

    //Join Teacher Class
    public function requestJoin($teacher_id)
    {
        $student_id = Session::get('student_id');
        $exists = ClassModel::where('t_id',$teacher_id)->where('s_id',$student_id)->first();
        if (!$exists) {
            ClassModel::create(['t_id'=>$teacher_id, 's_id'=>$student_id]);
            return back()->with('success','Request sent');
        }
        return back()->with('error','You already requested or joined this teacher');
    }

    //Take Quiz
    public function takeQuiz($quiz_id)
    {
        $quiz = Quiz::with('questions')->find($quiz_id);
        return view('student.take_quiz', compact('quiz'));
    }

    public function submitQuiz(Request $request, $quiz_id)
    {
        $student_id = Session::get('student_id');

        foreach($request->answers as $q_no => $answer) {
            StudAnsEval::updateOrCreate(
                ['s_id'=>$student_id,'q_id'=>$quiz_id,'q_no'=>$q_no],
                ['ans'=>$answer,'evaluation'=>null]
            );
        }

        // calculate score
        $quiz = Quiz::with('questions')->find($quiz_id);
        $score = 0;
        foreach ($quiz->questions as $q) {
            $ans = $request->answers[$q->q_no] ?? null;
            if ($ans !== null && $ans == $q->correct_answer) $score++;
        }

        Result::updateOrCreate(
            ['s_id'=>$student_id, 'q_id'=>$quiz_id],
            ['score'=>$score]
        );

        return redirect()->route('student.dashboard')->with('success','Quiz submitted');
    }

    //View Quiz Result
    public function viewQuizResult($quiz_id)
    {
        $student_id = Session::get('student_id');
        $quiz = Quiz::with('questions')->find($quiz_id);
        $result = Result::where('s_id',$student_id)->where('q_id',$quiz_id)->first();
        return view('student.quiz_result', compact('quiz','result'));
    }
}
