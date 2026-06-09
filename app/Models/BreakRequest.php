<?php

namespace App\Models;
use Carbon\Carbon;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BreakRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'attendance_request_id',
        'requested_break_start',
        'requested_break_end'
    ];

    public function attendanceRequest()
    {
        return $this->belongsTo(AttendanceRequest::class);
    }


    // 時間の表示方法
    public function getRequestedBreakStartAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : '';
    }

    public function getRequestedBreakEndAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : '';
    }

}

