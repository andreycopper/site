<?php
use System\Request;
?>

<div class="d-flex flex-grow-1 align-items-center justify-content-center py-md-5 py-4">
    <div class="center-block">
        <div class="text-center mb-2">
            <h3>Confirm Account</h3>
            <p class="text-muted">Enter the code into the form</p>
        </div>

        <form action="/register/confirm/" method="get" class="needs-validation" id="confirm" novalidate>
            <!--        <input type="hidden" name="csrf" value="--><?php //= $this->csrf ?><!--">-->

            <div class="mb-3 login-block font-size-14">
                <label for="code" class="form-label">Code <sup class="required">*</sup></label>
                <input type="text" name="code" id="code" value="<?= Request::get('code') ?? '' ?>" class="form-control" placeholder="Enter code" required>
            </div>

            <div class="mb-3">
                <button type="submit" class="btn btn-primary w-100 waves-effect waves-light">Confirm</button>
            </div>
        </form>
    </div>
</div>

<script>
    $(function () {
        $('#confirm button[type=submit]').on('click', function (e) {
            e.preventDefault();
            let form = $(this).parents('form');

            $.ajax({
                method: "GET",
                dataType: 'json',
                url: form.attr('action'),
                data: form.serialize(),
                beforeSend: function () {
                    $('#loader').show();
                },
                success: function (data, textStatus, jqXHR) {console.log(data);
                    $('#loader').hide();
                    if (textStatus === 'success' && jqXHR.status === 200 && data && data.result)
                        window.location.href = '/register/finish/' + data.message + '/';
                },
                error: function (jqXHR, textStatus, errorThrown) {console.log(jqXHR);
                    $('#loader').hide();
                    showError(jqXHR.responseJSON.message);
                }
            });
        });
    });
</script>
