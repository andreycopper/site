<div class="d-flex flex-grow-1 align-items-center justify-content-center py-md-5 py-4">
    <div class="center-block">
        <div class="text-center mb-5">
            <h3>Reset Password</h3>
            <p class="text-muted">Reset Password with Site.</p>
        </div>

        <div class="alert alert-info text-center my-4" role="alert">
            Enter your Email and instructions will be sent to you!
        </div>

        <form action="/recover/submit/" method="post">
            <input type="hidden" name="csrf" value="<?= $this->csrf ?>">

            <div class="mb-4">
                <label for="email" class="form-label">Email</label>
                <input type="text" name="email" id="email" class="form-control" placeholder="Enter email">
            </div>

            <div class="text-center mt-4">
                <button class="btn btn-primary w-100" type="submit">Reset</button>
            </div>
        </form>

        <div class="text-center text-muted">
            <p class="mt-4">Remember It? <a href="/" class="fw-medium text-decoration-underline"> Login</a></p>
        </div>
    </div>
</div>
