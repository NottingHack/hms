$(document).ready(function() {
	$(".datepicker").datepicker({
		'dateFormat': 'dd/mm/yy'
	});

	$('#dayview').height($('#booking').height());
});