<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ID17_ApiAttendanceRecordTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_can_get_attendance_list_with_pagination(): void
    {
        $user = User::factory()->create();
        Attendance::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/v1/attendance-records');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ]);
    }

    public function test_can_get_attendance_detail(): void
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create(['user_id' => $user->id]);

        $response = $this->getJson("/api/v1/attendance-records/{$attendance->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => ['id', 'user', 'date', 'clock_in', 'breaks'],
            ]);
    }

    public function test_returns_404_for_nonexistent_id(): void
    {
        $response = $this->getJson('/api/v1/attendance-records/99999');

        $response->assertStatus(404)
            ->assertJson(['error' => '勤怠情報が見つかりませんでした。']);
    }
}
