$(document).ready(function () {
    $('.loginForm').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: '/event_managment/api/login',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    successMessageNotify(response.success)
                    window.location.href = '/event_managment/dashboard';
                } else {
                    successMessageNotify(response.message)
                }
            },
            error: function (xhr, status, error) {
                alert('An error occurred. Please try again.');
            }
        });
    });
});
