<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Attendance;
use App\Models\User;
use Illuminate\Support\Facades\Hash;



class ID19_SanctumAuthTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;
    public function test_unauthenticated_user_cannot_write_attendance_record(): void
    {
        $response = $this->postJson('/api/v1/attendance-records', [
            'date' => '2026-07-01',
            'clock_in' => '08:00:00',
            'clock_out' => '19:00:00',
            'comment' => 'お願いします',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_own_attendance_can_put_and_delete(): void
    {
        $user = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user->id,
            'date' => '2026-07-01',
            'clock_in' => '09:00:00',
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

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/attendance-records/{$attendance->id}");
        $this->assertDatabaseMissing('attendances', ['id' => $attendance->id]);

        $response->assertStatus(204);
    }

    public function test_others_attendances_can__not_put_and_delete(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $attendance = Attendance::factory()->create([
            'user_id' => $user2->id,
            'date' => '2026-07-01',
            'clock_in' => '09:00:00',
            'clock_out' => '18:00:00',
        ]);

        $response = $this->actingAs($user1, 'sanctum')
            ->putJson("/api/v1/attendance-records/{$attendance->id}", [
                'clock_out' => '20:00:00',
            ]);
        $response->assertStatus(403);
        $response->assertJson(['error' => 'この操作を実行する権限がありません。']);

    }
}
