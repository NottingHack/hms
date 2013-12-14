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
 * Helper to display a list of mailing lists.
 */
class MailinglistHelper extends AppHelper {

/**
 * Helpers this helper requires.
 * @var array
 */
	public $helpers = array('Form', 'Html');

/**
 * Given a list of mailing list data, output a list of selection boxes showing subscription status.
 * @param  array $mailingListDetails Array of mailing list data.
 * @return string HTML required to render the selection boxes.
 */
	public function output($mailingListDetails) {
		$mailingListOptions = array();
		$selectedMailingLists = array();

		foreach ($mailingListDetails['data'] as $mailingList) {
			$listId = $mailingList['id'];
			$mailingListOptions[$listId] = $mailingList['name'];
			if (Hash::get($mailingList, 'subscribed')) {
				array_push($selectedMailingLists, $listId);
			}
		}

		return $this->Form->input('MailingLists.MailingLists',
			array(
				'label' => __(' ', true),
				'type' => 'select',
				'multiple' => 'checkbox',
				'options' => $mailingListOptions,
				'selected' => $selectedMailingLists,
			)
		);
	}

/**
 * Given a list of mailing list data, output an englishised list of the names
 * of the mailing lists that are subscribed to.
 * @param  array $mailingListDetails Array of mailing list data.
 * @return string HTML to render the lists.
 */
	public function outputList($mailingListDetails) {
		$output = array();
		foreach ($mailingListDetails['data'] as $mailingList) {
			$listId = $mailingList['id'];
			if (Hash::get($mailingList, 'subscribed')) {
				array_push($output, $this->Html->link($mailingList['name'], array('controller' => 'Mailinglists', 'action' => 'view', $listId)));
			}
		}

		if (count($output) > 0) {
			return String::toList($output);
		}

		return 'None';
	}
}
