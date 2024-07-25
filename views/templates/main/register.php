<?php
use System\Request;
?>

<div class="d-flex flex-grow-1 align-items-center justify-content-center py-md-5 py-4">
    <div class="center-block">
        <div class="text-center mb-2">
            <h3>Register Account</h3>
            <p class="text-muted">Welcome</p>
        </div>

        <form action="/register/reg/?code=<?= Request::get('code') ?>" method="post" class="needs-validation" id="register" novalidate>
            <input type="hidden" name="csrf" value="<?= $this->csrf ?>">

            <div class="mb-3 login-block font-size-14">
                <label for="login" class="form-label">Username <sup class="required">*</sup></label>
                <input type="text" name="login" id="login" class="form-control" placeholder="Enter username" required value="test_user">
                <div class="invalid-feedback font-size-12">
                    Please Enter Username
                </div>
            </div>

            <div class="mb-3 email-block font-size-14">
                <label for="email" class="form-label">Email <sup class="required">*</sup></label>
                <input type="email" name="email" id="email" class="form-control" placeholder="Enter email" required value="test@mail.ru">
                <div class="invalid-feedback font-size-12">
                    Please Enter Email
                </div>
            </div>

            <div class="mb-3 password-block font-size-14">
                <label for="password" class="form-label">Password <sup class="required">*</sup></label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required value="8epT%J1@TuK">
                <div class="invalid-feedback font-size-12">
                    Please Enter Password
                </div>
            </div>

            <div class="mb-3 password-confirm-block font-size-14">
                <label for="password_confirm" class="form-label">Password <sup class="required">*</sup></label>
                <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Confirm password" required value="8epT%J1@TuK">
                <div class="invalid-feedback font-size-12">
                    Please Confirm Password
                </div>
            </div>

            <div class="mb-4 font-size-13">
                <p class="mb-0">By registering you agree to the Mesigo <a href="#" class="text-primary">Terms of Use</a></p>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary w-100 waves-effect waves-light">Register</button>
            </div>
        </form>

        <div class="mt-2 text-center text-muted font-size-13">
            <p>Already have an account? <a href="/auth/" class="fw-medium text-decoration-underline">Login</a></p>
        </div>
    </div>

</div>

<script>
    $(function () {
        $('#register button[type=submit]').on('click', function (e) {
            e.preventDefault();
            let form = $(this).parents('form');

            $.ajax({
                method: "POST",
                dataType: 'json',
                url: form.attr('action'),
                data: form.serialize(),
                beforeSend: function () {
                    $('#loader').show();
                },
                success: function (data, textStatus, jqXHR) {
                    if (textStatus === 'success' && jqXHR.status === 200 && data && data.result)
                        window.location.href = '/register/success/' + data.message + '/';
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#loader').hide();
                    showError(jqXHR.responseJSON.message);
                }
            });
        });
    });
</script>
