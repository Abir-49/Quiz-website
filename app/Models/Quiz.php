<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['t_id', 'title', 'duration', 'creation_time', 'expire_time'];
protected $casts = [
    'expire_time' => 'datetime',
];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 't_id');
    }

    public function questions()
    {
        return $this->hasMany(Question::class, 'q_id');
    }

    public function results()
    {
        return $this->hasMany(Result::class, 'q_id');
    }
     public function students()
    {
        return $this->belongsToMany(Student::class, 'results', 'q_id', 's_id')->withPivot('score');
    }
}