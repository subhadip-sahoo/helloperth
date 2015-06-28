var placeSearch, autocomplete;
var componentForm = {
  street_number: 'short_name',
  route: 'long_name',
  locality: 'long_name',
  administrative_area_level_1: 'short_name',
  country: 'long_name',
  postal_code: 'short_name'
};

function initialize() {
    var mapOptions = {
        center: new google.maps.LatLng(-31.9528536, 115.8573389),
        zoom: 13
    };
    var map = new google.maps.Map(document.getElementById('map-canvas'),mapOptions);
    autocomplete = new google.maps.places.Autocomplete((document.getElementById('autocomplete')),{ types: ['geocode'] });
    autocomplete.bindTo('bounds', map);
    var infowindow = new google.maps.InfoWindow();
    var marker = new google.maps.Marker({
      map: map,
      anchorPoint: new google.maps.Point(0, -29)
    });
    if(document.getElementById('geo_latlng').value != ''){
        var geo_latlng = document.getElementById('geo_latlng').value;
        var latlng = geo_latlng.split(',');
        var loc = new google.maps.LatLng(latlng[0], latlng[1]);
        infowindow.close();
        marker.setVisible(false);
        map.setCenter(loc);
        map.setZoom(17);
        marker.setIcon(({
            url: 'http://maps.gstatic.com/mapfiles/place_api/icons/geocode-71.png',
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(35, 35)
        }));
        marker.setPosition(loc);
        marker.setVisible(true);
        infowindow.setContent('<div><strong>' + document.getElementById('geo_name').value + '</strong><br>' + document.getElementById('geo_address').value);
        infowindow.open(map, marker);
    }
    
    google.maps.event.addListener(autocomplete, 'place_changed', function() {
        fillInAddress();
        infowindow.close();
        marker.setVisible(false);
        var place = autocomplete.getPlace();
        if (!place.geometry) {
            return;
        }
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17);
        }
        marker.setIcon(({
            url: place.icon,
            size: new google.maps.Size(71, 71),
            origin: new google.maps.Point(0, 0),
            anchor: new google.maps.Point(17, 34),
            scaledSize: new google.maps.Size(35, 35)
        }));
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);

        var address = '';
        if (place.address_components) {
            address = [
              (place.address_components[0] && place.address_components[0].short_name || ''),
              (place.address_components[1] && place.address_components[1].short_name || ''),
              (place.address_components[2] && place.address_components[2].short_name || '')
            ].join(', ');
        }
        infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
        infowindow.open(map, marker);

        document.getElementById('geo_latlng').value = place.geometry.location;
        document.getElementById('geo_name').value = place.name;
        document.getElementById('geo_address').value = address;
    });
}

function fillInAddress() {
    var place = autocomplete.getPlace();
    for (var component in componentForm) {
      document.getElementById(component).value = '';
      document.getElementById(component).disabled = false;
    }

  for (var i = 0; i < place.address_components.length; i++) {
    var addressType = place.address_components[i].types[0];
    if (componentForm[addressType]) {
        var val = place.address_components[i][componentForm[addressType]];
        document.getElementById(addressType).value = val;
    }
  }
}
function geolocate() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
            var circle = new google.maps.Circle({
                center: geolocation,
                radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
        });
    }
}
google.maps.event.addDomListener(window, 'load', initialize);