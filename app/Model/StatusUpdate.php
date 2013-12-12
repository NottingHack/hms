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
 * Model for all status update data
 */
class StatusUpdate extends AppModel {

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = 'status_updates';

/**
 * Specify the primary key.
 * @var string
 */
	public $primaryKey = 'id';

/**
 * Specify the 'belongs to' associations.
 * @var array
 */
	public $belongsTo = array(
		'Member' => array(
			'className' => 'Member',
			'foreignKey' => 'member_id'
		),
		'MemberAdmin' => array(
			'className' => 'Member',
			'foreignKey' => 'admin_id',
		),
		'OldStatus' => array(
			'className' => 'Status',
			'foreignKey' => 'old_status',
		),
		'NewStatus' => array(
			'className' => 'Status',
			'foreignKey' => 'new_status',
		),
	);

/**
 * Create a new status update record.
 * 
 * @param int $memberId The id of the member who's having their status updated.
 * @param int $adminId The id of the member who is doing the updating.
 * @param int $oldStatus The members previous status.
 * @param int $newStatus The members new status.
 * @return bool True if creation was successful, false otherwise.
 */
	public function createNewRecord($memberId, $adminId, $oldStatus, $newStatus) {
		if (isset($memberId) && isset($adminId) && isset($oldStatus) && isset($newStatus) &&
			is_numeric($memberId) && is_numeric($adminId) && is_numeric($oldStatus) && is_numeric($newStatus)) {
			$this->Create();

			$data = array(
				'StatusUpdate' => array(
					'member_id' => $memberId,
					'admin_id' => $adminId,
					'old_status' => $oldStatus,
					'new_status' => $newStatus,
					'timestamp' => date('Y-m-d H:i:s'),
				)
			);

			return ($this->save($data) != false);
		}

		return false;
	}

/**
 * Get all the data about a status update, formatted so that calling code doesn't need to know about our columns.
 * 
 * @param int $updateId The id of the record to format.
 * @return mixed Array of formatted data if record found, otherwise false.
 */
	public function formatStatusUpdate($updateId) {
		$record = $this->find('first', array('conditions' => array('StatusUpdate.id' => $updateId)));
		if ($record) {
			$adminId = Hash::get($record, 'StatusUpdate.admin_id');
			return array(
				'id' => Hash::get($record, 'StatusUpdate.member_id'),
				'by' => $adminId,
				'by_username' => $this->Member->getUsernameForMember($adminId),
				'from' => Hash::get($record, 'StatusUpdate.old_status'),
				'to' => Hash::get($record, 'StatusUpdate.new_status'),
				'at' => Hash::get($record, 'StatusUpdate.timestamp'),
			);
		}

		return false;
	}
}