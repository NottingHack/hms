<!-- File: /app/View/Members/index.ctp -->

<?php
    $this->Html->addCrumb('Members', '/members');
?>

<div class="search">
<?php
    echo $this->form->create("Member", array('url' => array('controller' => 'members', 'action' => 'search'), 'type' => 'GET'));
    echo $this->form->input("query", array('label' => ''));
    echo $this->form->end("Search"); 
?>
</div>

<h3>Summary</h3>

<table>
    <tr>
        <th>
            Member Status
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
                <?php echo $data['count'] ?>
            </td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td>
            <?php echo $this->Html->link('Total', array('controller' => 'members', 'action' => 'listMembers')); ?>
        </td>
        <td>
            <?php echo $memberTotalCount; ?>
        </td>
    </tr>
</table>

