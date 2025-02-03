$(document).ready(function () {

    function getQueryParam(param) {
        var urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }
    var eventId = document.getElementById("eventId").value;
    if (!eventId) {
        $("#eventTitle").text("Event ID not provided").addClass('text-danger');
        return;
    }
    $.ajax({
        url: `/event_managment/api/events/${eventId}`,
        type: 'GET',
        contentType: 'application/json',
        dataType: 'json',
        success: function (response) {
            if (response) {
                var event = response;

                $('#eventTitle').text(event.event.title || 'No title available');
                $('#eventDescription').text(event.event.description || 'No description available');
                $('#eventLocation').text(event.event.location || 'No location available');
                $('#eventPrice').text(event.event.price !== '0' ? '$' + event.event.price : 'Price not specified');
                $('#eventDuration').text(event.event.duration !== '0' ? event.event.duration + ' hours' : 'Duration not specified');
                $('#eventMaxCapacity').text(event.event.max_capacity || 'No capacity set');
                $('#eventCurrentCapacity').text(event.attendees_count || 'Current capacity unknown');
                $('#eventCreator').text(event.event.creator || 'No creator information');
            } else {
                $('#eventTitle').text('Event not found').addClass('text-danger');
                $('#eventDescription').text('No description available');
                $('#eventDate').text('No date provided');
                $('#eventLocation').text('No location provided');
                $('#eventPrice').text('Price not specified');
                $('#eventDuration').text('Duration not specified');
                $('#eventMaxCapacity').text('No capacity set');
                $('#eventCurrentCapacity').text('Current capacity unknown');
                $('#eventCreator').text('No creator information');
            }
        },
        error: function (xhr, status, error) {
            $('#eventTitle').text("Failed to load event details").addClass('text-danger');
            console.log("Error:", xhr, status, error);
        }
    });
});
