<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use Tests\TestCase;

class ID18_ApiAttendanceRecordWriteTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_can_make_attendance_record(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/attendance-records', [
            'date' => '2026-07-01',
            'clock_in' => '08:00:00',
            'clock_out' => '19:00:00',
            'comment' => 'お願いします',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => ['id', 'date', 'clock_in', 'clock_out'],
            ]);
    }

    public function test_error_input_validated_japanese(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/attendance-records', [
            'date' => '',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
            'comment' => 'お願いします',
        ]);

        $response->assertStatus(422)
            ->assertJsonStructure([
                'errors' => ['date']
            ])
            ->assertJsonFragment(['date' => ['勤怠日は必須です。']]);
    }

    public function test_can_update_attendance_record(): void
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/attendance-records/{$attendance->id}", [
                'clock_out' => '20:00:00',
            ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_out' => '20:00:00',
        ]);

        $response->assertStatus(200);

        // 存在しないID
        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/v1/attendance-records/99999", [
                'clock_out' => '20:00:00',
            ]);

        $this->assertDatabaseHas('attendances', [
            'id' => $attendance->id,
            'clock_out' => '20:00:00',
        ]);

        $response->assertStatus(404);


    }

    public function test_can_delete_attendance_record(): void
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'id' => 1,
            'user_id' => $user->id,
            'date' => '2026-07-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/attendance-records/1");
        $this->assertDatabaseMissing('attendances', ['id' => $attendance->id]);

        $response->assertStatus(204);

        // 存在しないID
        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/attendance-records/2");
        $response->assertStatus(404);
    }
}
