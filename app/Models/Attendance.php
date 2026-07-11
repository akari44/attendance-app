<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Attendance extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_out',
        'status',
        'comment',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function breakTimes(): HasMany
    {
        return $this->hasMany(BreakTime::class);
    }

    public function attendanceRequest(): HasOne
    {
        return $this->hasOne(AttendanceRequest::class);
    }

    // Blade表示用: M月D日(曜日)形式にフォーマット
    public function getDateAttribute($value)
    {
        return Carbon::parse($value)->locale('ja')->isoFormat('M月D日(ddd)');
    }

    // Blade表示用: H:i形式にフォーマット（nullの場合は空文字）
    public function getClockInAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : '';
    }

    // Blade表示用: H:i形式にフォーマット（nullの場合は空文字
    public function getClockOutAttribute($value)
    {
        return $value ? Carbon::parse($value)->format('H:i') : '';
    }

    public function getTotalBreakMinutesAttribute()
    {
        return $this->breakTimes->sum(function ($break) {
            return Carbon::parse($break->break_start)
                ->diffInMinutes(Carbon::parse($break->break_end));
        });
    }

    public function getTotalBreakTimeAttribute()
    {
        $minutes = $this->total_break_minutes;
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    public function getTotalWorkTimeAttribute()
    {
        $workMinutes = Carbon::parse($this->clock_in)
            ->diffInMinutes(Carbon::parse($this->clock_out));
        $minutes = $workMinutes - $this->total_break_minutes;
        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }

    // API Resourceのwhenloaded('applications')と対応させるため
    public function applications(): HasOne
    {
        return $this->hasOne(AttendanceRequest::class);
    }



}