function initialize() {

    $('form').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });
    // initMap();
    // initAutocomplete();
    const locationInputs = document.getElementsByClassName("map-input");
    // alert(locationInputs[0].value)
    // alert(locationInputs[0].value);
    const autocompletes = [];
    const geocoder = new google.maps.Geocoder;
    var infowindow = new google.maps.InfoWindow();
    for (let i = 0; i < locationInputs.length; i++) {
        const input = locationInputs[i];
        const fieldKey = input.id.replace("-input", "");
        // const isEdit = document.getElementById(fieldKey + "_latitude").value != '' && document.getElementById(fieldKey + "_longitude").value != '';

        const latitude = parseFloat(document.getElementById(fieldKey + "_latitude").value) || 21.027763;
        const longitude = parseFloat(document.getElementById(fieldKey + "_longitude").value) || 105.834160;

        const map = new google.maps.Map(document.getElementById(fieldKey + '-map'), {
            center: { lat: latitude, lng: longitude },
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });
        const marker = new google.maps.Marker({
            map: map,
            position: { lat: latitude, lng: longitude },
            draggable: true
        });

        // marker.setVisible(isEdit);

        const autocomplete = new google.maps.places.Autocomplete(input);
        autocomplete.key = fieldKey;
        autocompletes.push({ input: input, map: map, marker: marker, autocomplete: autocomplete });
    }

    for (let i = 0; i < autocompletes.length; i++) {
        const input = autocompletes[i].input;
        const autocomplete = autocompletes[i].autocomplete;
        const map = autocompletes[i].map;
        const marker = autocompletes[i].marker;

        google.maps.event.addListener(marker, 'dragend', function () {
            geocoder.geocode({ 'latLng': marker.getPosition() }, function (results, status) {
                if (status == google.maps.GeocoderStatus.OK) {
                    if (results[0]) {
                        const lat = marker.getPosition().lat(); //results[0].geometry.location.lat();
                        const lng = marker.getPosition().lng(); //results[0].geometry.location.lng();
                        setLocationCoordinates(autocomplete.key, lat, lng);
                        infowindow.setContent(input.value);
                        infowindow.open(map, marker);
                        // console.log(marker.getPosition().lat(), google.maps.GeocoderStatus.OK, status);
                    }

                }

            });

        });

        google.maps.event.addListener(autocomplete, 'place_changed', function (locationInputs) {
            marker.setVisible(false);
            const place = autocomplete.getPlace();
            geocoder.geocode({ 'placeId': place.place_id }, function (results, status, locationInputs) {
                if (status === google.maps.GeocoderStatus.OK) {
                    const lat = marker.getPosition().lat(); //results[0].geometry.location.lat();
                    const lng = marker.getPosition().lng(); //results[0].geometry.location.lng();
                    setLocationCoordinates(autocomplete.key, lat, lng);

                } else {
                    const lat = 21.027763;
                    const lng = 105.834160;
                    marker.setVisible(true);
                    map.setZoom(14);
                    setLocationCoordinates(autocomplete.key, lat, lng);

                }
                console.log(marker.getPosition().lat(), google.maps.GeocoderStatus.OK, status+'aaa');
                infowindow.setContent(input.value);
                infowindow.open(map, marker);
            });

            if (!place.geometry) {
                window.alert("Không xác đinh được toạ độ: '" + place.name + "'\nBạn có thể di chuyển vị trí trên map tới địa chỉ của bạn.");

                // input.value = "";
                return;
            }
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport);
            } else {
                map.setCenter(place.geometry.location);
                map.setZoom(17);
            }
            marker.setPosition(place.geometry.location);
            marker.setVisible(true);
            google.maps.event.addDomListener(window, 'load', initialize);
        });
    }
}

function setLocationCoordinates(key, lat, lng) {
    // const latitudeField = document.getElementById(key + "_" + "latitude");
    // const longitudeField = document.getElementById(key + "_" + "longitude");
    // latitudeField.value = lat;
    // longitudeField.value = lng;
    $('#' + key + "_" + "latitude").val(lat);
    $('#' + key + "_" + "longitude").val(lng);
}
