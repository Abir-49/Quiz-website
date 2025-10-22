<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = ['s_id', 'q_id', 'score', 'submitted_at','total_marks','percentage'];
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
}