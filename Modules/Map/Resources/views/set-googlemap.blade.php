<!-- Include Axios library -->
<script src="{{ asset('frontend') }}/assets/js/axios.min.js"></script>

<!-- Include Google Maps check component -->
<x-website.map.google-map-check />

<script>
    function initMap() {

        // Get Google Maps API key from Laravel settings
        var token = "{{ $setting->google_map_key }}";

        // Use defaults latitude and longitude data 
        var oldlat = {{ Session::has('location') ? Session::get('location')['lat'] : $setting->default_lat }};
        var oldlng = {{ Session::has('location') ? Session::get('location')['lng'] : $setting->default_long }};

        // Create a Google Map instance

        const map = new google.maps.Map(document.getElementById("google-map"), {
            zoom: 5,
            center: {
                lat: oldlat,
                lng: oldlng
            },
        });

        const image = "https://gisgeography.com/wp-content/uploads/2018/01/map-marker-3-116x200.png";

        // Create a marker on the map
        const beachMarker = new google.maps.Marker({

            draggable: true,
            position: {
                lat: oldlat,
                lng: oldlng
            },
            map,
            // icon: image
        });

        // Function to handle updating the map marker and fetching location data
        function handleMapUpdate(lat, lng) {
            // Update the position of the existing marker with the new latitude and longitude
            beachMarker.setPosition({
                lat: lat,
                lng: lng
            });

            // Fetch location information using Google Maps Geocoding API
            axios.post(
                `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${token}`
            ).then((data) => {
                // Check if there's an error message in the API response
                if (data.data.error_message) {
                    toastr.error(data.data.error_message, 'Error!');
                    toastr.error('Your location is not set due to an incorrect API key.', 'Error!');
                }

                // Extract relevant location data from the API response
                const total = data.data.results.length;
                let amount = '';
                if (total > 4) {
                    amount = total - 3;
                }
                const result = data.data.results.slice(amount);
                let country = '';
                let region = '';
                let district = '';

                // Iterate through the results to extract country, region, and district
                for (let index = 0; index < result.length; index++) {
                    const element = result[index];

                    if (element.types[0] == 'country') {
                        country = element.formatted_address;
                    }
                    if (element.types[0] == 'administrative_area_level_1') {
                        const str = element.formatted_address;
                        const first = str.split(' ').shift();
                        region = first;
                    }
                    if (element.types[0] == 'administrative_area_level_2') {
                        const str = element.formatted_address;
                        const first = str.split(' ').shift();
                        district = first;
                    }
                }

                // Create a form and populate it with location data
                var form = new FormData();
                form.append('lat', lat);
                form.append('lng', lng);
                form.append('country', country);
                form.append('region', region);
                form.append('exact_location', district + "," + region + "," + country);

                // Store location data in session
                setLocationSession(form);

                // Update the UI with the fetched location information
                $('.location_country').text(country);
                $('.location_full_address').text(district + "," + region);
                $('.loader_position').addClass('d-none');
                $('.location_secion').removeClass('d-none');
                $('.location_footer').removeClass('d-none');
            }).catch((error) => {
                // Handle errors and display an error message
                toastr.error('Something Went Wrong', 'Error!');
                console.log(error);
            });
        }

        // Listen for a click event on the map
        google.maps.event.addListener(map, 'click',
            function(event) {
                // Show loader and hide location section
                $('.loader_position').removeClass('d-none');
                $('.location_secion').addClass('d-none');

                // Get latitude and longitude from the event
                pos = event.latLng;
                beachMarker.setPosition(pos);
                let lat = beachMarker.position.lat();
                let lng = beachMarker.position.lng();

                // Make a request to Google Geocoding API
                axios.post(
                    `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${token}`
                ).then((data) => {
                    // Check for API error message
                    if (data.data.error_message) {
                        toastr.error(data.data.error_message, 'Error!');
                        toastr.error('Your location is not set because of a wrong API key.', 'Error!');
                    }

                    // Process geocoding results
                    const total = data.data.results.length;
                    let amount = '';
                    if (total > 4) {
                        amount = total - 3;
                    }
                    const result = data.data.results.slice(amount);
                    let country = '';
                    let region = '';
                    let district = '';

                    // Extract relevant location information from results
                    for (let index = 0; index < result.length; index++) {
                        const element = result[index];

                        if (element.types[0] == 'country') {
                            country = element.formatted_address;
                        }
                        if (element.types[0] == 'administrative_area_level_1') {
                            const str = element.formatted_address;
                            const first = str.split(' ').shift();
                            region = first;
                        }
                        if (element.types[0] == 'administrative_area_level_2') {
                            const str = element.formatted_address;
                            const first = str.split(' ').shift();
                            district = first;
                        }
                    }

                    // Create a FormData object with location details
                    var form = new FormData();
                    form.append('lat', lat);
                    form.append('lng', lng);
                    form.append('country', country);
                    form.append('region', region);
                    form.append('exact_location', district + "," + region + "," + country);

                    // Set location session data
                    setLocationSession(form);

                    // Update UI elements with location information
                    $('.location_country').text(country);
                    $('.location_full_address').text(district + "," + region);
                    $('.loader_position').addClass('d-none');
                    $('.location_secion').removeClass('d-none');
                });
            });


        // Listen for a dragend event on the marker
        google.maps.event.addListener(beachMarker, 'dragend',
            function() {
                // Show loader and hide location section
                $('.loader_position').removeClass('d-none');
                $('.location_secion').addClass('d-none');

                // Get latitude and longitude from the beachMarker
                let lat = beachMarker.position.lat();
                let lng = beachMarker.position.lng();

                // Send a geocoding request to Google Maps API
                axios.post(
                    `https://maps.googleapis.com/maps/api/geocode/json?latlng=${lat},${lng}&key=${token}`
                ).then((data) => {
                    // Check if there's an error message in the response
                    if (data.data.error_message) {
                        // Display error messages using toastr library
                        toastr.error(data.data.error_message, 'Error!');
                        toastr.error('Your location is not set because of a wrong API key.', 'Error!');
                    }

                    // Calculate how many results to skip
                    const total = data.data.results.length;
                    let amount = '';
                    if (total > 4) {
                        amount = total - 3;
                    }

                    // Slice the results array based on the calculated amount
                    const result = data.data.results.slice(amount);

                    let country = '';
                    let region = '';
                    let district = '';

                    // Loop through the results to extract location information
                    for (let index = 0; index < result.length; index++) {
                        const element = result[index];

                        // Check the type of location and extract relevant information
                        if (element.types[0] == 'country') {
                            country = element.formatted_address;
                        }
                        if (element.types[0] == 'administrative_area_level_1') {
                            const str = element.formatted_address;
                            const first = str.split(',').shift();
                            region = first;
                        }
                        if (element.types[0] == 'administrative_area_level_2') {
                            const str = element.formatted_address;
                            const first = str.split(' ').shift();
                            district = first;
                        }
                    }

                    // Create a FormData object to send the location information
                    var form = new FormData();
                    form.append('lat', lat);
                    form.append('lng', lng);
                    form.append('country', country);
                    form.append('region', region);
                    form.append('exact_location', district + "," + region + "," + country);

                    // Set the location session using the FormData
                    setLocationSession(form);

                    // Update UI with location information
                    $('.location_country').text(country);
                    $('.location_full_address').text(district + "," + region);

                    // Hide loader and show location section
                    $('.loader_position').addClass('d-none');
                    $('.location_secion').removeClass('d-none');
                });
            });


        // Get the input element with the ID 'searchInput'
        var input = document.getElementById('searchInput');

        // Attach the search input to the top-left corner of the map
        map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

        // Create an autocomplete object using the Google Maps Autocomplete service
        var autocomplete = new google.maps.places.Autocomplete(input);

        // Limit the autocomplete suggestions to the current map bounds
        autocomplete.bindTo('bounds', map);

        // Create an info window to display information about the selected place
        var infowindow = new google.maps.InfoWindow();

        // Create a marker to indicate the selected place
        var marker = new google.maps.Marker({
            map: map,
            anchorPoint: new google.maps.Point(0, -29) // Offset for marker position
        });

        // Listen for the 'place_changed' event on the autocomplete input
        autocomplete.addListener('place_changed', function() {
            // Close the info window and hide the marker
            infowindow.close();
            marker.setVisible(false);

            // Get the selected place details from the autocomplete object
            var place = autocomplete.getPlace();

            // Extract and parse the coordinates from the place's geometry
            const coordinates = String(place.geometry.location);
            const regex = /(-?\d+\.\d+)/g;
            const matches = coordinates.match(regex);

            // If coordinates are successfully extracted
            if (matches && matches.length >= 2) {
                const lat = parseFloat(matches[0]);
                const lng = parseFloat(matches[1]);

                // Call the handleMapUpdate function with the extracted coordinates
                handleMapUpdate(lat, lng);
            } else {
                console.log("Invalid coordinate format.");
            }

            // Adjust the map view based on the selected place's geometry
            if (place.geometry.viewport) {
                map.fitBounds(place.geometry.viewport); // Fit map to the place's viewport
            } else {
                map.setCenter(place.geometry.location); // Center map on the selected place
                map.setZoom(17); // Set zoom level
            }
        });

    }

    // Initialize the map when the window loads
    window.initMap = initMap;
</script>
<script>
    //Google Maps API script URL
    @php
        $link1 = 'https://maps.googleapis.com/maps/api/js?key=';
        $link2 = $setting->google_map_key;
        $Link3 = '&callback=initMap&libraries=places,geometry';
        $scr = $link1 . $link2 . $Link3;
    @endphp;
</script>

<!-- Load the Google Maps API script asynchronously -->
<script src="{{ $scr }}" async defer></script>

<script type="text/javascript">
    // Initialize tooltips
    $(document).ready(function() {
        $("[data-toggle=tooltip]").tooltip()
    })
</script>
