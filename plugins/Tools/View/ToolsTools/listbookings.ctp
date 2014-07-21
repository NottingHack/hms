<?php
/* Breadcrumbs */
$this->Html->addCrumb('Tools', '/tools/');
$this->Html->addCrumb($tool['Tool']['tool_name'], '/tools/view/' . $tool['Tool']['tool_id']);
$this->Html->addCrumb('List Bookings', '#');

?>
<h2>Your Bookings</h2>

<?php

if (count($events) > 0) {
	?>

<table>
	<tr>
		<th>&nbsp;</th>
		<th>Start</th>
		<th>End</th>
		<th>Type</th>
		<th>Notes</th>
	</tr>
<?php
	
	foreach ($events as $event) {
		$img_options = array(
							'alt'	=> 'Delete Booking',
							'title'	=> 'Delete Booking',
							'url'	=> array('plugin' => 'Tools', 'controller' => 'ToolsTools', 'action' => 'deleteBooking', $tool['Tool']['tool_id'], $event['id']),
							);
?>
	<tr>
		<td width="25"><?php echo($this->Html->image("Tools.icon_delete.png", $img_options)) ?></td>
		<td><?php echo($event['start']->format('jS F Y @ H:i')); ?></td>
		<td><?php echo($event['end']->format('jS F Y @ H:i')); ?></td>
		<td><?php echo(ucwords($event['type'])); ?></td>
		<td>&nbsp;</td>
	</tr>
<?php
	}
?>
</table>

<?php
}

?>