<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Domain\Models\Shift;
use App\Domain\Services\ShiftService;
use App\Http\Requests\Shift\EndShiftRequest;
use App\Http\Requests\Shift\StartShiftRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Ref: DESIGN.md & ARCHITECTURE.md
 */
class ShiftController extends Controller
{
    public function __construct(private ShiftService $shiftService) {}

    public function index(): View
    {
        $shifts = Shift::with('user')->orderBy('created_at', 'desc')->paginate(10);
        $activeShift = Shift::where('user_id', auth()->id())->where('status', 'open')->first();

        return view('shift.index', compact('shifts', 'activeShift'));
    }

    public function start(StartShiftRequest $request): RedirectResponse
    {
        try {
            $this->shiftService->openShift(auth()->user(), (float) $request->starting_float);

            return back()->with('success', 'Shift started successfully.');
        } catch (\Exception $e) {
            Log::error('Shift start failed: '.$e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    public function end(EndShiftRequest $request, Shift $shift): RedirectResponse
    {
        try {
            $this->shiftService->closeShift($shift, (float) $request->ending_cash);

            $variance = (float) $shift->refresh()->variance;
            $msg = 'Shift closed successfully.';
            if ($variance !== 0.0) {
                $msg .= ' WARNING: Variance detected: Rp '.number_format($variance);
            }

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            Log::error('Shift end failed: '.$e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }
}
