var eventId = document.getElementById("eventId").value;

$.ajax({
    url: `/event_managment/api/events/${eventId}`,
    type: 'GET',
    contentType: 'application/json',
    dataType: 'json',
    success: function (response) {

        var event = response;
        $('#eventTitle').val(event.event.title);
        $('#eventDescription').val(event.event.description);
        $('#eventLocation').val(event.event.location);
        $('#eventPrice').val(event.event.price)
        $('#eventDuration').val(event.event.duration);
        $('#eventMaxCapacity').val(event.event.max_capacity);
        $('#eventStartDate').val(new Date());
        $('#eventEndDate').val(new Date(event.end_date).toISOString().split('T')[0]);
        $('#eventCreator').val(event.event.creator);

    },

    error: function (xhr, status, error) {
        console.error("Error fetching event data:", xhr.responseText);
    }
});


$('#updateEventForm').on('submit', function (e) {
    e.preventDefault();

    var formData = $(this).serialize();
    var eventId = document.getElementById("eventId").value;

    $.ajax({
        type: "PATCH",
        url: '/event_managment/events/' + eventId,
        data: formData,
        dataType: "json",
        success: function (response) {
            if (response.success) {
                successMessageNotify(response.success)
            } else {

                errorMessageNotify(response.error)
            }
        },
        error: function (xhr, status, error) {
            console.error("Error:", xhr.responseText);
        }
    });
});
