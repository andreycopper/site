<div class="d-flex flex-grow-1 align-items-center justify-content-center py-md-5 py-4">
    <div class="center-block">
        <div class="text-center mb-5">
            <h3>Welcome Back!</h3>
            <p class="text-muted">Sign in to continue to Site.</p>
        </div>

        <form action="/auth/login/" method="post" id="auth">
            <input type="hidden" name="csrf" value="<?= $this->csrf ?>">

            <div class="mb-3">
                <label for="email" class="form-label">Username</label>
                <input type="text" name="email" id="email" class="form-control" placeholder="Enter email" value="andreycopper@gmail.com">
            </div>

            <div class="mb-3">
                <div class="float-end">
                    <a href="/" class="text-muted">Forgot password?</a>
                </div>

                <label for="password" class="form-label">Password</label>
                <div class="position-relative auth-pass-inputgroup mb-3">
                    <input type="password" name="password" id="password" class="form-control pe-5" placeholder="Enter Password" value="8epT%J1@TuK">
                    <button type="button" id="password-addon" class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted">
                        <i class="ri-eye-fill align-middle"></i>
                    </button>
                </div>
            </div>

            <div class="form-check form-check-info font-size-16">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label for="remember" class="form-check-label font-size-14">
                    Remember me
                </label>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary w-100">Log In</button>
            </div>
        </form>

        <div class="mt-5 text-center text-muted">
            <p>Don't have an account? <a href="/register/" class="fw-medium text-decoration-underline">Register</a></p>
        </div>
    </div>

</div>

<script>
    $(function () {
        $('#auth button[type=submit]').on('click', function (e) {
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
                success: function (data, textStatus, jqXHR) {console.log(data);
                    if (textStatus === 'success' && jqXHR.status === 200 && data && data.result)
                        window.location.href = '/';
                },
                error: function (jqXHR, textStatus, errorThrown) {console.log(jqXHR);
                    $('#loader').hide();
                    showError(jqXHR.responseJSON.message);
                }
            });
        });
    });
</script>
