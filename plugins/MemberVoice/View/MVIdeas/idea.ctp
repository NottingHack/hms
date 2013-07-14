<?php
/* Breadcrumbs */
$this->Html->addCrumb('MemberVoice', '/membervoice/ideas');
$this->Html->addCrumb($idea['Idea']['id'] , '/membervoice/ideas/view' . $idea['Idea']['id']);

/* Load our CSS */
$this->Html->css('MemberVoice.mvideas', null, array('inline' => false));

/* Enclose all HTML to isolate css */
?>

<div class="memberVoice">

<div class="mvIdeaDetail">
	<h2><?php echo($idea['Idea']['idea']); ?></h2>
	<p><?php echo($idea['Idea']['description']); ?></p>
	<div class="mvMeta">
		<p><?php
			echo(count($idea['Comment']) . " Comment");
			echo(count($idea['Comment']) == 1 ? "" : "s");
		?></p>
	</div>
</div>

<div class="mvIdeaVotes">
	<div class="mvVoteCount">
		<strong><?php echo($idea['Idea']['votes']); ?></strong>
		<span>vote<?php echo($idea['Idea']['votes'] == 1 ? "" : "s"); ?></span>
	</div>

	<!-- Voting code - need some member detail from the controller -->

</div>


<?php
/* End isolation block */
?>
</div>