<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class AttendanceListTest extends TestCase
{
    use RefreshDatabase;
    // テストケース　ID:9
    public function test_show_all_attendances()
    {
        $user = User::factory()->create();

        // 月の境界をまたがない設定
        $date1 = Carbon::now()->startOfMonth()->addDays(14);
        $date2 = Carbon::now()->startOfMonth()->addDays(19);

        Attendance::create([
            'user_id' => $user->id,
            'date' => $date1,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        Attendance::create([
            'user_id' => $user->id,
            'date' => $date2,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertSee($date1->locale('ja')->isoFormat('M月D日(ddd)'));
        $response->assertSee($date2->locale('ja')->isoFormat('M月D日(ddd)'));
    }

    public function test_show_month_now()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertSee(Carbon::today()->locale('ja')->isoFormat('Y年M月'));

    }
    public function test_show_prev_month_attendances()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->subMonth(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $prevMonth = Carbon::now()->subMonth()->format('Y-m');
        $response = $this->actingAs($user)->get('/attendance/list?month=' . $prevMonth);
        $response->assertSee(Carbon::now()->subMonth()->locale('ja')->isoFormat('Y年M月'));
    }
    public function test_show_next_month_attendances()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->addMonth(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $nextMonth = Carbon::now()->addMonth()->format('Y-m');
        $response = $this->actingAs($user)->get('/attendance/list?month=' . $nextMonth);
        $response->assertSee(Carbon::now()->addMonth()->locale('ja')->isoFormat('Y年M月'));
    }
    public function test_attendance_detail_is_recorded_at_attendance_detail()
    {
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $response = $this->actingAs($user)->get('/attendance/detail/' . $attendance->id);
        $response->assertStatus(200);
    }
}
