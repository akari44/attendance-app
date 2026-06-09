<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AttendanceDetailTest extends TestCase
{
    use RefreshDatabase;
    // テストケース　ID:10
    public function test_attendance_detail_shows_user_name()
    {
        $user = User::create([
            'name' => 'テスト太郎',
            'email' => 'user1@example.com',
            'password' => Hash::make('password')
        ]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);
        $response->assertSee($user->name);
    }

    public function test_attendance_detail_shows_date()
    {
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-15',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);
        $response->assertSee('2026年');
        $response->assertSee('6月15日');
    }

    public function test_attendance_detail_shows_clock_time()
    {
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-15',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);
        $response->assertSee('09:00');
        $response->assertSee('18:00');
    }

    public function test_attendance_detail_shows_break_time()
    {
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-15',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);
        $response->assertSee('12:00');
        $response->assertSee('13:00');
    }
}
