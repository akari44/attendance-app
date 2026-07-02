<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class ID13_AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_detail_shows_correct_data(): void
    {
        $user = User::factory()->create();
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);

        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/' . $attendance->id);

        $response->assertSee('2026年');
        $response->assertSee('6月1日');
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_admin_detail_invalid_clock_time_shows_error(): void
    {
        $user = User::factory()->create();
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $response = $this->actingAs($admin, 'admin')->put('/admin/attendance/' . $attendance->id, [
            'requested_clock_in' => '19:00',
            'requested_clock_out' => '18:00',
            'reason' => '修正お願いします',
        ]);

        $response->assertSessionHasErrors(['requested_clock_out']);
        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/' . $attendance->id);
        $response->assertSee('出勤時間もしくは退勤時間が不適切な値です');

    }

    public function test_admin_detail_invalid_break_start_shows_error(): void
    {
        $user = User::factory()->create();
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);
        $response = $this->actingAs($admin, 'admin')->put('/admin/attendance/' . $attendance->id, [
            'requested_clock_in' => '09:00',
            'requested_clock_out' => '18:00',
            'requested_break_start' => ['19:00'], // 退勤より後
            'requested_break_end' => ['13:00'],
            'reason' => '修正理由',
        ]);

        $response->assertSessionHasErrors(['break_error']);
        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/' . $attendance->id);
        $response->assertSee('休憩時間が不適切な値です');

    }

    public function test_admin_detail_invalid_break_end_shows_error(): void
    {
        $user = User::factory()->create();
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);
        $response = $this->actingAs($admin, 'admin')->put('/admin/attendance/' . $attendance->id, [
            'requested_clock_in' => '09:00',
            'requested_clock_out' => '18:00',
            'requested_break_start' => ['12:00'],
            'requested_break_end' => ['19:00'],
            // 退勤より後
            'reason' => '修正理由',
        ]);
        $response->assertSessionHasErrors(['break_error']);
        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/' . $attendance->id);
        $response->assertSee('休憩時間もしくは退勤時間が不適切な値です');
    }

    public function test_admin_detail_no_reason_shows_error(): void
    {
        $user = User::factory()->create();
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $response = $this->actingAs($admin, 'admin')->put('/admin/attendance/' . $attendance->id, [
            'requested_clock_in' => '09:30',
            'reason' => '',
        ]);

        $response->assertSessionHasErrors(['reason']);
        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/' . $attendance->id);
        $response->assertSee('備考を記入してください');
    }
}
