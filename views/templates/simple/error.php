<div class="row align-items-center align-content-center py-4">
    <div class="text-center mb-2">

        <h3>Oops!</h3>

        <p class="text-muted">Something went wrong</p>
    </div>

    <div class="text-center py-2">
        <p class="error-code"><?= $this->code ?></p>

        <p class="text-muted font-size-16 error-message mt-2">
            <?= $this->message ?>
        </p>
    </div>

    <div class="mt-4 text-center text-muted">
        <p>
            Return <a href="<?= $_SERVER['HTTP_REFERER'] ?? '/' ?>" class="fw-medium text-decoration-underline">back</a> or
            return to <a href="/" class="fw-medium text-decoration-underline">home page</a>
        </p>
    </div>
</div>
