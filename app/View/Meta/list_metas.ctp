<!-- File: /app/View/Meta/list_meta.ctp -->

<?php
$this->Html->addCrumb('Meta', '/meta');
?>

<table>
<tr>
<th>Name</th>
<th>Value</th>
<th>Actions</th>
</tr>
<?php
foreach ($metasList as $meta) {
echo '<tr>';
echo '<td>' . $meta['name'] . '</td>';
echo '<td>';
if(strlen($meta['value']) > $shortDescriptionLength) {
echo substr($meta['value'], 0, $shortDescriptionLength) . '...';
} else {
echo $meta['value'];
}
echo '</td>';
echo '<td>';
echo $this->Html->link("Edit", array('controller' => 'meta', 'action' => 'edit', $meta['name']));
echo '</td>';
echo '</tr>';
}
?>
</tr>
</table>

<div class="paginate">
<?php echo $this->Paginator->numbers(); ?>
</div>


