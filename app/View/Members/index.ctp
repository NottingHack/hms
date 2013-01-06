<!-- File: /app/View/Members/index.ctp -->

<?php
    $this->Html->addCrumb('Members', '/members');
?>

<div class="search">
<?php
    echo $this->form->create("Member", array('action' => 'search', 'type' => 'GET'));
    echo $this->form->input("query", array('label' => ''));
    echo $this->form->end("Search"); 
?>
</div>

<h3>Summary</h3>

<table>
    <tr>
        <th>
            Member Type
        </th>
        <th>
            Description
        </th>
        <th>
            No. Members with this status
        </th>
    </tr>
        <?php
            foreach ($memberStatusInfo as $data):
        ?>
        <tr>
            <td>
                <?php echo $this->Html->link($data['name'], array('controller' => 'members', 'action' => 'listMembersWithStatus', $data['id'])); ?>
            </td>
            <td>
                <?php echo $data['description']; ?>
            </td>
            <td>
                <?php echo $data['count'] ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td>
            <?php echo $this->Html->link('All', array('controller' => 'members', 'action' => 'listMembers')); ?>
        </td>
        <td>
            All Members
        </td>
        <td>
            <?php echo $memberTotalCount; ?>
        </td>
    </tr>
</table>

