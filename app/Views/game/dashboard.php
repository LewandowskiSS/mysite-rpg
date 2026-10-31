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
<div class="card">
    <h1><?= esc($user['username']) ?></h1>
    <p class="subtitle">Уровень <?= esc($user['level']) ?></p>

    <div style="margin-top:16px;">
        <label>HP</label>
        <div style="background:#334155;height:14px;border-radius:8px;overflow:hidden;">
            <div style="width:<?= max(0, min(100, (int) $user['hp'])) ?>%;height:100%;background:#ef4444;"></div>
        </div>
        <p style="font-size:.8rem;color:#94a3b8;margin-top:4px;"><?= esc($user['hp']) ?> / 100</p>
    </div>

    <div style="margin-top:16px;">
        <label>Энергия</label>
        <div style="background:#334155;height:14px;border-radius:8px;overflow:hidden;">
            <div style="width:<?= max(0, min(100, (int) $user['energy'] * 10)) ?>%;height:100%;background:#f59e0b;"></div>
        </div>
        <p style="font-size:.8rem;color:#94a3b8;margin-top:4px;"><?= esc($user['energy']) ?> / 10</p>
    </div>
  
    <?php
        $cooldownSec = (int) ($cooldownRemaining ?? 0);
        $isCooling   = $cooldownSec > 0;
        $noEnergy    = (int) $user['energy'] < 1;
        $disabled    = $isCooling || $noEnergy;
    ?>
    <form action="/walk" method="post" style="margin-top:24px;display:flex;justify-content:center;">
      <input type="text" name="website" value="" style="display:none;" autocomplete="off" tabindex="-1">  
      <?= csrf_field() ?>
        <div class="btn-ring" data-cooldown="<?= $cooldownSec ?>" data-disabled="<?= $disabled ? '1' : '0' ?>">
            <svg class="ring" viewBox="0 0 100 100">
                <circle class="ring-bg" cx="50" cy="50" r="46"></circle>
                <circle class="ring-fill" cx="50" cy="50" r="46"></circle>
            </svg>
            <button type="submit" class="walk-btn" <?= $disabled ? 'disabled' : '' ?>>
                <?= $noEnergy ? 'Нет энергии' : 'Путешествовать' ?>
            </button>
        </div>
    </form>
</div>
<script>
(function () {
    var ring = document.querySelector('.btn-ring');
    if (!ring) return;

    var fill  = ring.querySelector('.ring-fill');
    var btn   = ring.querySelector('.walk-btn');
    var C = 2 * Math.PI * 46; // circumference of r=46

    fill.style.strokeDasharray  = C;
    fill.style.strokeDashoffset = C; // empty at start

    var total  = parseInt(ring.dataset.cooldown, 10) || 0;
    var start  = null;

    function tick(ts) {
        if (start === null) start = ts;
        var elapsed = (ts - start) / 1000;
        var remain = Math.max(0, total - elapsed);
        var progress = total > 0 ? (elapsed / total) : 1;

        // fill clockwise (snake toward itself)
        fill.style.strokeDashoffset = C * (1 - progress);

        if (remain > 0) {
            requestAnimationFrame(tick);
        } else {
            fill.style.strokeDashoffset = 0;
            btn.disabled = false;
            btn.textContent = 'Путешествовать';
        }
    }

    if (total > 0) {
        btn.disabled = true;
        requestAnimationFrame(tick);
    }
})();
</script>
<?= $this->endSection() ?>