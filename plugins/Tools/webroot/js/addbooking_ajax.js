$(document).ready(function() {
	$("a.addbooking").click(addBooking);
});

function addBooking(event) {
	alert($(this).attr("href").substr(1));
	return false;
}