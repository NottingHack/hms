<?php
/* Breadcrumbs */
$this->Html->addCrumb('MemberVoice', $this->Html->url(array('plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'index', 'base' => false)));
if (isset($category)) {
	$this->Html->addCrumb($category['Category']['category'], $this->Html->url(array('plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'index', 'base' => false, $category['Category']['id'])));
}

/* Load our CSS */
$this->Html->css("MemberVoice.mvideas", null, array('inline' => false));

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

<div class="memberVoice">

<?php
/* Need some way to change category */

/* Add idea form will go here, but not yet! */

/* Show the ideas, if we've got any! */
if (count($ideas) > 0):
	?>
<ul class="mvIdeas">
<?php
	foreach ($ideas as $idea):
?>

<li class="mvIdea" id="mvIdea<?php echo($idea['Idea']['id']); ?>">

<div class="mvIdeaDetail">
	<h2><?php echo($this->Html->link($idea['Idea']['idea'], array( 'plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'idea', $idea['Idea']['id'] ))); ?></h2>
	<p><?php echo($idea['Idea']['description']); ?></p>
	<div class="mvMeta">
		<p><a href="<?php
			echo($this->Html->url(array('plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'idea', $idea['Idea']['id'])));
		?>#comments"><?php
			echo(count($idea['Comment']) . " Comment");
			echo(count($idea['Comment']) == 1 ? "" : "s");
		?></a></p>
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


</li>

<?php
	endforeach;
	?>
</ul>
<?php
/* Pagination links */
echo '<div class="paginate">';
echo $this->Paginator->numbers();
echo '</div>';

endif;

$args = array(
			'categories' => $categories
			);
if (isset($category)) {
	$args['thisCategory'] = $category;
}
echo($this->element('categories', $args));

/* End isolation block */
?>
</div>