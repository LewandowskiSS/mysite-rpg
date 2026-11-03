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
        <div class="bar"><div id="mhp-bar" class="bar-fill bar-hp" style="width:<?= max(0, min(100, (int) $battle['monster_hp'] / (int) $monster['hp'] * 100)) ?>%"></div></div>
        <div class="opponent-hp" id="mhp-text"><?= max(0, (int) $battle['monster_hp']) ?> / <?= esc($monster['hp']) ?></div>
    </div>

    <!-- Игрок -->
    <div class="battle-player">
        <div class="player-name"><?= esc($user['username']) ?></div>
        <div class="bar"><div id="php-bar" class="bar-fill bar-hp" style="width:<?= max(0, min(100, (int) $user['hp'] / (new \App\Services\EnergyService())->maxHp((int) $user['level']) * 100)) ?>%"></div></div>
        <div class="player-hp" id="php-text"><?= esc($user['hp']) ?> / <?= (new \App\Services\EnergyService())->maxHp((int) $user['level']) ?> HP · ⚡<span id="energy-text"><?= esc($user['energy']) ?></span></div>
    </div>
  
    <div id="battle-log" class="battle-log"></div>
    <!-- Кнопки -->
    <div class="battle-actions">
        <?php $noEnergy = (int) $user['energy'] < 1; $cooling = (int) $cooldownRemaining > 0; ?>
        <form action="/battle/attack" method="post">
            <?= csrf_field() ?>
            <button type="button" id="attack-btn" <?= ($noEnergy || $cooling) ? 'disabled' : '' ?>>
                <?= $noEnergy ? 'Нет энергии' : ($cooling ? 'Перезарядка…' : 'Атаковать') ?>
            </button>
        </form>
        <form action="/battle/flee" method="post">
            <?= csrf_field() ?>
            <button type="button" id="flee-btn" style="background:#1f2029;color:#e5e5ea;">Сбежать</button>
        </form>
    </div>
</div>
<script>
const csrfName  = '<?= csrf_token() ?>';
const csrfHash  = '<?= csrf_hash() ?>';
const $ = (id) => document.getElementById(id);

function send(url, method) {
    return fetch(url, {
        method: method,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: new URLSearchParams({ [csrfName]: csrfHash })
    }).then(r => r.json());
}

function applyState(d) {
    if (d.monster_hp === undefined) return; // guard
    $('mhp-bar').style.width = Math.max(0, Math.min(100, d.monster_hp / d.monster_max * 100)) + '%';
    $('mhp-text').textContent = d.monster_hp + ' / ' + d.monster_max;
    $('php-bar').style.width = Math.max(0, Math.min(100, d.player_hp / d.player_max * 100)) + '%';
    $('php-text').textContent = d.player_hp + ' / ' + d.player_max + ' HP · ⚡' + d.energy;
    $('energy-text').textContent = d.energy;
    $('battle-log').textContent = d.log;
}

let cooldownTimer = null;

function startCooldown(sec) {
    sec = parseInt(sec, 10);
    if (isNaN(sec) || sec <= 0) {
        sec = 3;
    }
    const btn = $('attack-btn');
    btn.disabled = true;
    let remain = sec;

    if (cooldownTimer) {
        clearInterval(cooldownTimer);
    }

    btn.textContent = 'Перезарядка… ' + remain;
    cooldownTimer = setInterval(() => {
        remain--;
        if (remain <= 0) {
            clearInterval(cooldownTimer);
            cooldownTimer = null;
            btn.disabled = false;
            btn.textContent = 'Атаковать';
        } else {
            btn.textContent = 'Перезарядка… ' + remain;
        }
    }, 1000);
}

$('attack-btn').addEventListener('click', () => {
    const btn = $('attack-btn');
    btn.disabled = true;
    send('/battle/attack', 'POST').then(d => {
        if (d.status === 'cooldown') {
            $('battle-log').textContent = d.log;
            startCooldown((d.remaining || 3) + 1);
            return;
        }
        if (d.status === 'no_energy') {
            $('battle-log').textContent = d.log;
            btn.disabled = true;
            btn.textContent = 'Нет энергии';
            return;
        }
        applyState(d);
        if (d.ended) {
            setTimeout(() => location.href = '/', 1200);
        } else {
            startCooldown(d.cooldown + 1);
        }
    }).catch(() => {
        btn.disabled = false;
    });
});

$('flee-btn').addEventListener('click', () => {
    send('/battle/flee', 'POST').then(d => {
        location.href = '/';
    });
});
</script>
<?= $this->endSection() ?>