<?php
/**
 * 
 * PHP 5
 *
 * Copyright (C) HMS Team
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     HMS Team
 * @package       app.View.Helper
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppHelper', 'View/Helper');

/**
 * Helper to output a list of links in a div/ul.
 */
class NavHelper extends AppHelper {

/**
 * List of helpers this helper uses.
 * @var array
 */
	public $helpers = array('Html');

/**
 * Given an array of link data, print a list of HTML links.
 * @param  array $links Array of link data.
 */
	public function output($links) {
		if (count($links) > 0) {
			echo '<div class="actions">';
			echo '<ul class="nav">';

			foreach ($links as $link) {
				echo '<li>';

				# Build the options array
				$options = '';
				if (isset($link['url'])) {
					$options = $link['url'];
				} else {
					$options = array( 'controller' => $link['controller'], 'action' => $link['action'] );
					$options = array_merge($options, $link['params']);
				}

				$htmlAttrs = array();
				if (isset($link['class']) && $link['class'] != '') {
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