<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use App\Models\Attendance;
use Carbon\Carbon;

class AdminStaffTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_see_all_staff_name_and_email()
    {
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        User::factory()->create([
            'name' => 'テスト太郎',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
        ]);
        User::factory()->create([
            'name' => 'テスト花子',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
        ]);
        $response = $this->actingAs($admin, 'admin')->get('/admin/staff/list');

        $response->assertSee('テスト太郎');
        $response->assertSee('テスト花子');
        $response->assertSee('user1@example.com');
        $response->assertSee('user2@example.com');
    }
    public function test_admin_can_see_staff_month_attendance()
    {
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $user = User::factory()->create([
            'name' => 'テスト太郎',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::yesterday(),
            'clock_in' => '09:10:00',
            'clock_out' => '18:10:00',
            'status' => '退勤済',
        ]);
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::tomorrow(),
            'clock_in' => '09:20:00',
            'clock_out' => '18:20:00',
            'status' => '退勤済',
        ]);
        $response = $this->actingAs($admin, 'admin')->get('admin/staff/list');
        $response->assertSee('/admin/attendance/staff/' . $user->id);
        $response = $this->actingAs($admin, 'admin')->get('admin/attendance/staff/' . $user->id);
        $response->assertSee('テスト太郎');
        $response->assertSee(Carbon::today()->locale('ja')->isoFormat('M月D日(ddd)'));
        $response->assertSee(Carbon::yesterday()->locale('ja')->isoFormat('M月D日(ddd)'));
        $response->assertSee(Carbon::tomorrow()->locale('ja')->isoFormat('M月D日(ddd)'));
        $response->assertSee('09:00');
        $response->assertSee('18:00');
        $response->assertSee('09:10');
        $response->assertSee('18:10');
        $response->assertSee('09:20');
        $response->assertSee('18:20');
    }

    public function test_show_prev_month_attendances()
    {
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->subMonth(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $prevMonth = Carbon::now()->subMonth()->format('Y-m');
        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/staff/' . $user->id . '?month=' . $prevMonth);
        $response->assertSee(Carbon::now()->subMonth()->locale('ja')->isoFormat('Y年M月'));
    }
    public function test_show_next_month_attendances()
    {
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::now()->addMonth(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $addMonth = Carbon::now()->addMonth()->format('Y-m');
        $response = $this->actingAs($admin, 'admin')->get('/admin/attendance/staff/' . $user->id . '?month=' . $addMonth);
        $response->assertSee(Carbon::now()->addMonth()->locale('ja')->isoFormat('Y年M月'));
    }
    public function test_admin_can_navigate_to_attendance_detail()
    {
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $response = $this->actingAs($admin, 'admin')->get('admin/attendance/staff/' . $user->id);
        $response->assertSee(route('admin.attendance.detail', $attendance->id));

    }
}
