<?php

declare(strict_types=1);

namespace App\Domain\Services;

use App\Domain\Models\AuditLog;
use App\Domain\Models\CashFlow;
use App\Domain\Models\Sale;
use App\Domain\Models\Shift;
use App\Domain\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Ref: DESIGN.md & ARCHITECTURE.md
 */
final class ShiftService
{
    /**
     * Open a new shift for the user.
     */
    public function openShift(User $user, float $startingFloat): Shift
    {
        return DB::transaction(function () use ($user, $startingFloat) {
            // Check if user already has an open shift (Race condition protection)
            $existingShift = Shift::where('user_id', $user->id)
                ->where('status', 'open')
                ->lockForUpdate()
                ->first();

            if ($existingShift) {
                throw new \Exception('User already has an open shift.');
            }

            $shift = Shift::create([
                'user_id' => $user->id,
                'starting_float' => $startingFloat,
                'status' => 'open',
            ]);

            AuditLog::create([
                'user_id' => $user->id,
                'action' => 'SHIFT_OPEN',
                'entity' => 'Shift',
                'entity_id' => $shift->id,
                'new_data' => $shift->toArray(),
            ]);

            return $shift;
        });
    }

    /**
     * Close the shift and calculate reconciliation.
     */
    public function closeShift(Shift $shift, float $endingCash): Shift
    {
        return DB::transaction(function () use ($shift, $endingCash) {
            // Lock shift row to prevent concurrent closure
            $shift = Shift::where('id', $shift->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($shift->status !== 'open') {
                throw new \Exception('Shift is already closed.');
            }

            // Calculate expected cash: starting + cash sales + cash in - cash out
            $cashSales = Sale::where('shift_id', $shift->id)
                ->where('payment_method', 'cash')
                ->sum('total');

            $cashIn = CashFlow::where('shift_id', $shift->id)
                ->where('type', 'in')
                ->sum('amount');

            $cashOut = CashFlow::where('shift_id', $shift->id)
                ->where('type', 'out')
                ->sum('amount');

            $expectedCash = (float) $shift->starting_float + (float) $cashSales + (float) $cashIn - (float) $cashOut;

            $oldData = $shift->toArray();

            $shift->update([
                'ending_cash' => $endingCash,
                'expected_cash' => $expectedCash,
                'status' => 'closed',
            ]);

            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => 'SHIFT_CLOSE',
                'entity' => 'Shift',
                'entity_id' => $shift->id,
                'old_data' => $oldData,
                'new_data' => $shift->refresh()->toArray(),
            ]);

            return $shift;
        });
    }
}
