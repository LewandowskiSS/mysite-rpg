<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
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

    <div style="margin-top:20px;display:flex;gap:12px;">
        <div style="flex:1;background:#0f172a;border:1px solid #334155;border-radius:10px;padding:14px;text-align:center;">
            <div style="font-size:1.5rem;font-weight:700;color:#fbbf24;"><?= esc($user['gold']) ?></div>
            <div style="font-size:.75rem;color:#94a3b8;">Золото</div>
        </div>
        <div style="flex:1;background:#0f172a;border:1px solid #334155;border-radius:10px;padding:14px;text-align:center;">
            <div style="font-size:1.5rem;font-weight:700;color:#60a5fa;"><?= esc($user['exp']) ?></div>
            <div style="font-size:.75rem;color:#94a3b8;">Опыт</div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>