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
 * @package       app.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppModel', 'Model');

/**
 * Model for all member status data
 */
class Status extends AppModel {

/**
 * The id of the prospective member status.
 */
	const PROSPECTIVE_MEMBER = 1;

/**
 * The id of the pre-member (stage 1) status.
 */
	const PRE_MEMBER_1 = 2;

/**
 * The id of the pre-member (stage 2) status.
 */
	const PRE_MEMBER_2 = 3;

/**
 * The id of the pre-member (stage 3) status.
 */
	const PRE_MEMBER_3 = 4;

/**
 * The id of the current member status.
 */
	const CURRENT_MEMBER = 5;

/**
 * The id of the ex member status.
 */
	const EX_MEMBER = 6;

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = "status";

/**
 * Specify the primary key.
 * @var string
 */
	public $primaryKey = 'status_id';

/**
 * Specify the 'has' associations.
 * @var array
 */
	public $hasMany = array(
		'Member' => array(
			'foreignKey' => 'member_status',
		),
	);

/**
 * Get a summary of the status records for all statuses.
 * 
 * @return array A summary of the data of all statuses.
 * @link Status::__getStatusSummary()
 */
	public function getStatusSummaryAll() {
		return $this->__getStatusSummary();
	}

/**
 * Get a summary of the status records for a single status.
 * 
 * @param int $id The id of the status to look at
 * @return mixed A summary of the data for a single status, or false if none can be found.
 * @link Status::__getStatusSummary()
 */
	public function getStatusSummaryForId($id) {
		$info = $this->__getStatusSummary( array('Status.status_id' => $id) );

		if (count($info) > 0) {
			return $info[0];
		}
		return $info;
	}

/**
 * Get a summary of the status records for all statuses that match the conditions.
 *
 * @param array $conditions Only return a summary for statuses that match these conditions.
 * @return array A summary (id, name, description and member count) of the data of all statuses that match the conditions.
 */
	private function __getStatusSummary($conditions = array()) {
		$info = $this->find( 'all', array('conditions' => $conditions) );

		return $this->__formatStatusInfo($info);
	}

/**
 * Format status information into a nicer arrangement.
 * 
 * @param $info The info to format, usually retrieved from Status::__getStatusSummary.
 * @return array An array of status information, formatted so that nothing needs to know database rows.
 * @link Status::__getStatusSummary
 */
	private function __formatStatusInfo($info) {
		/*
			Data should be presented to the view in an array like so:
				[n] =>
					[id] => status id
					[name] => status name
					[description] => status description
					[count] => number of members with this status
	 */

		$formattedInfo = array();
		foreach ($info as $status) {
			$id = Hash::get($status, 'Status.status_id');
			$name = Hash::get($status, 'Status.title');
			$count = count( Hash::extract($status, 'Member') );

			array_push($formattedInfo,
				array(
					'id' => $id,
					'name' => $name,
					'count' => $count,
				)
			);
		}

		return $formattedInfo;
	}
}