<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'start_work_time',
        'end_work_time',
        'total_work_time'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function scopeUserDateSearch($query, $user_id)
    {
        if (!empty($user_id)) {
            $query->where('user_id', $user_id)
            ->where('date', date('Y-m-d'));
        }
    }

    public function scopeEndWorkSearch($query, $attendance_id)
    {
        if (!empty($attendance_id)) {
            $query->where('id', $attendance_id)
            ->where('end_work_time', "!=", null);
        }
    }
}
