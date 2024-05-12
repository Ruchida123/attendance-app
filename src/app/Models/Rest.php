<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'date',
        'start_rest_time',
        'end_rest_time',
        'total_rest_time'
    ];

    public function scopeAttendanceDateSearch($query, $attendance_id)
    {
        if (!empty($attendance_id)) {
            $query->where('attendance_id', $attendance_id)
            ->where('date', date('Y-m-d'));
        }
    }

    public function scopeStartRestAtteSearch($query, $attendance_id)
    {
        if (!empty($attendance_id)) {
            $query->where('attendance_id', $attendance_id)
            ->where('date', date('Y-m-d'))
            ->where('end_rest_time', null);
        }
    }

    public function scopeStartRestSearch($query, $rest_id)
    {
        if (!empty($rest_id)) {
            $query->where('id', $rest_id)
            ->where('date', date('Y-m-d'))
            ->where('end_rest_time', null);
        }
    }
}
