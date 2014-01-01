<!-- File: /app/View/MailingLists/view.ctp -->

<?php
	$this->Html->addCrumb('Mailing Lists', '/mailinglists');
	$this->Html->addCrumb($mailingList['name'], '/mailinglists/view/' . $mailingList['id']);

	$membersSubscribedCount = count($membersSubscribed);
	$membersNotSubscribedCount = count($membersNotSubscribed);
?>

<h3>Stats</h3>
<dl>
	<dt>
		Created On:
	</dt>
	<dd>
		<?php echo date('l, dS F, Y', strtotime($mailingList['date_created'])); ?>
	</dd>

	<dt>
		No. Subscribers:
	</dt>
	<dd>
		<?php echo $mailingList['stats']['member_count']; ?>
	</dd>

	<dt>
		Total Un-Subscribers:
	</dt>
	<dd>
		<?php echo $mailingList['stats']['unsubscribe_count']; ?>
	</dd>

	<dt>
		No. New Subscribers Since Last Mail:
	</dt>
	<dd>
		<?php echo $mailingList['stats']['member_count_since_send']; ?>
	</dd>

	<dt>
		No. Un-Subscribers Since Last Mail:
	</dt>
	<dd>
		<?php echo $mailingList['stats']['unsubscribe_count_since_send']; ?>
	</dd>

	<dt>
		No. Mails Sent:
	</dt>
	<dd>
		<?php echo $mailingList['stats']['campaign_count']; ?>
	</dd>
</dl>

</br>
</br>

<?php if($membersSubscribedCount > 0): ?>
	<h3>Subscribed Members (<?php echo $membersSubscribedCount; ?>)</h3>
	<ul>
	<?php foreach ($membersSubscribed as $memberInfo): ?>
		<li>
			<?php echo $this->Html->link($memberInfo['bestName'], array('controller' => 'members', 'action' => 'view', $memberInfo['id'])); ?>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>

</br>
</br>

<?php if($membersSubscribedCount > 0): ?>
	<h3>Non-Subscribed Members (<?php echo $membersNotSubscribedCount; ?>)</h3>
	<ul>
	<?php foreach ($membersNotSubscribed as $memberInfo): ?>
		<li>
			<?php echo $this->Html->link($memberInfo['bestName'], array('controller' => 'members', 'action' => 'view', $memberInfo['id'])); ?>
		</li>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>

