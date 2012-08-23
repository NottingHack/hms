<!-- File: /app/View/Members/list_members.ctp -->

<h1>Members</h1>
<table>
    <tr>
        <th>Id</th>
        <th>Member No.</th>
        <th>Name</th>
        <th>Email</th>
        <th>Join Date</th>
        <th>Handle</th>
        <th>Unlock Text</th>
        <th>Groups</th>
        <th>Actions</th>
    </tr>
    <?php foreach ($members as $member): ?>
    <tr>
        <td><?php echo $member['Member']['member_id']; ?></td>
        <td><?php echo $member['Member']['member_number']; ?></td>
        <td>
            <?php echo $this->Html->link($member['Member']['name'], array('controller' => 'members', 'action' => 'view', $member['Member']['member_id'])); ?>
        </td>
        <td><?php echo $member['Member']['email']; ?></td>
        <td><?php echo $member['Member']['join_date']; ?></td>
        <td><?php echo $member['Member']['handle']; ?></td>
        <td><?php echo $member['Member']['unlock_text']; ?></td>
        <td>
            <?php
                $numGroups = count($member['Group']);
                if($numGroups === 0)
                {
                    echo 'None';
                }
                else
                {
                    for($i = 0; $i < $numGroups; $i++) {
                        echo $member['Group'][$i]['grp_description'];
                        if($i < $numGroups - 1)
                        {
                            echo ', ';
                        }
                    }
                }
            ?>
        </td>
        <td>
            <?php 
                switch ($member['Member']['member_status']) {
                    case 1: # Prospective member
                        echo $this->Html->link("Approve member", array('controller' => 'members', 'action' => 'set_member_status', $member['Member']['member_id'], 2));
                        break;

                    case 2: # Current member
                        echo $this->Html->link("Revoke membership", array('controller' => 'members', 'action' => 'set_member_status', $member['Member']['member_id'], 3));
                        break;

                    case 3: # Ex-member
                        echo $this->Html->link("Reinstate membership", array('controller' => 'members', 'action' => 'set_member_status', $member['Member']['member_id'], 2));
                        break;
                }
            ?>
        </td>
    </tr>
    <?php endforeach; ?>

</table>