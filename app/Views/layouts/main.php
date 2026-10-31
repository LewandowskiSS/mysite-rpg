<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'MySite RPG') ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body class="bg-gray-900 text-gray-100 min-h-screen">
    <header>
        <div class="wrap flex-between">
            <a href="/" class="logo">MySite RPG</a>
            <nav>
                <?php $cu = current_user(); ?>
                <?php if ($cu): ?>
                    <div class="hud">
                        <span class="hud-item hud-hp">HP <?= esc($cu['hp']) ?>/100</span>
                        <span class="hud-item hud-en">⚡<?= esc($cu['energy']) ?>/10</span>
                        <span class="hud-item hud-gold">🪙<?= esc($cu['gold']) ?></span>
                        <span class="hud-item hud-lvl">Ур.<?= esc($cu['level']) ?></span>
                    </div>
                    <span class="username"><?= esc($cu['username']) ?></span>
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
</body>
</html>