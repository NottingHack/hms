<!-- File: /app/View/Group/add.ctp -->


<?php
    $this->Html->addCrumb('Group', '/groups');
	$this->Html->addCrumb('Add Group', '/groups/add');
?>

<?php
	echo $this->Form->create('Group');
	echo $this->Form->input('grp_description', array( 'label' => 'Description' ) );

	echo '<fieldset>';
	echo '<legend>Permissions</legend>';

	echo $this->Form->input('Permission',array(
            'label' => __(' ',true),
            'type' => 'select',
            'multiple' => 'checkbox',
            'options' => $permissions,
            'selected' => $this->Html->value('Permission.Permission'),
        )); 

	echo '</fieldset>';
	echo $this->Form->end('Add Group');
?>