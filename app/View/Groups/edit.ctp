<!-- File: /app/View/Group/edit.ctp -->

<h1>Edit Group</h1>
<?php
	echo $this->Form->create('Group');
	echo $this->Form->hidden('id' );
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
	echo $this->Form->end('Update Group');
?>