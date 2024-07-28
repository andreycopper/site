<?php
/**
 * @var string $code
 */
?>

<div class="d-flex flex-grow-1 align-items-center justify-content-center py-md-5 py-4">
    <div class="center-block">
        <div class="text-center mb-5">
            <h3>Recovery Password</h3>
        </div>

        <form action="/recover/password/?code=<?= $code ?>" method="post" id="recover">
            <input type="hidden" name="csrf" value="<?= $this->csrf ?>">

            <div class="mb-3">
                <label for="password-new" class="form-label">New Password</label>
                <div class="position-relative auth-pass-inputgroup mb-3">
                    <input type="password" name="password" id="password" class="form-control pe-5" placeholder="Enter New Password">
                    <button type="button" id="password-new-show" class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted">
                        <i class="ri-eye-fill align-middle"></i>
                    </button>
                </div>
            </div>

            <div class="mb-3">
                <label for="password-confirm" class="form-label">Confirm New Password</label>
                <input type="password" name="password_confirm" id="password_confirm" class="form-control" placeholder="Enter Confirm Password">
            </div>

            <div class="text-center mt-4">
                <div class="row">
                    <div class="col-6">
                        <button class="btn btn-primary w-100" type="submit">Save</button>
                    </div>

                    <div class="col-6">
                        <button class="btn btn-light w-100" type="reset">Cancel</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(function () {
        $('#recover button[type=submit]').on('click', function (e) {
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
                    $('#loader').hide();
                    if (textStatus === 'success' && jqXHR.status === 200 && data && data.result)
                        window.location.href = '/recover/finish/' + data.message + '/';
                },
                error: function (jqXHR, textStatus, errorThrown) {console.log(jqXHR);
                    $('#loader').hide();
                    showError(jqXHR.responseJSON.message);
                }
            });
        });
    });
</script>
