<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['q_id', 'q_no', 'question', 'a', 'b', 'c', 'd', 'correct_answer'];
     protected $primaryKey = 'id';
    public $incrementing = true;
protected $casts = [
    'submitted_at' => 'datetime',
];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'q_id');
    }
     public function answers()
    {
        return $this->hasMany(StudAnsEval::class, 'q_id');
    }
}