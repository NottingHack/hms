<!-- File: /app/View/RfidTags/edit.ctp -->

<?php
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb(isset($member['username']) ? $member['username'] : $member['email'], '/members/view/' . $member['id']);
	$this->Html->addCrumb('Registered Cards', '/rfidTags/view/' . $member['id']);
	$this->Html->addCrumb(isset($rfidTagDetails['tagName']) ? $rfidTagDetails['tagName'] : $rfidTagDetails['rfidId'], '/rfidTags/edit/'. $rfidTagDetails['rfidId']);
?>

<?php
  echo $this->Form->create('RfidTag', array(
  	'inputDefaults' => array(
  		'label' => false,
  		//'div' => false,
  		),
  	)
  );
  echo $this->Form->hidden('rfidId');
 ?>
<table>
	<tr>
	<td>Card Serial</td>
   	<td><?php echo $rfidTagDetails['tagSerial']; ?></td>
</tr><tr>
   	<td>Last Used</td>
   	<td><?php echo $rfidTagDetails['lastSeen']; ?></td>
   </tr>
<tr>
   	<td>Card Name</td>
   	<td><?php echo $this->Form->input('friendly_name'); ?></td>
   </tr>
<tr>
   	<td>State</td>
   	<td><?php echo $this->Form->input('state', array('options' => $states)); ?></td>
   </tr>
</table>
<?php
  echo $this->Form->end('Update');
