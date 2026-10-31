<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'MySite RPG') ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <header>
        <div class="wrap flex-between">
            <a href="/" class="logo">MySite RPG</a>
            <nav>
                <?php if (current_user()): ?>
                    <a href="/logout" class="link-danger">Выйти</a>
                <?php else: ?>
                    <a href="/login">Вход</a>
                    <a href="/register" class="link-accent">Регистрация</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>
        <?= $this->renderSection('content') ?>
    </main>
    <?php $cu = current_user(); ?>
    <?php if ($cu): ?>
    <?php $maxHp = (new \App\Services\EnergyService())->maxHp((int) $cu['level']); ?>

    <!-- Нижняя навигация -->
    <nav class="bottom-nav">
        <a href="/" class="bn-item bn-active">🏠 <span>Главная</span></a>
        <button type="button" class="bn-item" onclick="document.getElementById('profileModal').classList.add('open')">👤 <span>Профиль</span>
        </button>
    </nav>

    <!-- Всплывающая панель профиля -->
    <div id="profileModal" class="modal" onclick="if(event.target===this)this.classList.remove('open')">
        <div class="modal-card">
            <div class="modal-head">
                <span class="modal-name"><?= esc($cu['username']) ?></span>
                <span class="modal-lvl">Ур. <?= esc($cu['level']) ?></span>
            </div>

            <div class="bar-block">
                <div class="bar-label">HP <span><?= esc($cu['hp']) ?>/<?= $maxHp ?></span></div>
                <div class="bar"><div class="bar-fill bar-hp" style="width:<?= max(0, min(100, (int) $cu['hp'] / $maxHp * 100)) ?>%"></div></div>
            </div>

            <div class="bar-block">
                <div class="bar-label">Энергия <span><?= esc($cu['energy']) ?>/10</span></div>
                <div class="bar"><div class="bar-fill bar-en" style="width:<?= max(0, min(100, (int) $cu['energy'] * 10)) ?>%"></div></div>
            </div>

            <div class="modal-stats">
                <div class="stat"><span>🪙</span> <?= esc($cu['gold']) ?></div>
                <div class="stat"><span>✨</span> <?= esc($cu['exp']) ?></div>
            </div>

            <button type="button" class="modal-close" onclick="document.getElementById('profileModal').classList.remove('open')">Закрыть</button>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>