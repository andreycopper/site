<div>
    <h3>Welcome to Mesigo, <?= $login ?>!</h3>

    <p><b>Your account is almost ready.</b> Please confirm your email and hit the button below.</p>

    <a href="<?= SITE_URL ?>/confirm/?hash=<?= $hash ?>" class="btn">Confirm Email</a>

    <p>You can also paste the following link into your browser</p>

    <p class="link"><?= SITE_URL ?>/confirm/?user=<?= $hash ?></p>
</div>
