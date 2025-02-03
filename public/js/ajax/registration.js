$(document).ready(function () {
    $('#registrationForm').on('submit', function (e) {
        e.preventDefault();
        var formData = $(this).serialize(); 
        
        $.ajax({
            url: '/event_managment/api/registration', 
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                        window.location.href = '/event_managment/'; 
                } else {
                    $('#errorMessage').text(response.message).show();
                }
            },
            error: function (xhr, status, error) {
                alert('An error occurred. Please try again.');
            }
        });
    });
});
