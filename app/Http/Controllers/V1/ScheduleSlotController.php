<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SyncScheduleSlotsRequest;
use App\Services\V1\ScheduleSlotSyncService;
use Illuminate\Http\Request;

class ScheduleSlotController extends Controller
{
    /**
     * @var ScheduleSlotSyncService
     */
    private ScheduleSlotSyncService $syncService;

    public function __construct(ScheduleSlotSyncService $syncService)
    {
        $this->syncService = $syncService;
    }

    public function scheduleSlotSync(
        SyncScheduleSlotsRequest $request
    ) {

        $this->syncService->sync(
            $request->validated()
        );

        return response()->json([
            'message' => 'Schedule synchronized successfully'
        ]);
    }
}
