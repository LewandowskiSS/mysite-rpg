<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <h1>Регистрация</h1>
    <p class="subtitle">Создайте персонажа и начните приключение</p>

    <?php if (session()->get('errors')): ?>
        <div class="errors">
            <?php foreach (session()->get('errors') as $e): ?>
                <p><?= esc($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="/register" method="post">
        <?= csrf_field() ?>
        <label>Имя пользователя</label>
        <input type="text" name="username" value="<?= esc(old('username')) ?>" required minlength="3" maxlength="30">

        <label>Email</label>
        <input type="email" name="email" value="<?= esc(old('email')) ?>" required>

        <label>Пароль</label>
        <input type="password" name="password" required minlength="6">

        <button type="submit">Создать аккаунт</button>
    </form>
</div>
<?= $this->endSection() ?>