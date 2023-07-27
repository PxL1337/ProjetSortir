$(document).ready(function() {
    var timeout;
    $('#place_rue').keyup(function() {
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            $.ajax({
                method: 'GET',
                url: `https://nominatim.openstreetmap.org/search?format=json&limit=5&q=${$('#place_rue').val()}`,
                success: function(data) {
                    console.log(data);
                    var html = '';
                    for(var i = 0; i < data.length; i++) {
                        html += `<div class="address-suggestion" data-lat="${data[i].lat}" data-lon="${data[i].lon}">${data[i].display_name}</div>`;
                    }
                    $('#suggestions').html(html);
                }
            });
        }, 500);
    });

    $(document).on('click', '.address-suggestion', function() {
        $('#place_rue').val($(this).text());
        $('#place_latitude').val($(this).data('lat'));
        $('#place_longitude').val($(this).data('lon'));
        $('#suggestions').html('');
    });
});
