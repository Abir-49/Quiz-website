<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudAnsEval extends Model
{
    protected $fillable = ['s_id', 'q_id', 'q_no', 'ans', 'evaluation'];
    public $incrementing = false;
    protected $primaryKey = ['s_id', 'q_id', 'q_no'];

    public function student()
    {
        return $this->belongsTo(Student::class, 's_id');
    }
}