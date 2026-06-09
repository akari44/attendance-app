<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;

class AttendanceUpdateTest extends TestCase
{
    use RefreshDatabase;
    // テストケース　ID:11
    // 最後2件のみ未完了

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
            'requested_clock_out' => '09:00',// 退勤が出勤より前
            'reason' => '修正理由',
        ]);

        $response->assertSessionHasErrors(['requested_clock_out']);
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');
    }

    public function test_attendance_invalid_update_break_start_shows_error()
    {
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);

        $response = $this->actingAs($user)->post('/attendance/detail/' . $attendance->id, [
            'requested_clock_in' => '09:00',
            'requested_clock_out' => '18:00',
            'requested_break_start' => ['19:00'], // 退勤より後
            'requested_break_end' => ['20:00'],
            'reason' => '修正理由',
        ]);

        $response->assertSessionHasErrors(['break_error']);
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);
        $response->assertSee('休憩時間が不適切な値です');
    }

    public function test_attendance_invalid_update_break_end_shows_error()
    {
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);

        $response = $this->actingAs($user)->post('/attendance/detail/' . $attendance->id, [
            'requested_clock_in' => '09:00',
            'requested_clock_out' => '18:00',
            'requested_break_start' => ['12:00'],
            'requested_break_end' => ['19:00'],// 退勤より後
            'reason' => '修正理由',
        ]);

        $response->assertSessionHasErrors(['break_error']);
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);
        $response->assertSee('休憩時間もしくは退勤時間が不適切な値です');
    }

    public function test_attendance_invalid_no_reason_shows_error()
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
            'requested_clock_in' => '09:00',
            'requested_clock_out' => '18:30',
            'reason' => '',
        ]);

        $response->assertSessionHasErrors(['reason']);
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);
        $response->assertSee('備考を記入してください');
    }

    public function test_attendance_update_request_is_corrected()
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
            'requested_clock_in' => '08:00',
            'requested_clock_out' => '19:00',
            'requested_break_start' => ['14:00'],
            'requested_break_end' => ['15:00'],
            'reason' => '修正お願いします',
        ]);

        $this->assertDatabaseHas('attendance_requests', [
            'attendance_id' => $attendance->id,
            'requested_clock_in' => '08:00:00',
            'requested_clock_out' => '19:00:00',
            'reason' => '修正お願いします',
            'status' => '承認待ち',
        ]);
        $this->assertDatabaseHas('break_requests', [
            'requested_break_start' => '14:00:00',
            'requested_break_end' => '15:00:00',
        ]);
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);
        $response->assertSee('08:00');
        $response->assertSee('19:00');
        $response->assertSee('14:00');
        $response->assertSee('15:00');
        $response->assertSee('修正お願いします');

        $response = $this->actingAs($user)->get('/stamp_correction_request/list');
        $response->assertSee('承認待ち');
        $response->assertSee('修正お願いします');
    }

    public function test_all_update_request_is_on_list()
    {
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $this->actingAs($user)->post('/attendance/detail/' . $attendance->id, [
            'requested_clock_in' => '08:00',
            'reason' => '修正お願いします',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-02',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $this->actingAs($user)->post('/attendance/detail/' . $attendance->id, [
            'requested_clock_in' => '08:20',
            'reason' => '修正お願いします',
        ]);

        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-03',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $this->actingAs($user)->post('/attendance/detail/' . $attendance->id, [
            'requested_clock_in' => '08:40',
            'reason' => '修正お願いします',
        ]);

        $response = $this->actingAs($user)->get('/stamp_correction_request/list');
        $response->assertSee('承認待ち');
        $this->assertDatabaseCount('attendance_requests', 3);

    }



}
