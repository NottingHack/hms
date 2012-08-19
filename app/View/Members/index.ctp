<!-- File: /app/View/Members/index.ctp -->

<h1>Members</h1>

<h3>Summary</h3>

<table>
    <?php foreach ($memberStatusCount as $title => $data): ?>
        <tr>
            <td>
                <?php echo $this->Html->link($title, array('controller' => 'members', 'action' => 'list_members_with_status', $data['id'])); ?>
            </td>
            <td>
                <?php echo $data['count'] ?>
            </td>
        </tr>
    <? endforeach; ?>
</table>

<ul class="nav">
    <li>
        <?php echo $this->Html->link("Add Member", array('controller' => 'members', 'action' => 'add')); ?>
    </li>
</ul>