<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudAnsEval extends Model
{
    protected $fillable = ['s_id', 'q_id', 'q_no', 'ans', 'evaluation','marks_obtained'];
     protected $primaryKey = 'id';
    public $incrementing = true;
protected $casts = [
    'submitted_at' => 'datetime',
];


    public function student()
    {
        return $this->belongsTo(Student::class, 's_id');
    }
     public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'q_id');
    }

    public function question()
    {
        return $this->belongsTo(Question::class, 'q_no', 'q_no');
    }
}