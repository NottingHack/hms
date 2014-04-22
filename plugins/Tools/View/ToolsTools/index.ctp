<?php
/* Breadcrumbs */
$this->Html->addCrumb('Tools', '/tools/');
?>
<h2>Tools</h2>

<?php

if (count($tools) > 0) {
	?>

<table>
	<tr>
		<th>Tool</th>
		<th>Status</th>
		<th>Cost per hour</th>
		<th>Next booking</th>
		<th>Actions</th>
	</tr>
<?php

	foreach ($tools as $tool) {
		?>

	<tr>
		<td><?php echo($tool['Tool']['tool_name']); ?></td>
		<td<?php echo($tool['Tool']['tool_status'] == "DISABLED" ? ' style="background-color: #FF0000"' : ''); ?>><?php echo(ucwords(strtolower($tool['Tool']['tool_status']))); ?></td>
		<td>&pound;<?php echo(number_format($tool['Tool']['tool_pph'] / 100, 2)); ?></td>
		<td><?php echo($tool['Tool']['next_booking']->format('jS F Y @ H:i')); ?></td>
		<td>
<?php
		$i = 1;
		foreach ($tool['Tool']['actions'] as $text => $action) {
			?>
			<a href="<?php echo($this->Html->url($action)); ?>"><?php echo($text); ?></a>
<?php
			echo($i == count($tool['Tool']['actions']) ? '' : ' | ');
			$i++;
		}
?>
		</td>
	</tr>
<?php
	}

?>
</table>

<?php
}

?>