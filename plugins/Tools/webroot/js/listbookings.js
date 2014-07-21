var deleteLink;

$(document).ready(function() {
	$("a:has(img.delete)").click(confirmDelete);

	$( "#dialog-confirm" ).dialog({
			resizable: false,
			height: 200,
			width: 300,
			modal: true,
			buttons: {
				Yes: function() {
					$( this ).dialog( "close" );
					deleteBooking();
				},
				No: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	$( "#dialog-confirm" ).dialog("close");
});

function confirmDelete(event) {
	deleteLink = this.href;
	start = $(this).parent().parent().children("td:nth-child(2)").text();
	$( "#dialog-confirm p" ).append(start + "?");
	$( "#dialog-confirm" ).dialog('open');
	return false;
}

function deleteBooking() {
	window.location = deleteLink;
}