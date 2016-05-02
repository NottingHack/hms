<p>
    Hello Membership Team,
</p>
<p>
    I have audited the members with following results.
</p>
<h2>New Members: <?php echo count($approveMembers);?></h2>
<table border="1" style="width:100%">
    <tr>
        <th>Name</th>
        <th>Email</th>
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
    </tr>
<?php endforeach; ?>
</table>

<h2>Warned Members: <?php echo count($warnedMembers);?></h2>
<table border="1" style="width:100%">
    <tr>
        <th>Name</th>
        <th>Email</th>
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
    </tr>
<?php endforeach; ?>
</table>

<h2>Revoked Members: <?php echo count($revokedMembers);?></h2>
<table border="1" style="width:100%">
    <tr>
        <th>Name</th>
        <th>Email</th>
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
    </tr>
<?php endforeach; ?>
</table>

<h2>Reinstated Members: <?php echo count($reinstatedMembers);?></h2>
<table border="1" style="width:100%">
    <tr>
        <th>Name</th>
        <th>Email</th>
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
    </tr>
<?php endforeach; ?>
</table>

<p>
    Thank you,<br/>
    HMS
</p>
