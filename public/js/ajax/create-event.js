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
                    $("#createEventForm")[0].reset();

                } else {
                    errorMessageNotify(response.error)
                }
            },
            error: function (xhr, status, error) {
                $('#responseMessage').html('<p style="color: red;">An error occurred. Please try again.</p>');
            }
        });
    });
});
