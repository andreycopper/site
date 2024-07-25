<div class="row align-items-center align-content-center py-4">
    <div class="text-center mb-5">
        <h3>Change Password</h3>
    </div>

<!--    <div class="user-thumb text-center mb-4">-->
<!--        <img src="/images/avatar-1.jpg" class="rounded-circle img-thumbnail avatar-lg" alt="user">-->
<!--        <h5 class="font-size-15 mt-3">Kathryn Swarey</h5>-->
<!--    </div>-->

    <form action="/recover/password/" method="post">
        <input type="hidden" name="csrf" value="<?= $this->csrf ?>">

        <div class="mb-3">
            <label for="password" class="form-label">Old Password</label>
            <input type="password" name="password" id="password" class="form-control" placeholder="Enter Old Password">
        </div>

        <div class="mb-3">
            <label for="password-new" class="form-label">New Password</label>
            <div class="position-relative auth-pass-inputgroup mb-3">
                <input type="password" name="password" id="password-new" class="form-control pe-5" placeholder="Enter New Password">
                <button type="button" id="password-new-show" class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted">
                    <i class="ri-eye-fill align-middle"></i>
                </button>
            </div>
        </div>

        <div class="mb-3">
            <label for="password-confirm" class="form-label">Confirm New Password</label>
            <input type="password" name="password_confirm" id="password-confirm" class="form-control" placeholder="Enter Confirm Password">
        </div>

        <div class="text-center mt-4">
            <div class="row">
                <div class="col-6">
                    <button class="btn btn-primary w-100" type="submit">Save</button>
                </div>

                <div class="col-6">
                    <button class="btn btn-light w-100" type="button">Cancel</button>
                </div>
            </div>
        </div>
    </form>
</div>
