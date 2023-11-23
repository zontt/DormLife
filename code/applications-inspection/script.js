// Initialize DataTable
$(document).ready(function () {
    $('#myTable').DataTable();

    // Get references to elements
    const menuBar = document.querySelector('#content nav .bx.bx-menu');
    const sidebar = document.getElementById('sidebar');

    // Close the menu by adding the "hide" class on page load
    sidebar.classList.add('hide');

    // Add event listener for the menu toggle button
    menuBar.addEventListener('click', function () {
        sidebar.classList.toggle('hide');
    });

    // Logic for changing the interface on window resize
    window.addEventListener('resize', function () {
        if (window.innerWidth < 768) {
            sidebar.classList.add('hide');
        } else if (window.innerWidth > 576) {
            // Add logic if needed for larger window sizes
        }
    });

    // JavaScript code for handling accept and reject buttons
    $('.accept-button').on('click', function () {
        handleAction('accept', $(this).data('application-id'));
    });

    $('.reject-button').on('click', function () {
        handleAction('reject', $(this).data('application-id'));
    });

    function handleAction(action, applicationId) {
        $.ajax({
            url: 'process_application.php',
            method: 'POST',
            data: { action: action, application_id: applicationId },
            dataType: 'json',
            success: handleResponse,
            error: handleAjaxError
        });
    }

    function handleResponse(response) {
        try {
            // Attempt to parse the JSON response
            if (response.status === 'success') {
                location.reload();
            } else {
                alert('Error: ' + response.message);
            }
        } catch (e) {
            console.error('JSON Parsing Error:', response);
            alert('Error parsing JSON response. See console for details.');
        }
    }

    function handleAjaxError(xhr, textStatus, error) {
        console.error('Ajax Request Failed: ', textStatus, ', Details: ', error);
        alert('Ajax Request Failed: ' + error);
    }
});
//2