<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    protected $table = 'classes';
    protected $fillable = ['t_id', 's_id', 'status'];
     protected $primaryKey = 'id';
    public $incrementing = true;
    protected $casts = [
    'submitted_at' => 'datetime',
];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 't_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 's_id');
    }
}