<!-- File: /app/View/MailingLists/index.ctp -->

<?php
	$this->Html->addCrumb('Mailing Lists', '/mailinglists');
?>

<?php
	if(count($mailingLists) <= 0):
	
		echo 'No mailing lists';
	
	else:
?>

<table>
	<tr>
		<th>List</th>
		<th>No. Subscribers</th>
		<th>No. Member Subscribers</th>
	</tr>

<?php
	foreach($mailingLists as $list):
?>

	<tr>
		<td><?php echo $this->Html->link($list['name'], array('controller' => 'mailinglists', 'action' => 'view', $list['id'])); ?></td>
		<td><?php echo $list['stats']['member_count']; ?></td>
		<td><?php echo $list['stats']['hms_member_count']; ?></td>
	</tr>

<?php
	endforeach;
?>

</table>

<?php
	endif;
?>