<?php
/* Breadcrumbs */
$this->Html->addCrumb('Tools', '/tools/');

$tool_status = array(
	'IN_USE'	=>	'In Use',
	'FREE'		=>	'Available',
	'DISABLED'	=>	'Disabled',
	);

?>
<h2>Tools</h2>

<?php

if (count($tools) > 0) {
	?>

<table>
	<tr>
		<th>&nbsp;</th>
		<th>Tool</th>
		<th>Status</th>
		<th>Cost per hour</th>
		<th>Next booking</th>
	</tr>
<?php

	foreach ($tools as $tool) {
		// Tool status may not be set, so need to deal with that.
		if ((isset($tool['Tool']['tool_status']) && $tool['Tool']['tool_status'] == '') || !isset($tool['Tool']['tool_status'])) {
			$tool['Tool']['tool_status'] = 'DISABLED';
		}

		$img_options = array(
			'alt'	=> 'Access Tool',
			'title'	=> 'Access Tool',
			'url'	=> $tool['Tool']['view']['link'],
			);
		?>
	<tr>
		<td width="25"><?php echo($this->Html->image("Tools." . $tool['Tool']['view']['image'], $img_options)) ?></td>
		<td><?php echo($this->Html->link($tool['Tool']['tool_name'], $tool['Tool']['view']['link'])); ?></td>
		<td<?php echo($tool['Tool']['tool_status'] == "DISABLED" ? ' style="background-color: #FF0000"' : ''); ?>><?php echo($tool_status[$tool['Tool']['tool_status']]); ?></td>
		<td>&pound;<?php echo(number_format($tool['Tool']['tool_pph'] / 100, 2)); ?></td>
		<td><?php echo($tool['Tool']['next_booking'] ? $tool['Tool']['next_booking']->format('jS F Y @ H:i') : "None"); ?></td>
	</tr>
<?php
	}

?>
</table>

<?php
}

?>