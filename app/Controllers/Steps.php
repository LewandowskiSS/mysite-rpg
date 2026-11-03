<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Event;
use CodeIgniter\Controller;

class Steps extends BaseController
{
    protected $userModel;
    protected $eventModel;
    // Cooldown between walks, seconds.
    public const COOLDOWN_SEC = 5;


    public function __construct()
    {
        $this->userModel = new User();
        $this->eventModel = new Event();
    }

    public function walk()
    {
        if (! session()->get('user_id')) {
            return redirect()->to('login');
        }
        // Rate limit — prevent scripted auto-walk.
        $throttler = \Config\Services::throttler();
        if ($throttler->check(md5(session()->get('user_id') . 'walk'), 10, MINUTE) === false) {
            return redirect()->to('/')->with('error', 'Слишком много действий. Попробуйте позже.');
        }  

        $user = $this->currentUser;
    
        if (! $user) {
            session()->destroy();
            return redirect()->to('login');
        }
        // Cooldown check — prevent rapid clicking / auto-click.
        if (! empty($user['last_walk_at'])) {
            $lastWalk = strtotime($user['last_walk_at']);
            $remaining = self::COOLDOWN_SEC - (time() - $lastWalk);
            if ($remaining > 0) {
                return redirect()->to('/')->with('error', 'Подождите немного перед следующим шагом.');
            }
        }

        // Energy check
        if ((int) $user['energy'] < 1) {
            return redirect()->to('/')->with('error', 'Недостаточно энергии. Отдохните немного.');
        }
        // Death check — cannot walk while at 0 HP.
        if ((int) $user['hp'] <= 0) {
            return redirect()->to('/')->with('error', 'Вы без сил. Подождите восстановления HP.');
        }
      
        // Pick event
        $event = $this->eventModel->pickRandom();
        // Random encounter: 20% chance to divert into a battle.
        if (mt_rand(1, 100) <= 20) {
            // Consume 1 energy for the step, then start battle.
            $this->userModel->update($user['id'], [
                'energy'       => (int) $user['energy'] - 1,
                'last_step_at' => date('Y-m-d H:i:s'),
            ]);
            return redirect()->to('battle/start');
        }
        // Apply effect
        $delta = mt_rand((int) $event['min_value'], (int) $event['max_value']);

        $gold   = (int) $user['gold'];
        $exp    = (int) $user['exp'];
        $hp     = (int) $user['hp'];
        $energy = (int) $user['energy'] - 1;
        $level  = (int) $user['level'];


        switch ($event['type']) {
            case 'gold':
                $gold += $delta;
                break;
            case 'exp':
                $exp += $delta;
                break;
            case 'hp':
                $maxHp = (new \App\Services\EnergyService())->maxHp($level);
                $hp = max(0, min($maxHp, $hp + $delta));
                break;
            case 'nothing':
                break;
        }

        // Level up: exp_needed = level * 100
        $expNeeded = $level * 100;
        if ($exp >= $expNeeded) {
            $exp -= $expNeeded;
            $level++;
        }

        $this->userModel->update($user['id'], [
            'gold'         => $gold,
            'exp'          => $exp,
            'hp'           => $hp,
            'energy'       => $energy,
            'level'        => $level,
            'last_step_at' => date('Y-m-d H:i:s'),
            'last_walk_at' => date('Y-m-d H:i:s'),
        ]);

        $message = $event['text'];

        if ($event['type'] === 'gold') {
            $message .= " +{$delta} золота.";
        } elseif ($event['type'] === 'exp') {
            $message .= " +{$delta} опыта.";
        } elseif ($event['type'] === 'hp' && $delta < 0) {
            $message .= " -" . abs($delta) . " HP.";
        } elseif ($event['type'] === 'hp' && $delta > 0) {
            $message .= " +{$delta} HP.";
        }

        return redirect()->to('/')->with('event_message', $message);
    }
}