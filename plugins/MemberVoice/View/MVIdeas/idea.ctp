<?php
/* Breadcrumbs */
$this->Html->addCrumb('MemberVoice', '/membervoice/ideas');
$this->Html->addCrumb($idea['Idea']['id'] , '/membervoice/ideas/view' . $idea['Idea']['id']);

/* Load our CSS */
$this->Html->css('MemberVoice.mvideas', null, array('inline' => false));

/* The JSON URL */
$this->append('script');
echo('<script type="text/javascript">' . "\n");
echo('var mvVoteUrl = "' . $voteurl . '";' . "\n");
echo('</script>' ."\n");
$this->end();

/* Load the Ideas JS */
$this->Html->script('MemberVoice.idea', array('inline' => false));

/* Enclose all HTML to isolate css */
?>

<div class="memberVoice" id="mvIdea<?php echo($idea['Idea']['id']); ?>">

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

	<?php
	$hasVoted = 0;
	foreach ($idea['Vote'] as $vote) {
		if ($vote['user_id'] == $user) {
			$hasVoted = $vote['votes'];
		}
	}
	echo($this->element('vote', array('id' => $idea['Idea']['id'], 'hasVoted' => $hasVoted)));

	?>

</div>


<?php
/* End isolation block */
?>
</div>