<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Teacher extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password'];
protected $casts = [
    'submitted_at' => 'datetime',
];

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 't_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'classes', 't_id', 's_id')
                    ->withPivot('status')
                    ->withTimestamps();
    }
    public function classRequests()
    {
        return $this->hasMany(ClassModel::class, 't_id')->where('status', 'pending');
    }
}