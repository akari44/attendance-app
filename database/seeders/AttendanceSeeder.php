<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */

    private function getWeekdays(Carbon $month, int $count): array
    {
        $dates = [];
        $date = $month->copy();

        while (count($dates) < $count) {
            if ($date->isWeekday()) {
                $dates[] = $date->toDateString();
            }
            $date->addDay();
        }
        return $dates;
    }

    public function run(): void
    {
        $user1 = User::Where('email', 'user1@example.com')->first();
        $user2 = User::Where('email', 'user2@example.com')->first();

        for ($i = 5; $i >= 1; $i--) {
            $month = Carbon::now()->subMonths($i)->startOfMonth();
            $weekdays = $this->getWeekdays($month, 15);

            foreach ($weekdays as $date) {
                $attendance = Attendance::create([
                    'user_id' => $user1->id,
                    'date' => $date,
                    'clock_in' => '09:00:00',
                    'clock_out' => '18:00:00',
                    'status' => '退勤済',
                ]);

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => '12:00:00',
                    'break_end' => '13:00:00'
                ]);
            }
        }

        $patterns = [
            ...array_fill(0, 10, ['in' => '09:00:00', 'out' => '18:00:00']),
            ...array_fill(0, 3, ['in' => '09:00:00', 'out' => '20:00:00']),
            ...array_fill(0, 2, ['in' => '09:30:00', 'out' => '18:00:00']),
            ...array_fill(0, 1, ['in' => '09:00:00', 'out' => '17:00:00']),
            ...array_fill(0, 1, ['in' => '08:00:00', 'out' => '21:00:00']),
        ];

        $month = Carbon::now()->startOfMonth();
        $weekdays = $this->getWeekdays($month, 17);


        for ($i = 0; $i < 17; $i++) {
            $date = $weekdays[$i];
            $pattern = $patterns[$i];

            $attendance = Attendance::create([
                'user_id' => $user1->id,
                'date' => $date,
                'clock_in' => $pattern['in'],
                'clock_out' => $pattern['out'],
                'status' => '退勤済',
            ]);

            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => '12:00:00',
                'break_end' => '13:00:00'
            ]);
        }

        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->startOfMonth();
            $weekdays = $this->getWeekdays($month, 15);

            foreach ($weekdays as $date) {
                $attendance = Attendance::create([
                    'user_id' => $user2->id,
                    'date' => $date,
                    'clock_in' => '09:00:00',
                    'clock_out' => '18:00:00',
                    'status' => '退勤済',
                ]);

                BreakTime::create([
                    'attendance_id' => $attendance->id,
                    'break_start' => '12:00:00',
                    'break_end' => '13:00:00'
                ]);
            }
        }

    }
}
