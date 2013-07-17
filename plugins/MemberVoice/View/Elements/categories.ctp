<ul class="mvCategories">
	<li>
		<a href="<?php
			echo($this->Html->url(array('plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'index')));
		?>"
		<?php
			if (!isset($thisCategory)) {
				echo(' class="mvSelected"');
			}
			?>>
		All 
		</a>
	</li>
	<?php
	foreach ($categories as $category) :
	?>
	<li>
		<a href="<?php
			echo($this->Html->url(array('plugin' => 'membervoice', 'controller' => 'ideas', 'action' => 'index', $category['Category']['id'])));
		?>"
		<?php
			if (isset($thisCategory) and $category['Category']['id'] == $thisCategory['Category']['id']) {
				echo(' class="mvSelected"');
			}
			?>>
			<?php echo($category['Category']['category']) ?> (<?php echo(count($category['Idea'])); ?>)
		</a>
	</li>
	<?php
	endforeach;
	?>
</ul>