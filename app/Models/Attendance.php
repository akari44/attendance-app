<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'status', 
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes()
    {
        return $this->hasMany(BreakTime::class);
    }
    
    // 日時の表示方法
    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->locale('ja')->isoFormat('M月D日(ddd)');
    }

    public function getClockInAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : '';
    }

    public function getClockOutAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : '';
    }

    // 休憩時間（分）に計算
    public function getTotalBreakMinutesAttribute()
    {
        return $this->breakTimes->sum(function ($break) {
            return Carbon::parse($break->break_start)
                ->diffInMinutes(Carbon::parse($break->break_end));
        });
    }

    // 休憩時間アトリビュート（表示用）
    public function getTotalBreakTimeAttribute()
    {
        $minutes = $this->total_break_minutes;
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    // 勤務時間アトリビュート（表示用）
    public function getTotalWorkTimeAttribute()
    {
        $workMinutes = Carbon::parse($this->clock_in)
            ->diffInMinutes(Carbon::parse($this->clock_out));
        $minutes = $workMinutes - $this->total_break_minutes;
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

}