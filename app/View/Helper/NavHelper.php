<?php

App::uses('AppHelper', 'View/Helper');

class NavHelper extends AppHelper {

	public $helpers = array('Html');

	var $actions = array();

	public function add($text, $action)
	{
		$this->actions[$text] = $action;
	}

	public function output()
	{
		if(count($this->actions) > 0)
		{
			echo '<div class="actions">';
			echo '<ul class="nav">';

			foreach ($this->actions as $text => $action) {
				echo '<li>';

				echo $this->Html->link($text, $action);

				echo '</li>';
			}

			echo '</ul>';
			echo '</div>';
		}
	}
}

?>
