<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = ['q_id', 'q_no', 'question', 'a', 'b', 'c', 'd', 'correct_answer'];
    public $incrementing = false;
    protected $primaryKey = ['q_id', 'q_no'];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class, 'q_id');
    }
}