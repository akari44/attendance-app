<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {// 3か月分の出勤日の取得
            foreach ([0, 1, 2] as $monthsAgo) {
                $start = Carbon::now()->subMonths($monthsAgo)->startOfMonth();

                if ($monthsAgo === 0) {
                    // 今月は昨日まで毎日出勤と仮定
                    $end = Carbon::now()->subDay();
                } else {
                    $end = Carbon::now()->subMonths($monthsAgo)->endOfMonth();
                }


                while ($start->lte($end)) {
                    // 土日はスキップ
                    if ($start->isWeekend()) {
                        $start->addDay();
                        continue;
                    }
                    $clockIn = rand(9, 10) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00';
                    $clockOut = rand(17, 20) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00';

                    $attendance = Attendance::create([
                        'user_id' => $user->id,
                        'date' => $start->toDateString(),
                        'clock_in' => $clockIn,
                        'clock_out' => $clockOut,
                        'status' => '退勤済',
                    ]);

                    $breakStart1 = rand(12, 13) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00';
                    $breakEnd1 = rand(13, 14) . ':' . str_pad(rand(1, 59), 2, '0', STR_PAD_LEFT) . ':00';
                    $breakStart2 = rand(15, 16) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT) . ':00';
                    $breakEnd2 = rand(16, 17) . ':' . str_pad(rand(1, 59), 2, '0', STR_PAD_LEFT) . ':00';

                    $breakPattern = rand(1, 3);

                    if ($breakPattern === 1) {
                        // 休憩なし
                    } elseif ($breakPattern === 2) {
                        // 休憩1回
                        BreakTime::create([
                            'attendance_id' => $attendance->id,
                            'break_start' => $breakStart1,
                            'break_end' => $breakEnd1,
                        ]);
                    } else {
                        BreakTime::create([
                            'attendance_id' => $attendance->id,
                            'break_start' => $breakStart1,
                            'break_end' => $breakEnd1,
                        ]);
                        BreakTime::create([
                            'attendance_id' => $attendance->id,
                            'break_start' => $breakStart2,
                            'break_end' => $breakEnd2,
                        ]);
                    }
                    $start->addDay();
                }
            }
        }
    }
}
