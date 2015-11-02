var approveLink;

$(document).ready(function() {
	$("a.approve_member").click(confirmApprove);

	$( "#dialog-confirm" ).dialog({
			resizable: false,
			height: 200,
			width: 300,
			modal: true,
			buttons: {
				Yes: function() {
					$( this ).dialog( "close" );
					approveMember();
				},
				No: function() {
					$( this ).dialog( "close" );
				}
			}
		});
	$( "#dialog-confirm" ).dialog("close");
});

function confirmApprove(event) {
	approveLink = this.href;
	$( "#dialog-confirm" ).dialog('open');
	return false;
}

function approveMember() {
	window.location = approveLink;
}