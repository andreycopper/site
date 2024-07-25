<?php
use System\Request;
?>

<div class="row align-items-center align-content-center py-2">
    <div class="text-center mb-2">
        <h3>Register invitation</h3>
        <p class="text-muted">Enter the code into the form</p>
    </div>

    <form action="/register/" method="get" id="invite" class="needs-validation" novalidate>
        <div class="mb-3 login-block font-size-14">
            <label for="code" class="form-label">Code <sup class="required">*</sup></label>
            <input type="text" name="code" id="code" value="<?= Request::get('code') ?? '' ?>" class="form-control" placeholder="Enter code" required>
            <div class="invalid-feedback font-size-12">
                Please Enter Code
            </div>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-primary w-100 waves-effect waves-light">Send</button>
        </div>
    </form>
</div>

<script>
    $(function () {
        $('#invite button[type=submit]').on('click', function (e) {
            e.preventDefault();
            let form = $('#invite');

            $.ajax({
                method: "GET",
                dataType: 'json',
                url: form.attr('action'),
                data: form.serialize(),
                beforeSend: function () {
                    $('#loader').show();
                },
                success: function (data, textStatus, jqXHR) {
                    if (textStatus === 'success' && jqXHR.status === 200 && data && data.result)
                        window.location.href = '/register/?code=' + $('#code').val();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#loader').hide();
                    showError(jqXHR.responseJSON.message);
                }
            });
        });
    });
</script>
