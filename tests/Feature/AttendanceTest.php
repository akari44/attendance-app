<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

// ステータス確認機能
class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_datetime_display()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee(Carbon::now()->locale('ja')->isoFormat('Y年M月D日'));
        $response->assertSee(Carbon::now()->format('H:i'));
    }

    public function test_status_shows_not_working()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('勤務外');
    }

    public function test_status_shows_working()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => '09:00:00',
            'status' => '出勤中',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }
    public function test_status_shows_on_break()
    {
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => '09:00:00',
            'status' => '休憩中',
        ]);
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::now()->format('H:i'),
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('休憩中');
    }

    public function test_status_shows_finished()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => '09:00:00',
            'clock_out' => Carbon::now()->format('H:i'),
            'status' => '退勤済',
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('退勤済');
    }

    // 出勤機能
    public function test_clock_in_button_works()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('勤務外');

        $response = $this->actingAs($user)->post('/attendance', [
            'action' => 'clock_in',
        ]);
        $response->assertRedirect('/attendance');

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
    }

    public function test_clock_in_only_once_per_day()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => '09:00:00',
            'clock_out' => Carbon::now()->format('H:i'),
            'status' => '退勤済',
        ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertDontSee('出勤');
    }

    public function test_clock_in_time_recorded_in_list()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertStatus(200);
        $response->assertSee('勤務外');

        $response = $this->actingAs($user)->post('/attendance', [
            'action' => 'clock_in',
        ]);
        $response->assertRedirect('/attendance');

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertSee(Carbon::now()->format('H:i'));
    }

    // 休憩機能
    public function test_break_start_button_work()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->format('H:i'),
            'status' => '出勤中',
        ]);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩入');

        $response = $this->actingAs($user)->post('/attendance', [
            'action' => 'break_start',
        ]);
        $response->assertRedirect('/attendance');

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩中');
    }
    public function test_user_can_break_start_any_time()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->format('H:i:s'),
            'status' => '出勤中',
        ]);
        $this->actingAs($user)->post('/attendance', ['action' => 'break_start']);
        $this->actingAs($user)->post('/attendance', ['action' => 'break_end']);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩入');
    }
    public function test_break_end_button_work()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->format('H:i'),
            'status' => '出勤中',
        ]);
        $this->actingAs($user)->post('/attendance', ['action' => 'break_start']);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('休憩戻');

        $response = $this->actingAs($user)->post('/attendance', [
            'action' => 'break_end',
        ]);
        $response->assertRedirect('/attendance');

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('出勤中');
    }
    public function test_user_can_break_end_any_time()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->format('H:i:s'),
            'status' => '出勤中',
        ]);
        $this->actingAs($user)->post('/attendance', ['action' => 'break_start']);
        $this->actingAs($user)->post('/attendance', ['action' => 'break_end']);
        $this->actingAs($user)->post('/attendance', ['action' => 'break_start']);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertSee('休憩戻');
    }
    public function test_break_times_recorded_in_list()
    {
        $user = User::factory()->create();
        $attendance = Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->format('H:i:s'),
            'status' => '出勤中',
        ]);
        BreakTime::create([
            'attendance_id' => $attendance->id,
            'break_start' => Carbon::now()->subHour()->format('H:i:s'),
            'break_end' => Carbon::now()->format('H:i:s'),
        ]);
        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertSee('01:00');
    }

    // 退勤機能
    public function test_clock_out_button_work()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->format('H:i:s'),
            'status' => '出勤中',
        ]);

        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('退勤');

        $this->actingAs($user)->post('/attendance', ['action' => 'clock_out']);
        $response = $this->actingAs($user)->get('/attendance');
        $response->assertSee('お疲れ様でした。');
    }

    public function test_clock_out_time_recorded_in_list()
    {
        $user = User::factory()->create();
        Attendance::create([
            'user_id' => $user->id,
            'date' => Carbon::today(),
            'clock_in' => Carbon::now()->subHour()->format('H:i:s'),
            'status' => '出勤中',
        ]);

        $this->actingAs($user)->get('/attendance');
        $this->actingAs($user)->post('/attendance', ['action' => 'clock_out']);

        $response = $this->actingAs($user)->get('/attendance/list');
        $response->assertSee(Carbon::now()->format('H:i'));

    }



}