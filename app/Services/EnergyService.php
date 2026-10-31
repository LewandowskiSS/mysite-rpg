<?php

namespace App\Services;

use App\Models\User;

class EnergyService
{
    protected $userModel;

    // 1 energy per 3 minutes (180 sec), cap at 10.
    public const ENERGY_MAX  = 10;
    public const REGEN_SEC   = 180;
    // HP scales with level: 100 at level 1, +20 per level.
    public const HP_BASE      = 100;
    public const HP_PER_LEVEL = 20;
    public const HP_REGEN_SEC = 30; // 1 HP per 30 seconds

    public function maxHp(int $level): int
    {
        return self::HP_BASE + ($level - 1) * self::HP_PER_LEVEL;
    }


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
            $this->userModel->update($user['id'], [
                'last_step_at' => date('Y-m-d H:i:s'),
            ]);
            $user['last_step_at'] = date('Y-m-d H:i:s');
            return $this->regenHp($user);
        }

        $energy = (int) $user['energy'];

        if ($energy >= self::ENERGY_MAX) {
            $this->userModel->update($user['id'], [
                'last_step_at' => date('Y-m-d H:i:s'),
            ]);
            $user['last_step_at'] = date('Y-m-d H:i:s');
            return $this->regenHp($user);
        }

        $lastStep = strtotime($user['last_step_at']);
        $elapsed  = time() - $lastStep;

        if ($elapsed < self::REGEN_SEC) {
            return $this->regenHp($user);
        }

        $regen = floor($elapsed / self::REGEN_SEC);
        $newEnergy = min(self::ENERGY_MAX, $energy + $regen);

        $newTimestamp = date('Y-m-d H:i:s', $lastStep + ($regen * self::REGEN_SEC));

        $this->userModel->update($user['id'], [
            'energy'       => $newEnergy,
            'last_step_at' => $newTimestamp,
        ]);

        $user['energy']       = $newEnergy;
        $user['last_step_at'] = $newTimestamp;

        return $this->regenHp($user);
    }
      public function regenHp(array $user): array
    {
        $maxHp = $this->maxHp((int) $user['level']);
        $hp    = (int) $user['hp'];

        if ($hp >= $maxHp) {
            return $user;
        }

        if (empty($user['last_hp_regen_at'])) {
            $this->userModel->update($user['id'], [
                'last_hp_regen_at' => date('Y-m-d H:i:s'),
            ]);
            $user['last_hp_regen_at'] = date('Y-m-d H:i:s');
            return $user;
        }

        $lastRegen = strtotime($user['last_hp_regen_at']);
        $elapsed   = time() - $lastRegen;

        if ($elapsed < self::HP_REGEN_SEC) {
            return $user;
        }

        $regen = floor($elapsed / self::HP_REGEN_SEC);
        $newHp = min($maxHp, $hp + $regen);

        $newTimestamp = date('Y-m-d H:i:s', $lastRegen + ($regen * self::HP_REGEN_SEC));

        $this->userModel->update($user['id'], [
            'hp'               => $newHp,
            'last_hp_regen_at' => $newTimestamp,
        ]);

        $user['hp']               = $newHp;
        $user['last_hp_regen_at'] = $newTimestamp;

        return $user;
    }
}