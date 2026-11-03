<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php if (session()->get('event_message')): ?>
    <div class="notice" style="max-width:420px;margin:0 auto 16px;">
        <p><?= esc(session()->get('event_message')) ?></p>
    </div>
<?php endif; ?>

<?php if (session()->get('error')): ?>
    <div class="errors" style="max-width:420px;margin:0 auto 16px;">
        <p><?= esc(session()->get('error')) ?></p>
    </div>
<?php endif; ?>

<div class="card" style="max-width:420px;">
    <h1>⚔️ Бой</h1>

    <!-- Монстр -->
    <div class="battle-opponent">
        <div class="opponent-name"><?= esc($monster['name']) ?></div>
        <div class="bar"><div class="bar-fill bar-hp" style="width:<?= max(0, min(100, (int) $battle['monster_hp'] / (int) $monster['hp'] * 100)) ?>%"></div></div>
        <div class="opponent-hp"><?= max(0, (int) $battle['monster_hp']) ?> / <?= esc($monster['hp']) ?></div>
    </div>

    <!-- Игрок -->
    <div class="battle-player">
        <div class="player-name"><?= esc($user['username']) ?></div>
        <div class="bar"><div class="bar-fill bar-hp" style="width:<?= max(0, min(100, (int) $user['hp'] / (new \App\Services\EnergyService())->maxHp((int) $user['level']) * 100)) ?>%"></div></div>
        <div class="player-hp"><?= esc($user['hp']) ?> / <?= (new \App\Services\EnergyService())->maxHp((int) $user['level']) ?> HP · ⚡<?= esc($user['energy']) ?></div>
    </div>

    <!-- Кнопки -->
    <div class="battle-actions">
        <?php $noEnergy = (int) $user['energy'] < 1; $cooling = (int) $cooldownRemaining > 0; ?>
        <form action="/battle/attack" method="post">
            <?= csrf_field() ?>
            <button type="submit" <?= ($noEnergy || $cooling) ? 'disabled style="background:#1f2029;color:#5a5a66;cursor:not-allowed;"' : '' ?>>
                <?= $noEnergy ? 'Нет энергии' : ($cooling ? 'Перезарядка…' : 'Атаковать') ?>
            </button>
        </form>
        <form action="/battle/flee" method="post">
            <?= csrf_field() ?>
            <button type="submit" style="background:#1f2029;color:#e5e5ea;">Сбежать</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>