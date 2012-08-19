<!-- File: /app/View/Members/list.ctp -->

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
    </tr>
    <?php endforeach; ?>

</table>