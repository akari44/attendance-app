<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

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
       $attendance = Attendance::create([
            'user_id'  => $user->id,
            'date'     => Carbon::today(),
            'clock_in' => '09:00:00',
            'status'   => '出勤中',
        ]);

        $response = $this->actingAs($user)->get('/attendance');

        $response->assertStatus(200);
        $response->assertSee('出勤中');
    }
}