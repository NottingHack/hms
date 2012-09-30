<?php

App::uses('AppHelper', 'View/Helper');

# AT [16/09/2012] NavHelper exists to render a list of links in a nice way
class NavHelper extends AppHelper {

	public $helpers = array('Html');

	public function output($links)
	{
		if(count($links) > 0)
		{
			echo '<div class="actions">';
			echo '<ul class="nav">';

			foreach ($links as $link) {
				echo '<li>';

				# Build the options array
				$options = '';
				if(isset($link['url']))
				{
					$options = $link['url'];
				}
				else
				{
					$options = array( 'controller' => $link['controller'], 'action' => $link['action'] );
					$options = array_merge($options, $link['params']);
				}
				
				$htmlAttrs = array();
				if(isset($link['class']) && $link['class'] != '')
				{
					$htmlAttrs['class'] = $link['class'];
				}
				echo $this->Html->link($link['text'], $options, $htmlAttrs);

				echo '</li>';
			}

			echo '</ul>';
			echo '</div>';
		}
	}
}

?>
