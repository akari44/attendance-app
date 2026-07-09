<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\AttendanceBreakResource;
use App\Http\Resources\ApplicationResource;

class AttendanceRecordResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'date' => $this->getRawOriginal('date'),
            'clock_in' => $this->getRawOriginal('clock_in'),
            'clock_out' => $this->getRawOriginal('clock_out'),
            'total_time' => $this->getRawOriginal('clock_out') ? $this->total_work_time : null,
            'total_break_time' => $this->total_break_time,
            'comment' => $this->comment,
            'breaks' => AttendanceBreakResource::collection($this->whenLoaded('breakTimes')),
            'application' => new ApplicationResource($this->whenLoaded('applications')),
        ];
    }
}
