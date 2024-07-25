<?php
/**
 * @var string $code
 */
?>

<div class="d-flex flex-grow-1 align-items-center justify-content-center py-md-5 py-4">
    <div class="center-block">
        <div class="text-center mb-2">
            <h3>Reset Password</h3>
            <p class="text-muted">Enter the code into the form</p>
        </div>

        <form action="/recover/password/" method="get" class="needs-validation" novalidate>
            <!--        <input type="hidden" name="csrf" value="--><?php //= $this->csrf ?><!--">-->

            <div class="mb-3 login-block font-size-14">
                <label for="code" class="form-label">Code <sup class="required">*</sup></label>
                <input type="text" name="code" id="code" value="<?= $code ?>" class="form-control" placeholder="Enter code" required>
                <div class="invalid-feedback font-size-12">
                    Please Enter Code
                </div>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary w-100 waves-effect waves-light">Confirm</button>
            </div>
        </form>
    </div>
</div>
