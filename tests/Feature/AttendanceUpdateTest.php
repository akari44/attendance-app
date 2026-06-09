<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceUpdateTest extends TestCase
{
    use RefreshDatabase;
    // テストケース　ID:11
    // 退勤が出勤より前
    public function test_attendance_invalid_update_clock_time_shows_error()
    {
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $response = $this->actingAs($user)->post('/attendance/detail/' . $attendance->id, [
            'requested_clock_in' => '18:00',
            'requested_clock_out' => '09:00',
            'reason' => '修正理由',
        ]);

        $response->assertSessionHasErrors(['requested_clock_out']);
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }
}
