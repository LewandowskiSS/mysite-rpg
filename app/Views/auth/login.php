<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="card">
    <h1>Вход</h1>
    <p class="subtitle">Вернитесь в игру, воин</p>

    <?php if (session()->get('errors')): ?>
        <div class="errors">
            <?php foreach (session()->get('errors') as $e): ?>
                <p><?= esc($e) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (session()->get('error')): ?>
        <div class="errors">
            <p><?= esc(session()->get('error')) ?></p>
        </div>
    <?php endif; ?>

    <form action="/login" method="post">
        <?= csrf_field() ?>
        <label>Имя пользователя</label>
        <input type="text" name="username" value="<?= esc(old('username')) ?>" required>

        <label>Пароль</label>
        <input type="password" name="password" required>

        <button type="submit">Войти</button>
    </form>

    <p class="form-footer">Нет аккаунта? <a href="/register">Зарегистрироваться</a></p>
</div>
<?= $this->endSection() ?>