let currentPage = 1;
let sortBy = 'title';
let order = 'ASC';
let filter = '';
let itemsPerPage = 5;

function fetchEvents() {
    const data = {
        page: currentPage,
        itemsPerPage: itemsPerPage,
        sortBy: sortBy,
        order: order,
        filter: filter,
    };

    $.ajax({
        url: '/event_managment/events/list',
        type: 'GET',
        dataType: 'json',
        data: data,
        contentType: 'application/json',
        success: function (result) {
            if (result.error) {
                alert(result.error);
                return;
            }

            renderEvents(result.events);
            renderPagination(result.totalPages);
        },
        error: function (xhr, status, error) {
            console.error('Error fetching events:', error);
            $('#eventList').html('<p class="text-danger">Failed to load events. Please try again later.</p>');
        }
    });
}

function renderEvents(events) {
    const eventList = $('#eventList');
    eventList.empty();

    const role = document.getElementById('role').value;

    if (events.length === 0) {
        eventList.html('<p>No events found.</p>');
        return;
    }

    events.forEach((event) => {
        let card = `
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Title: ${event.title}</h5>
                    <p><strong>Location:</strong> ${event.location}</p>
                    <p><strong>Description:</strong> ${event.description}</p>
                    <p><strong>Duration:</strong> ${event.duration} hours</p>
                    <p><strong>Date:</strong> ${event.start_date}</p>
                    <p><strong>Created by:</strong> ${event.creator}</p>
                    <a href="/event_managment/events/${event.id}" data-event-id='${event.id}' class="btn btn-primary"><i class="fa fa-eye" aria-hidden="true"></i></a>
        `;

        if (role === 'admin') {
            card += `
                <a href="/event_managment/update_events/${event.id}" class="btn btn-secondary updateEventBtn" data-id="${event.id}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                <a class="btn btn-danger deleteEventBtn" data-id="${event.id}"><i class="fa fa-trash" aria-hidden="true"></i></a>
                <a href="/event_managment/download/events/csv/${event.id}" class="btn btn-warning text-white" data-id="${event.id}"><i class="fa fa-download" aria-hidden="true"></i></a>
            `;
        }

        if (role === 'user') {
            card += `
                <a href="/event_managment/events/registration/${event.id}" class="btn btn-danger">Register for Event</a>
            `;
        }

        card += `</div></div>`;

        eventList.append(card);
    });

}

function renderPagination(totalPages) {
    const pagination = $('#pagination');
    pagination.empty();

    for (let i = 1; i <= totalPages; i++) {
        const isActive = i === currentPage ? 'active' : '';
        const isDisabled = i === currentPage ? 'disabled' : '';

        pagination.append(`
            <li class="page-item ${isActive}">
                <a class="page-link ${isDisabled}" href="#" onclick="changePage(${i})">${i}</a>
            </li>
        `);
    }
}

function changePage(page) {
    currentPage = page;
    fetchEvents();
}

$('#sortFilter').on('change', function () {
    const [sort, sortOrder] = this.value.split('-');
    sortBy = sort;
    order = sortOrder;
    currentPage = 1;
    fetchEvents();
});

$('#itemsPerPageFilter').on('change', function () {
    itemsPerPage = parseInt(this.value, 10);
    currentPage = 1;
    fetchEvents();
});

$('#searchButton').on('click', function () {
    filter = $('#searchFilter').val().trim();
    currentPage = 1;
    fetchEvents();
});
$(document).on('click', '.deleteEventBtn', function (e) {
    e.preventDefault();
    let eventId = $(this).data('id');
    if (!confirm('Are you sure you want to delete this event?')) {
        return;
    }

    $.ajax({
        url: '/event_managment/events/' + eventId,
        type: 'DELETE',
        dataType: 'json',
        contentType: 'application/json',
        success: function (response) {
            if (response.error) {
                alert(response.error);
                return;
            }

            $(`.deleteEventBtn[data-id="${eventId}"]`).closest('.card').fadeOut(500, function () {
                $(this).remove();
            });
            alert('Event deleted successfully.');
        },
        error: function (xhr, status, error) {
            console.error('Error deleting event:', xhr.responseText);
            alert('Failed to delete the event. Please try again.');
        }
    });
});
fetchEvents();
