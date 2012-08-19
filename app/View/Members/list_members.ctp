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
    </tr>
    <?php #print_r($members); ?>
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
    </tr>
    <?php endforeach; ?>

</table>