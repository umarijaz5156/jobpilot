<script src="{{ asset('frontend') }}/assets/js/axios.min.js"></script>
<x-map.leaflet.map_scripts />
<x-map.leaflet.autocomplete_scripts />
<script>
    var oldlat = {{ Session::has('location') ? Session::get('location')['lat'] : $setting->default_lat }};
    var oldlng = {{ Session::has('location') ? Session::get('location')['lng'] : $setting->default_long }};

    // Map preview
    var element = document.getElementById('leaflet-map');

    // Height has to be set. You can do this in CSS too.
    element.style = 'height:300px;';

    // Create Leaflet map on map element.
    var leaflet_map = L.map(element);

    // Add OSM tile layer to the Leaflet map.
    L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(leaflet_map);

    // Target's GPS coordinates.
    var target = L.latLng(oldlat, oldlng);

    // Set map's center to target with zoom 14.
    const zoom = 7;
    leaflet_map.setView(target, zoom);

    // Place a marker on the same location.
    var markers = new L.FeatureGroup();
    var marker = L.marker(target, {
        draggable: true
    });

    function handleMapClick(lat, lng) {
        $('.location_footer').removeClass('d-none');
        $('.loader_position').removeClass('d-none');
        $('.location_secion').addClass('d-none');

        leaflet_map.panTo(new L.LatLng(lat, lng));

        // Clear old markers
        markers.clearLayers();

        var marker = L.marker([lat, lng], {
            draggable: true
        });

        // Add marker to the map after a slight delay
        setTimeout(() => {
            marker.addTo(markers);
            markers.addTo(leaflet_map);
        }, 100);

        // Fetch location details from OpenStreetMap's Nominatim API
        axios.get(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)
            .then(function(response) {
                // Extract address components or fallback options
                var data = response.data;
                var country = data?.address?.country ? data?.address?.country : 'Country is not selected';
                var region = data?.address?.state ? data?.address?.state : data?.address?.province ? data?.address
                    ?.province : data?.address?.village ? data?.address?.village : data?.address?.country ? data
                    ?.address?.country : "Division is not selected";
                var district = data?.address?.state_district ? data?.address?.state_district : data?.address
                    ?.province ? data?.address?.province : data?.address?.county ? data?.address?.county : data
                    ?.address?.municipality ? data?.address?.municipality : data?.address?.city ? data?.address
                    ?.city : data?.address?.region ? data?.address?.region : data?.address?.town ? data?.address
                    ?.town : data?.address?.village ? data?.address?.village : data?.address?.state ? data?.address
                    ?.state : data?.address?.country ? data?.address?.country : "District is not selected";
                var place = data?.address?.city ? data?.address?.city : "";

                // Create a form data object to store location details
                var form = new FormData();
                form.append('lat', lat);
                form.append('lng', lng);
                form.append('country', country);
                form.append('region', region);
                form.append('district', district);
                form.append('place', place);
                form.append('exact_location', district + "," + region + "," + country);

                // Store location details in a session 
                setLocationSession(form);

                // Displayed location information
                $('.location_country').text(country);
                $('.location_full_address').text(district + "," + region);
                $('.loader_position').addClass('d-none');
                $('.location_secion').removeClass('d-none');
                $('.location_footer').removeClass('d-none');
            })
            .catch(function(error) {
                // Display an error message if the API request fails
                toastr.error('Something Went Wrong', 'Error');
                console.log(error);
            });
    }

    function updateMapWithCity(cityName) {
        axios.get(`https://nominatim.openstreetmap.org/search?format=json&q=${cityName}`)
            .then(function(response) {
                if (response.data.length ==1) {
                    const firstResult = response.data[0];
                    const lat = parseFloat(firstResult.lat);
                    const lng = parseFloat(firstResult.lon);
                    handleMapClick(lat, lng);
                } else {
                    const firstResult = response.data[1];
                    const lat = parseFloat(firstResult.lat);
                    const lng = parseFloat(firstResult.lon);
                    handleMapClick(lat, lng);
                }
            })
            .catch(function(error) {
                toastr.error('Something Went Wrong', 'Error');
                console.log(error);
            });
    }

    // Event listener for search input
    const searchInput = document.getElementById('leaflet_search');
    searchInput.addEventListener('change', function() {
        const cityName = this.value;
        if (cityName) {

            updateMapWithCity(cityName);
        }
    });

    // Check if old latitude and longitude values exist
    if (oldlat && oldlng) {
        handleMapClick(oldlat, oldlng);
    }

    // Event listener for when the map is clicked
    leaflet_map.on('click', function(e) {
        let lat = e.latlng.lat;
        let lng = e.latlng.lng;
        handleMapClick(lat, lng);
    });

    // Add the marker to the markers layer
    marker.addTo(markers);

    // Add the markers layer to the leaflet map
    markers.addTo(leaflet_map);


    // marker drugEnd
    marker.on("dragend", function(e) {
        $('.location_footer').removeClass('d-none');
        $('.loader_position').removeClass('d-none');
        $('.location_secion').addClass('d-none');

        var marker = e.target;
        var position = marker.getLatLng();
        leaflet_map.panTo(new L.LatLng(position.lat, position.lng));
        // call api to get address from lat & lng
        axios.get(
            `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${position.lat}&lon=${ position.lng}`
            ).then(function(response) {
            var data = response.data;
            var country = data?.address?.country ? data?.address?.country : 'Country is not selected';
            var region = data?.address?.state ? data?.address?.state : data?.address?.province ? data
                ?.address?.province : data?.address?.village ? data?.address?.village : data?.address
                ?.country ? data?.address?.country : "Division is not selected";
            var district = data?.address?.state_district ? data?.address?.state_district : data?.address
                ?.province ? data?.address?.province : data?.address?.county ? data?.address?.county :
                data?.address?.municipality ? data?.address?.municipality : data?.address?.city ? data
                ?.address?.city : data?.address?.region ? data?.address?.region : data?.address?.town ?
                data?.address?.town : data?.address?.village ? data?.address?.village : data?.address
                ?.state ? data?.address?.state : data?.address?.country ? data?.address?.country :
                "District is not selected";
            var place = data?.address?.city ? data?.address?.city : "";

            /// Create a form data object to store location details
            var form = new FormData();
            form.append('lat', position.lat);
            form.append('lng', position.lng);

            form.append('country', country);
            form.append('region', region);
            form.append('district', district);
            form.append('place', place);
            form.append('exact_location', district + "," + region + "," + country);

            // Store location details in a session 
            setLocationSession(form);

            //Displayed location information
            $('.location_country').text(country);
            $('.location_full_address').text(district + "," + region);
            $('.loader_position').addClass('d-none');
            $('.location_secion').removeClass('d-none');
            $('.location_footer').removeClass('d-none');
        }).catch(function(error) {
            // Display an error message if the API request fails
            toastr.error('Something Went Wrong', 'Error');
            console.log(error);
        });
    });
    // marker drugEnd END

    // map click set marker
    leaflet_map.on('click', function(e) {
        $('.location_footer').removeClass('d-none');
        $('.loader_position').removeClass('d-none');
        $('.location_secion').addClass('d-none');

        let lat = e.latlng.lat;
        let lng = e.latlng.lng;
        leaflet_map.panTo(new L.LatLng(lat, lng));

        markers.clearLayers(); //c clear old merkers

        // re init marker
        var marker = L.marker([lat, lng], {
            draggable: true
        });
        setTimeout(() => {
            marker.addTo(markers);
            markers.addTo(leaflet_map);
        }, 100);
        //  re init marker END

        // call api to get address from lat & lng
        axios.get(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`).then(
            function(response) {
                // Extract address components or fallback options
                var data = response.data;
                var country = data?.address?.country ? data?.address?.country : 'Country is not selected';
                var region = data?.address?.state ? data?.address?.state : data?.address?.province ? data
                    ?.address?.province : data?.address?.village ? data?.address?.village : data?.address
                    ?.country ? data?.address?.country : "Division is not selected";
                var district = data?.address?.state_district ? data?.address?.state_district : data?.address
                    ?.province ? data?.address?.province : data?.address?.county ? data?.address?.county :
                    data?.address?.municipality ? data?.address?.municipality : data?.address?.city ? data
                    ?.address?.city : data?.address?.region ? data?.address?.region : data?.address?.town ?
                    data?.address?.town : data?.address?.village ? data?.address?.village : data?.address
                    ?.state ? data?.address?.state : data?.address?.country ? data?.address?.country :
                    "District is not selected";
                var place = data?.address?.city ? data?.address?.city : "";
                // Create a form data object to store location details
                var form = new FormData();
                form.append('lat', lat);
                form.append('lng', lng);

                form.append('country', country);
                form.append('region', region);
                form.append('district', district);
                form.append('place', place);
                form.append('exact_location', district + "," + region + "," + country);

                // Store location details in a session
                setLocationSession(form);

                // Update the displayed location information
                $('.location_country').text(country);
                $('.location_full_address').text(district + "," + region);
                $('.loader_position').addClass('d-none');
                $('.location_secion').removeClass('d-none');
                $('.location_footer').removeClass('d-none');
            }).catch(function(error) {

            // Display an error message if the API request fails
            toastr.error('Something Went Wrong', 'Error');
            console.log(error);
        });
    });
    // map click set marker  END
</script>
