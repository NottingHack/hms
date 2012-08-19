<!-- File: /app/View/Group/index.ctp -->

<h1>Groups</h1>

<table>
	<tr>
		<th></th>
		<?php foreach ($groups as $group): ?>
			<th>
				<?php echo $this->Html->link($group['Group']['grp_description'], array('controller' => 'groups', 'action' => 'edit', $group['Group']['grp_id'] )); ?>
			</th>
		<?php endforeach; ?>
	</tr>

	<?php 
		# Start the row with the description of the permission
		foreach ($permissions as $permission) {
			echo '<tr>';
			echo '<td>' . $permission['Permission']['permission_desc'] . '</td>';

			# Show if each group has this permission
			foreach ($groups as $group) {
				echo '<td>';
				$hasPermission = false;
				foreach ($group['Permission'] as $groupPerm) {
					if($groupPerm['permission_code'] === $permission['Permission']['permission_code'])
					{
						$hasPermission = true;
						break;
					}
				}
				
				echo $hasPermission ? "X" : "-";
				echo '</td>';
			}

			echo '</tr>';
		}
	?>

</table>

<ul class="nav">
    <li>
        <?php echo $this->Html->link("Add Group", array('controller' => 'groups', 'action' => 'add')); ?>
    </li>
</ul>
