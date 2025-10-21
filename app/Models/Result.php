<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    protected $fillable = ['s_id', 'q_id', 'score'];
    public $incrementing = false;

    public function student()
    {
        return $this->belongsTo(Student::class, 's_id');
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'q_id');
    }
}