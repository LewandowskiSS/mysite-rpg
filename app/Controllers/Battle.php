<?php

namespace App\Controllers;

use App\Models\Battle as BattleModel;
use App\Models\Monster;
use App\Models\User;
use App\Services\EnergyService;

class Battle extends BaseController
{
    // Cooldown between attacks, seconds.
    public const ATTACK_COOLDOWN = 3;

    protected $battleModel;
    protected $monsterModel;
    protected $userModel;
    protected $energyService;

    public function __construct()
    {
        $this->battleModel   = new BattleModel();
        $this->monsterModel  = new Monster();
        $this->userModel     = new User();
        $this->energyService = new EnergyService();
    }

    // Attack formula: base + per-level scaling.
    protected function playerAttack(int $level): int
    {
        return 10 + ($level - 1) * 3;
    }

    // Damage roll with ±10% spread and 10% crit (x1.5).
    protected function rollDamage(int $attack): array
    {
        $spread = (int) round($attack * mt_rand(90, 110) / 100);
        $crit   = mt_rand(1, 100) <= 10;

        if ($crit) {
            $spread = (int) round($spread * 1.5);
        }

        return ['damage' => max(1, $spread), 'crit' => $crit];
    }

    public function start()
    {
        $userId = session()->get('user_id');
        if (! $userId) {
            return redirect()->to('login');
        }

        // Reuse active battle if exists
        $active = $this->battleModel->findActiveByUser($userId);
        if ($active) {
            return redirect()->to('battle');
        }

        $user = $this->currentUser;
        if (! $user) {
            return redirect()->to('login');
        }

        $monster = $this->monsterModel->pickForLevel((int) $user['level']);
        if (! $monster) {
            return redirect()->to('/')->with('error', 'Нет доступных монстров.');
        }

        $token = bin2hex(random_bytes(32));

        $battleId = $this->battleModel->insert([
            'user_id'       => $user['id'],
            'monster_id'    => $monster['id'],
            'monster_hp'    => $monster['hp'],
            'token'         => $token,
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

      
        session()->set('battle_id', $battleId);
        session()->set('battle_token', $token);

        return redirect()->to('battle');
    }

    public function index()
    {
        $userId = session()->get('user_id');
        if (! $userId) {
            return redirect()->to('login');
        }

        $battle = $this->battleModel->findActiveByUser($userId);
        if (! $battle) {
            return redirect()->to('/')->with('error', 'Нет активного боя.');
        }

        $monster = $this->monsterModel->find($battle['monster_id']);
        $user    = $this->currentUser;

        if (! $user) {
            return redirect()->to('login');
        }

        $lastAttack = $battle['last_attack_at'] ? strtotime($battle['last_attack_at']) : 0;
        $cooldownRemaining = max(0, self::ATTACK_COOLDOWN - (time() - $lastAttack));

        return view('battle/index', [
            'battle'             => $battle,
            'monster'            => $monster,
            'user'               => $user,
            'playerAttack'       => $this->playerAttack((int) $user['level']),
            'cooldownRemaining'  => $cooldownRemaining,
        ]);
    }

    public function attack()
    {
        $userId = session()->get('user_id');
        if (! $userId) {
            return redirect()->to('login');
        }
    
        $battle = $this->battleModel->findActiveByUser($userId);
        if (! $battle) {
            return redirect()->to('/')->with('error', 'Нет активного боя.');
        }
    
        $user = $this->currentUser;
        if (! $user) {
            return redirect()->to('login');
        }
    
        // Cooldown check
        if ($battle['last_attack_at']) {
            $remaining = self::ATTACK_COOLDOWN - (time() - strtotime($battle['last_attack_at']));
            if ($remaining > 0) {
                return redirect()->to('battle')->with('error', 'Подождите перед следующей атакой.');
            }
        }

        // Energy check
        if ((int) $user['energy'] < 1) {
            return redirect()->to('battle')->with('error', 'Недостаточно энергии для атаки.');
        }

        $monster = $this->monsterModel->find($battle['monster_id']);
        if (! $monster) {
            return redirect()->to('battle')->with('error', 'Монстр не найден.');
        }

        $playerAtk = $this->playerAttack((int) $user['level']);
        $roll = $this->rollDamage($playerAtk);

        $monsterHp = (int) $battle['monster_hp'] - $roll['damage'];
        $energy = (int) $user['energy'] - 1;
        $hp = (int) $user['hp'];

        $log = [];
        $log[] = $roll['crit']
            ? "Критический удар! Вы нанесли {$roll['damage']} урона."
            : "Вы нанесли {$roll['damage']} урона.";

        // Monster counter-attack if still alive
        if ($monsterHp > 0) {
            $mRoll = $this->rollDamage((int) $monster['attack']);
            $hp = max(0, $hp - $mRoll['damage']);
            $log[] = "{$monster['name']} наносит вам {$mRoll['damage']} урона.";
        }

        $status = 'active';
        $endedAt = null;

        if ($monsterHp <= 0) {
            $status = 'won';
            $endedAt = date('Y-m-d H:i:s');
            $log[] = "Вы победили {$monster['name']}!";
        } elseif ($hp <= 0) {
            $status = 'lost';
            $endedAt = date('Y-m-d H:i:s');
            $log[] = "Вы пали в бою.";
        }

        // Update battle
        $this->battleModel->update($battle['id'], [
            'monster_hp'     => max(0, $monsterHp),
            'status'         => $status,
            'last_attack_at' => date('Y-m-d H:i:s'),
            'ended_at'       => $endedAt,
        ]);

        // Update user HP + energy
        $this->userModel->update($user['id'], [
            'hp'     => $hp,
            'energy' => $energy,
        ]);

        // Apply rewards on victory
        $rewardMsg = '';
        if ($status === 'won') {
            $gold = (int) $user['gold'] + (int) $monster['gold_reward'];
            $exp  = (int) $user['exp'] + (int) $monster['exp_reward'];
            $this->userModel->update($user['id'], ['gold' => $gold, 'exp' => $exp]);
            $rewardMsg = " Награда: +{$monster['gold_reward']} золота, +{$monster['exp_reward']} опыта.";
        }

        if ($status !== 'active') {
            session()->remove('battle_id');
            session()->remove('battle_token');
        }

        $message = implode(' ', $log) . $rewardMsg;
    
        // AJAX response (no page reload)
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'         => $status,
                'monster_hp'     => max(0, $monsterHp),
                'monster_max'    => (int) $monster['hp'],
                'player_hp'      => $hp,
                'player_max'     => $this->energyService->maxHp((int) $user['level']),
                'energy'         => $energy,
                'log'            => $message,
                'cooldown'       => self::ATTACK_COOLDOWN,
                'ended'          => $status !== 'active',
                'rewards'        => $rewardMsg,
            ]);
        }
    
        if ($status !== 'active') {
            return redirect()->to('/')->with('event_message', $message);
        }
    
        return redirect()->to('battle')->with('event_message', $message);
    }

    public function flee()
    {
        $userId = session()->get('user_id');
        if (! $userId) {
            return redirect()->to('login');
        }

        $battle = $this->battleModel->findActiveByUser($userId);
        if (! $battle) {
            return redirect()->to('/');
        }

        $this->battleModel->update($battle['id'], [
            'status'   => 'fled',
            'ended_at' => date('Y-m-d H:i:s'),
        ]);

        session()->remove('battle_id');
        session()->remove('battle_token');

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status'  => 'fled',
                'ended'   => true,
                'log'     => 'Вы сбежали из боя.',
            ]);
        }
      
        return redirect()->to('/')->with('event_message', 'Вы сбежали из боя.');
    }
}