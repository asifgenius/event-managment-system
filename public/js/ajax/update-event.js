$('#updateEventForm').on('submit', function(e) {
    e.preventDefault();

    var formData = $(this).serialize(); 
    var eventId = document.getElementById("eventId").value;
   
    $.ajax({
        type: "PATCH",
        url: '/event_managment/events/' + eventId, 
        data: formData,
        dataType: "json",
        success: function(response) {
            if (response.success) {
                $('#successMessage').removeClass('d-none'); 
            } else {
                $('#errorMessage').removeClass('d-none').text(response.error); 
            }
        },
        error: function(xhr, status, error) {
            console.error("Error:", xhr.responseText);
        }
    });
});
