<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    protected $fillable = ['name', 'roll', 'email', 'password'];
    protected $hidden = ['password'];

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'classes', 's_id', 't_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function results()
    {
        return $this->hasMany(Result::class, 's_id');
    }
    public function quizzesTaken()
    {
        return $this->belongsToMany(Quiz::class, 'results', 's_id', 'q_id')->withPivot('score');
    }
    public function answers()
    {
        return $this->hasMany(StudAnsEval::class, 's_id');
    }
}