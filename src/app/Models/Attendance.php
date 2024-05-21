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
        'total_work_time',
        'state'
    ];

    public function user(){
        return $this->belongsTo('App\Models\User');
    }

    public function rests(){
        return $this->hasMany('App\Models\Rest');
    }

    public function scopeUserTodaySearch($query, $user_id)
    {
        if (!empty($user_id)) {
            $query->where('user_id', $user_id)
            ->where('date', date('Y-m-d'));
        }
    }

    public function scopeDateSearch($query, $date)
    {
        if (!empty($date)) {
            $query->where('date', $date);
        }
    }

}
