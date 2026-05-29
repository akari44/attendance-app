<?php

namespace Tests\Feature;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AdminAttendanceTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_see_all_attendances()
    {
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $user1 = User::factory()->create([
            'name' => 'テスト太郎',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
        ]);
        $attendance1 = Attendance::create([
            'user_id' => $user1->id,
            'date' => Carbon::today(),
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'status' => '退勤済',
        ]);
        $user2 = User::factory()->create([
            'name' => 'テスト花子',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
        ]);
        $attendance2 = Attendance::create([
            'user_id' => $user2->id,
            'date' => Carbon::today(),
            'clock_in' => '09:30:00',
            'clock_out' => '18:30:00',
            'status' => '退勤済',
        ]);
        // 1時間休憩
        BreakTime::create([
            'attendance_id' => $attendance1->id,
            'break_start' => '12:00:00',
            'break_end' => '13:00:00',
        ]);
        // 30分休憩
        BreakTime::create([
            'attendance_id' => $attendance2->id,
            'break_start' => '12:30:00',
            'break_end' => '13:00:00',
        ]);



        $response = $this->actingAs($admin, 'admin')->get('admin/attendance/list');

        $response->assertSee('テスト太郎');
        $response->assertSee('テスト花子');
        $response->assertSee('09:00');  // 太郎の出勤
        $response->assertSee('18:00');  // 太郎の退勤
        $response->assertSee('09:30');  // 花子の出勤
        $response->assertSee('18:30');  // 花子の退勤
        $response->assertSee('01:00');  // 太郎の休憩（1時間）
        $response->assertSee('00:30');  // 花子の休憩（30分）
    }

    public function test_show_date_now()
    {
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($admin, 'admin')->get('admin/attendance/list');
        $response->assertSee(Carbon::today()->format('Y/m/d'));
    }
    public function test_show_yesterday_attendances()
    {
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/list?date=' . Carbon::yesterday()->format('Y-m-d'));
        $response->assertSee(Carbon::yesterday()->format('Y/m/d'));
    }
    public function test_show_next_day_attendances()
    {
        $admin = Admin::create([
            'name' => 'テスト管理者',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $response = $this->actingAs($admin, 'admin')
            ->get('/admin/attendance/list?date=' . Carbon::tomorrow()->format('Y-m-d'));
        $response->assertSee(Carbon::tomorrow()->format('Y/m/d'));
    }
}
