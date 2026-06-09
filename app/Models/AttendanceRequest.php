<?php

namespace App\Models;
use Carbon\Carbon;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        'attendance_id',
        'reason',
        'status',
        'requested_clock_in',
        'requested_clock_out'
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class);
    }

    public function breakRequests()
    {
        return $this->hasMany(BreakRequest::class);
    }

    // 時間の表示方法
    public function getRequestedClockInAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : '';
    }

    public function getRequestedClockOutAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : '';
    }

}
