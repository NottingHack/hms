
function createAutofillElement() {
	//Create the label element
	var label = $("<label>").text('Autofill:');
	//Create the input element
	var input = $('<input type="text">').attr({id: 'AddressAutofill', name: 'AddressAutofill'});

	input.appendTo(label);
	label.insertAfter('#MemberMemberId');
}

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
	// Create the autocomplete object, restricting the search
	// to geographical location types.
	autocomplete = new google.maps.places.Autocomplete(
		document.getElementById('AddressAutofill'),
		{ 
			types: ['geocode'],
			componentRestrictions: {country: 'gb'},
		}
	);

	// When the user selects an address from the dropdown,
	// populate the address fields in the form.
	google.maps.event.addListener(autocomplete, 'place_changed', function() {
		fillInAddress();
	});
}

// The START and END in square brackets define a snippet for our documentation:
// [START region_fillform]
function fillInAddress() {
	// Get the place details from the autocomplete object.
	var place = autocomplete.getPlace();

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
	document.getElementById(elementId).value = value;
}

// [END region_fillform]

$(function() {
	createAutofillElement();
	initialize();
});