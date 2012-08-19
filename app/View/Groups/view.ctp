<!-- File: /app/View/Group/view.ctp -->

<h1><?php echo $group['Group']['grp_description'] ?></h1>

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
		<?php echo $this->Html->link($member['name'] . '[' . $member['email'] . ']', array('controller' => 'members', 'action' => 'view', $member['member_id'] )); ?>
	</li>
	<?php endforeach; ?>
</ul>
