<!-- File: /app/View/Member/view.ctp -->

<?php
	$this->Html->addCrumb('Members', '/members');
	$this->Html->addCrumb('View Member', '/members/view');
?>


<h1><?php echo $member['Member']['name'] ?></h1>

<dl>
	<dt>
		Member No.
	</dt>
	<dd>
		<?php 
			echo isset($member['Member']['member_number']) ? $member['Member']['member_number'] : "None"; ?>
	</dd>

	<dt>
		Email
	</dt>
	<dd>
		<?php echo $member['Member']['email']; ?>
	</dd>

	<dt>
		Member Since
	</dt>
	<dd>
		<?php echo $member['Member']['join_date']; ?>
	</dd>

	<dt>
		Handle
	</dt>
	<dd>
		<?php echo $member['Member']['handle']; ?>
	</dd>

	<dt>
		Groups
	</dt>
	<dd>
		<?php

		$numGroups = count($member['Group']);
        if($numGroups === 0)
        {
            echo 'None';
        }
        else
        {
            for($i = 0; $i < $numGroups; $i++) {
                echo $this->Html->link($member['Group'][$i]['grp_description'], array('controller' => 'groups', 'action' => 'view', $member['Group'][$i]['grp_id']));
                if($i < $numGroups - 1)
                {
                    echo ', ';
                }
            }
        }
       ?>
	</dd>
	<dt>
		Status
	</dt>
	<dd>
		<?php echo $this->Html->link($member['Status']['title'], array('controller' => 'members', 'action' => 'list_members_with_status', $member['Status']['status_id'])); ?>
	</dd>
	<dt>
		Pin
	</dt>
	<dd>
		<?php echo $member['Pin']['pin']; ?>
	</dd>
	<dt>
		Current Balance
	</dt>
	<dd>
		<?php echo $member['Member']['balance']; ?>
	</dd>
	<dt>
		Credit Limit
	</dt>
	<dd>
		<?php echo $member['Member']['credit_limit']; ?>
	</dd>
</dl>

<ul class="nav">
    <li>
        <?php echo $this->Html->link("Edit", array('controller' => 'members', 'action' => 'edit', $member['Member']['member_id'])); ?>
    </li>
</ul>
