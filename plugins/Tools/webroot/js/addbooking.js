$(document).ready(function() {
	$("#ToolStartDate").datepicker({
		dateFormat: 'dd/mm/yy',
		minDate: 0
	}).change(setEndDatePicker);

	var date = $("#ToolEndDate").val().split("/");
	$("#ToolEndDate").datepicker({
		dateFormat: 'dd/mm/yy',
		minDate: new Date(date[2], date[1]-1, date[0])
	});

	$('#dayview').height($('#booking').height());
});

function setEndDatePicker() {
	var date = $("#ToolStartDate").val().split("/");
	
	$("#ToolEndDate").val($("#ToolStartDate").val()).datepicker("option", "minDate", new Date(date[2], date[1]-1, date[0]));
}