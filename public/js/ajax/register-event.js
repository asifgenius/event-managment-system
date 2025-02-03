$(document).ready(function () {

  var eventId = document.getElementById("eventId").value;

  $.ajax({
    url: `/event_managment/api/events/${eventId}`,
    type: "GET",
    contentType: "application/json",
    dataType: "json",
    success: function (response) {
      if (response) {
        var event = response;

        $("#eventTitle").text(event.event.title || "No title available");
        $("#eventDescription").text(
          event.event.description || "No description available"
        );
        $("#eventLocation").text(
          event.event.location || "No location available"
        );
        $("#eventPrice").text(
          event.event.price !== "0" ? "$" + event.price : "Price not specified"
        );
        $("#eventDuration").text(event.event.duration + " hours");
        $("#eventMaxCapacity").text(
          event.event.max_capacity || "No capacity set"
        );
        $("#eventCurrentCapacity").text(
          event.attendees_count || "Current capacity unknown"
        );
        $("#eventCreator").text(
          event.event.creator || "No creator information"
        );
      } else {
        $("#eventTitle").text("Event not found").addClass("text-danger");
        $("#eventDescription").text("No description available");
        $("#eventDate").text("No date provided");
        $("#eventLocation").text("No location provided");
        $("#eventPrice").text("Price not specified");
        $("#eventDuration").text("Duration not specified");
        $("#eventMaxCapacity").text("No capacity set");
        $("#eventCurrentCapacity").text("Current capacity unknown");
        $("#eventCreator").text("No creator information");
      }
    },
    error: function (xhr, status, error) {
      $("#eventTitle")
        .text("Failed to load event details")
        .addClass("text-danger");
      console.log("Error:", xhr, status, error);
    },
  });

  $("#registerForm").on("submit", function (e) {
    e.preventDefault();
    var eventId = document.getElementById("eventId").value;

    if (!eventId) {
      $("#feedback").html('<p style="color: red;">Event ID is missing.</p>');
      return;
    }

    var formData = new FormData(this);

    $.ajax({
      url: "/event_managment/events/registration/" + eventId,
      method: "POST",
      data: formData,
      dataType: "json",
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.status === "success") {
          successMessageNotify(response.message);
        } else {
          successMessageNotify(response.error);
        }
      },
      error: function (xhr, status, error) {
        alert("An error occurred. Please try again.");
      },
    });
  });
});
