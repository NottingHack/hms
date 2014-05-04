
function createAutofillElement() {
	var mapHtml = '<p>If you like, you can start typing your address below and we\'ll get google to fill in that part</p>' +
		'<input id="autofill-pac-input" class="autofill-controls" type="text" placeholder="Find your address"></input>' + 
		'<div id="autofill-map-canvas"></div>';
	$('#address_map').html(mapHtml);
	$('#address_map').show();
}

function initialize() {

	createAutofillElement();

	var mapOptions = {
		center: new google.maps.LatLng(52.95581,-1.13498),
		zoom: 18
	};
	var map = new google.maps.Map(document.getElementById('autofill-map-canvas'), mapOptions);

	var input = (document.getElementById('autofill-pac-input'));
	map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

	var autocompleteOptions = {
		types: ['geocode'],
		componentRestrictions: {country: 'gb'},
	};
	var autocomplete = new google.maps.places.Autocomplete(input, autocompleteOptions);
	autocomplete.bindTo('bounds', map);

	var infowindow = new google.maps.InfoWindow({
		maxWidth: "500"
	});
	var marker = new google.maps.Marker({
		map: map
	});

	google.maps.event.addListener(autocomplete, 'place_changed', function() {
		infowindow.close();
		marker.setVisible(false);
		var place = autocomplete.getPlace();
		if (!place.geometry) {
			return;
		}

		// If the place has a geometry, then present it on a map.
		if (place.geometry.viewport) {
			map.fitBounds(place.geometry.viewport);
		} else {
			map.setCenter(place.geometry.location);
			map.setZoom(17);  // Why 17? Because it looks good.
		}
		marker.setIcon(/** @type {google.maps.Icon} */({
			url: place.icon,
			size: new google.maps.Size(71, 71),
			origin: new google.maps.Point(0, 0),
			anchor: new google.maps.Point(17, 34),
			scaledSize: new google.maps.Size(35, 35)
		}));
		marker.setPosition(place.geometry.location);
		marker.setVisible(true);

		fillInAddress(place);

		infowindow.setContent('<div id="autofill-infowindow">' + place.name + '</div>');
		infowindow.open(map, marker);
	});
}

function fillInAddress(place) {
	var number = findAddressComponentInPlace(place, 'street_number');
	var street = findAddressComponentInPlace(place, 'route', true);
	var city = findAddressComponentInPlace(place, 'locality', true);
	var postcode = findAddressComponentInPlace(place, 'postal_code', true);

	setAddressValue('MemberAddress1', number + ' ' + street);
	setAddressValue('MemberAddressCity', city);
	setAddressValue('MemberAddressPostcode', postcode);
}

function findAddressComponentInPlace(place, desiredComponent, longName) {
	for (var i = 0; i < place.address_components.length; i++) {
		var component = place.address_components[i];
		if(component.types[0] == desiredComponent)
		{
			if(longName)
			{
				return component.long_name;
			}
			return component.short_name;
		}
	}
	return '';
}

function setAddressValue(elementId, value) {
	document.getElementById(elementId).value = value.trim();
}

google.maps.event.addDomListener(window, 'load', initialize);