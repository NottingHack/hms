<!-- File: /app/View/Member/upload_csv.ctp -->

<?php
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb('Upload CSV', '/members/uploadCsv');
?>

<?php if($memberList == null): ?>
	<?php
		echo $this->Form->create('FileUpload', array('type' => 'file'));
		echo $this->Form->input('filename', array('type' => 'file'));
		echo $this->Form->end('Upload');
	?>
<?php else: ?>

	<p>
		Found payments for the following members:
	</p>

	<table>
        <tr>
	        <th>Name</th>
	        <th>Payment Ref</th>
	    </tr>

	    <?php foreach ($memberList as $member): ?>

	    	<tr>
	    		<td> <?php echo $this->Html->link($member['bestName'], array('controller' => 'members', 'action' => 'view', $member['id'])); ?> </td>
	    		<td> <?php echo $member['paymentRef']; ?> </td>
	    	</tr>

	    <?php endforeach; ?>

	</table>

<?php endif; ?>