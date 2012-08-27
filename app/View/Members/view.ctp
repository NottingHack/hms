<!-- File: /app/View/Member/view.ctp -->

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
                echo $member['Group'][$i]['grp_description'];
                if($i < $numGroups - 1)
                {
                    echo ', ';
                }
            }
        }
       ?>
	</dd>
</dl>

<ul class="nav">
    <li>
        <?php echo $this->Html->link("Edit", array('controller' => 'members', 'action' => 'edit', $member['Member']['member_id'])); ?>
    </li>
</ul>
