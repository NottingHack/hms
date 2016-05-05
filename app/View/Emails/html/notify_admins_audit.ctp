<p>
    Hello Membership Team,
</p>
<p>
    I have audited the members with following results. <br/>
    Each will have had an automated email. 
</p>
<h2>New Members: <?php echo count($approveMembers);?></h2>
<p>We have just seen a payment from these new members.</p>
<table border="1" style="width:100%">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Pin</th>
        <th>Joint Account</th>
    </tr>
<?php foreach ($approveMembers as $member): ?>
    <tr>
        <td><?php echo $this->Html->link(
                                        sprintf('%s %s', $member['firstname'], $member['surname']),
                                        array(
                                            'controller' => 'members',
                                            'action' => 'view',
                                            $member['id'],
                                            'full_base' => true)
                                        );
            ?></td>
        <td><?php echo $member['email']; ?></td>
        <td><?php echo $member['pin'][0]['pin']; ?></td>
        <td><?php echo $member['joint']? "Yes": "No"; ?></td>
    </tr>
<?php endforeach; ?>
</table>

<h2>Notified Members: <?php echo count($warnedMembers);?></h2>
<p>We have not seen a payment from these members in a while, they may soon have there membership revoked.</p>
<table border="1" style="width:100%">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Joint Account</th>
        <th>Balance</th>
        <th>Last payment date</th>
        <th>Last visit date</th>
    </tr>
<?php foreach ($warnedMembers as $member): ?>
    <tr>
        <td><?php echo $this->Html->link(
                                        sprintf('%s %s', $member['firstname'], $member['surname']),
                                        array(
                                            'controller' => 'members',
                                            'action' => 'view',
                                            $member['id'],
                                            'full_base' => true)
                                        );
            ?></td>
        <td><?php echo $member['email']; ?></td>
        <td><?php echo $member['joint']? "Yes": "No"; ?></td>
        <td><?php echo $this->Currency->output($member['balance']); ?></td>
        <td><?php echo $latestTransactionDateForAccounts[$member['accountId']]; ?></td>
        <td><?php echo $warnedLastAccess[$member['id']]; ?></td>
    </tr>
<?php endforeach; ?>
</table>
<?php if ($paymentNotificationsClearCount != 0): ?>
<h2>
<?php echo $paymentNotificationsClearCount; ?> member<?php if ($paymentNotificationsClearCount != 1) { echo "s"; }?> that had been Notified <?php echo ($paymentNotificationsClearCount == 1)? "has" : "have"; ?> now made a payment in time to not be revoked.</h2>
<?php endif; ?>
<h2>Revoked Members: <?php echo count($revokedMembers);?></h2>
<p>These members last payment was too long ago, so thier membership has been revoked</p>
<table border="1" style="width:100%">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Joint Account</th>
        <th>Balance</th>
        <th>Last payment date</th>
        <th>Last visit date</th>
    </tr>
<?php foreach ($revokedMembers as $member): ?>
    <tr>
        <td><?php echo $this->Html->link(
                                        sprintf('%s %s', $member['firstname'], $member['surname']),
                                        array(
                                            'controller' => 'members',
                                            'action' => 'view',
                                            $member['id'],
                                            'full_base' => true)
                                        );
            ?></td>
        <td><?php echo $member['email']; ?></td>
        <td><?php echo $member['joint']? "Yes": "No"; ?></td>
        <td><?php echo $this->Currency->output($member['balance']); ?></td>
        <td><?php echo $latestTransactionDateForAccounts[$member['accountId']]; ?></td>
        <td><?php echo $revokedLastAccess[$member['id']]; ?></td>
    </tr>
<?php endforeach; ?>
</table>

<h2>Reinstated Members: <?php echo count($reinstatedMembers);?></h2>
<p>These Ex members have started paying again, so there membership has been reinstated</p>
<table border="1" style="width:100%">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Joint Account</th>
        <th>Balance</th>
        <th>Date made Ex</th>
        <th>Last visit date</th>
    </tr>
<?php foreach ($reinstatedMembers as $member): ?>
    <tr>
        <td><?php echo $this->Html->link(
                                        sprintf('%s %s', $member['firstname'], $member['surname']),
                                        array(
                                            'controller' => 'members',
                                            'action' => 'view',
                                            $member['id'],
                                            'full_base' => true)
                                        );
            ?></td>
        <td><?php echo $member['email']; ?></td>
        <td><?php echo $member['joint']? "Yes": "No"; ?></td>
        <td><?php echo $this->Currency->output($member['balance']); ?></td>
        <td><?php echo $member['lastStatusUpdate']['at']; ?></td>
        <td><?php echo $reinstatedLastAccess[$member['id']]; ?></td>
    </tr>
<?php endforeach; ?>
</table>

<p>
    Thank you,<br/>
    HMS
</p>
