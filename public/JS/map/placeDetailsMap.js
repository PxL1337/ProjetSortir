function showPlaceDetailMap(latitude, longitude, placeName, streetAddress) {
    var map = L.map('mapId').setView([latitude, longitude], 17);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
    }).addTo(map);

    L.marker([latitude, longitude]).addTo(map)
        .bindPopup("<b>" + placeName + "</b><br/>" + streetAddress).openPopup();
}