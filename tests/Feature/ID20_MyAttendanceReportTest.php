<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ID20_MyAttendanceReportTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_guest_cannot_access_report_page(): void
    {
        $response = $this->get('/attendance/report');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_see_report_stats(): void
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-01',
            'clock_in' => '09:30:00',//遅刻
            'clock_out' => '18:30:00',
            'status' => '退勤済',
        ]);
        Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-02',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-06-03',
            'clock_in' => '08:00:00',
            'clock_out' => '20:00:00',
            'status' => '退勤済',//長時間労働
        ]);
        Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-07-01',
            'clock_in' => '09:30:00',//遅刻
            'clock_out' => '18:30:00',
            'status' => '退勤済',
        ]);
        Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-07-02',
            'clock_in' => '08:30:00',
            'clock_out' => '17:30:00',//早退
            'status' => '退勤済',
        ]);
        Attendance::create([
            'user_id' => $user->id,
            'date' => '2026-07-03',
            'clock_in' => '08:00:00',
            'clock_out' => '20:00:00',
            'status' => '退勤済',//長時間労働
        ]);
        $response = $this->actingAs($user)->get('/attendance/report');
        $response->assertSee('60h 0m');//総労働時間
        $response->assertSee('12h 0m');//残業時間
        $response->assertSee('10h 0m');//平均労働時間
        $response->assertSee('2026-06');//月次推移（月）
        $response->assertSee('2026-07');//月次推移（月）
        $response->assertSee('30h 0m');//月次推移（労働時間）
        $response->assertSee('6h 0m');//月次推移（残業時間）
        $response->assertSee('2回');//異常検知（遅刻回数）
        $response->assertSee('1回');//異常検知（早退回数）
        $response->assertSee('2日');//異常検知（長時間労働日数）
    }

    public function test_report_page_shows_safely_without_attendance_data(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/attendance/report');
        $response->assertStatus(200);
    }
}

