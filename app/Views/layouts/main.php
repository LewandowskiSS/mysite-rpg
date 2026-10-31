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
                <?php if (session()->get('user_id')): ?>
                    <span class="username"><?= esc(session()->get('username')) ?></span>
                    <a href="/logout" class="link-danger">Выйти</a>
                <?php else: ?>
                    <a href="/login">Вход</a>
                    <a href="/register" class="link-accent">Регистрация</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-8">
        <?= $this->renderSection('content') ?>
    </main>
</body>
</html>