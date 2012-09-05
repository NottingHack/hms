<!-- File: /app/View/Group/view.ctp -->

<?php
	$this->Html->addCrumb('Group', '/groups');
	$this->Html->addCrumb('View ' . $group['Group']['grp_description'], '/groups/view/' . $group['Group']['grp_id']);
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
		<?php echo $this->Html->link($member['name'] . '[' . $member['email'] . ']', array('controller' => 'members', 'action' => 'view', $member['member_id'] )); ?>
	</li>
	<?php endforeach; ?>
</ul>

<h2>Actions</h2>

<ul class="nav">
    <li>
        <?php echo $this->Html->link("Edit", array('controller' => 'groups', 'action' => 'edit', $group['Group']['grp_id'])); ?>
    </li>
</ul>

