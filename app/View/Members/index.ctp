<!-- File: /app/View/Members/index.ctp -->

<h1>Members</h1>

<h3>Summary</h3>

<table>
    <?php foreach ($memberStatusCount as $title => $count): ?>
        <tr>
            <td>
                <?php echo $title ?>
            </td>
            <td>
                <?php echo $count ?>
            </td>
        </tr>
    <? endforeach; ?>
</table>
