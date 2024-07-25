let errorTimer, clearTimer;

$(function () {
});

/**
 * Show error message
 * @param message
 */
function showError(message) {
    clearTimeout(errorTimer);
    clearTimeout(clearTimer);
    $('#error').html(message).addClass('show');
    errorTimer = removeError();
}

/**
 * Hide error message
 * @returns {number}
 */
function removeError() {
    return setTimeout(function () {
        hideError();
    }, 5000);
}

/**
 * Hide & clear error message
 */
function hideError() {
    $('#error').removeClass('show');

    clearTimer = setTimeout(function () {
        $('#error').html('');
    }, 2000);
}
