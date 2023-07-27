$(document).ready(function () {

    // Function to perform the search
    let search = function() {
        // Empty the suggestions field every time the 'nom' or 'rue' field changes
        $('#suggestions').empty();

        let city = $('#place_city option:selected').text();
        let query = $('#place_nom').val() + ' ' + $('#place_rue').val();

        // Only perform the search if there are at least 3 characters in the query
        if (query.length > 2) {
            let url = `https://nominatim.openstreetmap.org/search?format=json&limit=5&q=${city} ${query}`;

            console.log( 'url appelée : ' + url);  // Print the request URL

            $.getJSON(url, function (data) {

                console.log(data);  // Print the response data

                // Use each item in the response to form a list of suggestions
                $.each(data, function (key, val) {

                    let suggestion = '<div><a href="#" class="suggestion" data-lat="' + val.lat + '" data-lon="' + val.lon + '" data-display-name="' + val.display_name + '">' + val.display_name + '</a></div>';
                    $('#suggestions').append(suggestion);

                });
            });
        }
    };

    // Trigger the search when the 'nom' field changes
    $('#place_nom').keyup(function () {
        search();
    });

    // Trigger the search when the 'rue' field changes
    $('#place_rue').keyup(function () {
        search();
    });

    // When a suggestion is clicked, fill the 'nom', 'rue', 'latitude', and 'longitude' fields and empty the suggestions
    $(document).on('click', '.suggestion', function (e) {
        e.preventDefault();
        let fullName = $(this).data('display-name');

        // Split the full name into an array
        let nameArray = fullName.split(',');

        // The place name is the first element in the array
        let placeName = nameArray[0];

        // The street address is usually the second and third elements in the array
        let streetAddress = nameArray[1] + ',' + nameArray[2];

        // Trim whitespace from the start and end of the place name and street address
        placeName = placeName.trim();
        streetAddress = streetAddress.trim();

        $('#place_nom').val(placeName);
        $('#place_rue').val(streetAddress);
        $('#place_latitude').val($(this).data('lat'));
        $('#place_longitude').val($(this).data('lon'));
        $('#suggestions').empty();
    });

});
