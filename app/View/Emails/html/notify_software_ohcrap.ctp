<p>
    Hello Software Team,
</p>
<p>
    I was unable to audit the following members automatically. <br/>
    These are current members that have no bank transactions against their name.
</p>
<p>
    <table border="1" style="width:100%">
        <tr>
            <th>Member Id</th>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
        </tr>
<?php foreach ($ohCrapMembers as $member): ?>
        <tr>
            <td><?php echo $member['id']; ?></td>
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
            <td><?php echo $member['status']['name']; ?></td>
        </tr>
<?php endforeach; ?>
    </table>
</p>
<p>
    Please help me,<br/>
    HMS
</p>
