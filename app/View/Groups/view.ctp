<!-- File: /app/View/Group/view.ctp -->

<?php
	$this->Html->addCrumb('Group', '/groups');
	$this->Html->addCrumb($group['Group']['grp_description'], '/groups/view/' . $group['Group']['grp_id']);

	$this->Nav->add("Edit", array('controller' => 'groups', 'action' => 'edit', $group['Group']['grp_id']));
?>

<h2>Permissions</h2>
<ul>
	<?php foreach ($group['Permission'] as $permission): ?>
	<li>
		<?php echo $permission['permission_desc']; ?>
	</li>
	<?php endforeach; ?>
</ul>

<h2>Members</h2>
<ul>
	<?php foreach ($group['Member'] as $member): ?>
	<li>
		<?php echo $this->Html->link($member['name'], array('controller' => 'members', 'action' => 'view', $member['member_id'] )); ?>
	</li>
	<?php endforeach; ?>
</ul>
