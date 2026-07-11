<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\AttendanceRecordResource;
use App\Http\Requests\Api\V1\IndexAttendanceRecordRequest;
use App\Http\Requests\Api\V1\StoreAttendanceRecordRequest;
use App\Http\Requests\Api\V1\UpdateAttendanceRecordRequest;


use App\Models\Attendance;
use Illuminate\Http\JsonResponse;

class AttendanceRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(IndexAttendanceRecordRequest $request): JsonResponse
    {
        $perPage = min($request->input('per_page', 20), 100);

        $records = Attendance::with(['user', 'breakTimes'])
            ->when($request->user_id, fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->date, fn($q) => $q->whereDate('date', $request->date))
            ->when($request->month, fn($q) => $q->where('date', 'like', $request->month . '%'))
            ->latest('date')
            ->paginate($perPage);

        return AttendanceRecordResource::collection($records)->response();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAttendanceRecordRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $attendance = $request->user()->attendances()->create($validated);
        $attendance->load(['user', 'breakTimes', 'applications']);
        return (new AttendanceRecordResource($attendance))->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendanceRecord): JsonResponse
    {
        $attendanceRecord->load(['user', 'breakTimes', 'applications']);
        return (new AttendanceRecordResource($attendanceRecord))->response();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateAttendanceRecordRequest $request,
        Attendance $attendanceRecord
    ): JsonResponse {
        $this->authorize('update', $attendanceRecord);
        $validated = $request->validated();
        $attendanceRecord->update($validated);
        $attendanceRecord->load(['user', 'breakTimes']);
        return (new AttendanceRecordResource($attendanceRecord))->response();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendanceRecord): JsonResponse
    {
        $this->authorize('delete', $attendanceRecord);
        $attendanceRecord->delete();
        return response()->json(null, 204);

    }
}
