<!-- File: /app/View/RfidTags/view.ctp -->

<?php
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb(isset($member['username']) ? $member['username'] : $member['email'], '/members/view/' . $member['id']);
	$this->Html->addCrumb('Registerd Cards', '/rfidtags/view/');
?>

<table>
	<tr>
		<th>Card Serial Number</th>
		<th>Last Used</th>
		<th>Card Name</th>
		<th>Card State</th>
		<th />
	</tr>
	<?php
		foreach ($tagsList as $tag) {
			echo '<tr>';
			echo '<td>' . $tag['tagSerial'] . '</td>';
			echo '<td>' . $tag['lastSeen'] . '</td>';
			echo '<td>' . $tag['tagName'] . '</td>';
			echo '<td>' . $tag['stateName'] . '</td>';
			echo '<td>' . $this->Html->Link('Edit Card', array('controller' => 'rfidtags', 'action' => 'edit', $tag['tagSerial']), array('escape' => false)) . '</td>';
//			echo 'some text here</form> some text after';
			echo '</tr>';
		}
	?>

	
</tr>
</table>

<div class="paginate">
	<?php echo $this->Paginator->numbers(); ?>
</div>


