<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendanceRecordPolicy
{
    /**
     * 管理者は全操作を許可する
     * @param User $user
     * @param string $ability
     * @return bool|null
     */

    public function before(User $user, string $ability): bool|null
    {
        if ($user instanceof \App\Models\Admin) {
            return true;
        }
        return null;
    }


    /**
     * 勤怠の更新権限を確認する
     * @param User $user
     * @param Attendance $attendance
     * @return bool
     */
    public function update(User $user, Attendance $attendance): bool
    {
        return $user->id === $attendance->user_id;
    }

    /**
     * 勤怠の削除権限を確認する
     * @param User $user
     * @param Attendance $attendance
     * @return bool
     */
    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->id === $attendance->user_id;
    }

}
