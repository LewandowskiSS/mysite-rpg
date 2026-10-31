<?php

use App\Models\User;
use App\Services\EnergyService;

if (! function_exists('current_user')) {
    function current_user(): ?array
    {
        static $cached = null;
        static $loaded = false;

        if ($loaded) {
            return $cached;
        }
        $loaded = true;

        $userId = session()->get('user_id');
        if (! $userId) {
            return $cached = null;
        }

        $userModel = new User();
        $user = $userModel->find($userId);
        if (! $user) {
            return $cached = null;
        }

        $energyService = new EnergyService();
        $cached = $energyService->regen($user);
        return $cached;
    }
}