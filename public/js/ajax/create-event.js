$(document).ready(function () {
    $('#createEventForm').on('submit', function (e) {
        e.preventDefault();

        var formData = $(this).serialize();
        $.ajax({
            url: '/event_managment/events',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    successMessageNotify(response.message)
                    $('#responseMessage').html('<p style="color: green;">' + response.success + '</p>');
                } else {
                    $('#responseMessage').html('<p style="color: red;">' + response.error + '</p>');
                }
            },
            error: function (xhr, status, error) {
                $('#responseMessage').html('<p style="color: red;">An error occurred. Please try again.</p>');
            }
        });
    });
});
