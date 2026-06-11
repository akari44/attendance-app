<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminAttendanceUpdaeTest extends TestCase
{
    use RefreshDatabase;
    // テストケース　ID:15
    public function test_admin_all_pending_requests_showed()
    {
        $user = User::factory()->create();
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        
        foreach (['2026-06-01', '2026-06-02', '2026-06-03'] as $date) {
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $this->actingAs($user)->post('/attendance/detail/' . $attendance->id, [
            'requested_clock_in' => '08:00',
            'reason' => '修正お願いします',
        ]);}

        $response = $this->actingAs($admin, 'admin')->get('/stamp_correction_request/list?tab=pending');

        $response->assertSee('2026/06/01');
        $response->assertSee('2026/06/02');
        $response->assertSee('2026/06/03');
    
    }

    public function test_admin_all_approved_requests_showed(){
        $user = User::factory()->create();
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        
        foreach (['2026-06-01', '2026-06-02', '2026-06-03'] as $date) {
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => $date,
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $this->actingAs($user)->post('/attendance/detail/' . $attendance->id, [
            'requested_clock_in' => '08:00',
            'reason' => '修正お願いします',
        ]);

        $attendance->refresh();
        $this->actingAs($admin, 'admin')->put('/admin/stamp_correction_request/approve/' . $attendance->attendanceRequest->id);
        }

        $response = $this->actingAs($admin, 'admin')->get('/stamp_correction_request/list?tab=approved');

        $response->assertSee('2026/06/01');
        $response->assertSee('2026/06/02');
        $response->assertSee('2026/06/03');
    }
    
    public function test_admin_correction_request_detail_is_showed(){
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

        $response = $this->actingAs($user)->post('/attendance/detail/' . $attendance->id, [
            'requested_clock_in' => '08:00',
            'requested_clock_out' => '19:00',
            'requested_break_start' => ['14:00'],
            'requested_break_end' => ['15:00'],
            'reason' => '修正お願いします',
        ]);
        $attendance->refresh();
        $response = $this->actingAs($admin, 'admin')->get('/admin/stamp_correction_request/approve/' . $attendance->attendanceRequest->id);

        $response->assertSee($user->name);
        $response->assertSee('2026年');
        $response->assertSee('6月1日');
        $response->assertSee('08:00');
        $response->assertSee('19:00');
        $response->assertSee('14:00');
        $response->assertSee('15:00');
        $response->assertSee('修正お願いします');
    } 
    public function test_admin_approval_process_works(){
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
        $this->actingAs($user)->post('/attendance/detail/' . $attendance->id, [
            'requested_clock_in' => '08:00',
            'reason' => '修正お願いします',
        ]);

        $attendance->refresh();
        $this->actingAs($admin, 'admin')->put('/admin/stamp_correction_request/approve/' . $attendance->attendanceRequest->id);

        $attendance->refresh();
        $this->assertDatabaseHas('attendance_requests', [
            'id' => $attendance->attendanceRequest->id,
            'status' => '承認済み',
            'requested_clock_in'=>'08:00'
        ]);

    }
}
