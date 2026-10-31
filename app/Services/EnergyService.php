<?php

namespace App\Services;

use App\Models\User;

class EnergyService
{
    protected $userModel;

    // 1 energy per 3 minutes (180 sec), cap at 10.
    public const ENERGY_MAX  = 10;
    public const REGEN_SEC   = 180;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Lazy regen: recalculate energy based on time since last action.
     * No background job — runs only when user is active.
     *
     * @param array $user
     * @return array updated user row (and persists if changed)
     */
    public function regen(array $user): array
    {
        if (empty($user['last_step_at'])) {
            // First interaction: set timestamp, energy already full.
            $this->userModel->update($user['id'], [
                'last_step_at' => date('Y-m-d H:i:s'),
            ]);
            $user['last_step_at'] = date('Y-m-d H:i:s');
            return $user;
        }

        $energy = (int) $user['energy'];

        if ($energy >= self::ENERGY_MAX) {
            // Already full — just bump timestamp so it doesn't accrue while capped.
            $this->userModel->update($user['id'], [
                'last_step_at' => date('Y-m-d H:i:s'),
            ]);
            $user['last_step_at'] = date('Y-m-d H:i:s');
            return $user;
        }

        $lastStep = strtotime($user['last_step_at']);
        $elapsed  = time() - $lastStep;

        if ($elapsed < self::REGEN_SEC) {
            // Not enough time passed — no change.
            return $user;
        }

        $regen = floor($elapsed / self::REGEN_SEC);
        $newEnergy = min(self::ENERGY_MAX, $energy + $regen);

        // Shift last_step_at by the consumed seconds (keep leftover time).
        $newTimestamp = date('Y-m-d H:i:s', $lastStep + ($regen * self::REGEN_SEC));

        $this->userModel->update($user['id'], [
            'energy'       => $newEnergy,
            'last_step_at' => $newTimestamp,
        ]);

        $user['energy']       = $newEnergy;
        $user['last_step_at'] = $newTimestamp;

        return $user;
    }
}