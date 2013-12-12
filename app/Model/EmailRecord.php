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
 * Model for all email record data
 */
class EmailRecord extends AppModel {

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = 'hms_emails';

/**
 * Specify the primary ket to use.
 * @var string
 */
	public $primaryKey = 'hms_email_id';

/**
 * Validation rules.
 * @var array
 */
	public $validate = array(
		'member_id' => array(
			'length' => array(
				'rule' => array('maxLength', 11),
				'message' => 'Member id must be no more than 11 characters long',
			),
			'content' => array(
				'rule' => 'numeric',
				'message' => 'Member id must be a number',
			),
		),
		'subject' => array(
			'rule' => 'notEmpty',
			'message' => 'Must have a subject',
		),
	);

/**
 * Get information about all the emails sent.
 * 
 * @return array An array of record data, or null if data could not be found.
 */
	public function getAllEmails() {
		$result = $this->find('all', array( 'order' => 'EmailRecord.timestamp DESC' ));
		return $this->__formatEmailRecords($result);
	}

/**
 * Get information about all the emails sent to a member.
 * 
 * @param int $memberId The id of the member to look for.
 * @return array An array of record data, or null if data could not be found.
 */
	public function getAllEmailsForMember($memberId) {
		if (is_numeric($memberId)) {
			$result = $this->find('all', array( 'order' => 'EmailRecord.timestamp DESC', 'conditions' => array('EmailRecord.member_id' => $memberId) ));
			return $this->__formatEmailRecords($result);
		}

		return null;
	}

/**
 * Get the most recent email record for a member.
 * 
 * @param int $memberId The id of the member to look for.
 * @return array An array of record data, or null if data could not be found.
 */
	public function getMostRecentEmailForMember($memberId) {
		if (is_numeric($memberId)) {
			$result = $this->find('first', array( 'order' => 'EmailRecord.timestamp DESC', 'conditions' => array('EmailRecord.member_id' => $memberId) ));
			if (is_array($result) && count($result) > 0) {
				return $this->__formatEmailRecord($result);
			}
		}

		return null;
	}

/**
 * Given an array of data for multiple records from the database, format it so other classes can use it.
 * 
 * @param array $recordList An array of records data.
 * @return mixed A formatted array of data, or null on error.
 */
	private function __formatEmailRecords($recordList) {
		if (is_array($recordList) && count($recordList) > 0) {
			$formattedList = array();
			foreach ($recordList as $record) {
				array_push($formattedList, $this->__formatEmailRecord($record));
			}
			return $formattedList;
		}
		return null;
	}

/**
 * Given an array of data from the database, format it so other classes can use it.
 * 
 * @param array $data An array of record data.
 * @return array A formatted array of data.
 */
	private function __formatEmailRecord($data) {
		return array(
			'id' => Hash::get($data, 'EmailRecord.hms_email_id'),
			'member_id' => Hash::get($data, 'EmailRecord.member_id'),
			'subject' => Hash::get($data, 'EmailRecord.subject'),
			'timestamp' => Hash::get($data, 'EmailRecord.timestamp'),
		);
	}

/**
 * Create one or more new EmailRecord entry
 * 
 * @param mixed $to Either a single member_id or an array of member id's that the e-mail was sent to.
 * @param string $subject The subject of the e-mail.
 * @return bool True if creation was successful, false otherwise.
 */
	public function createNewRecord($to, $subject) {
		if (!isset($to) || !isset($subject)) {
			return false;
		}

		if (!is_string($subject)) {
			return false;
		}

		if (is_array($to)) {
			// Creating multiple records, wrap it up in a transaction
			$dataSource = $this->getDataSource();
			$dataSource->begin();
			foreach ($to as $id) {
				if (!$this->__createNewRecordImpl($id, $subject)) {
					$dataSource->rollback();
					return false;
				}
			}
			$dataSource->commit();
			return true;
		} elseif (is_numeric($to)) {
			return $this->__createNewRecordImpl($to, $subject);
		}

		return false;
	}

/**
 * Create a new EmailRecord entry
 * 
 * @param int $to The member_id of the member that the e-mail was sent to.
 * @param string $subject The subject of the e-mail.
 * @return bool True if creation was successful, false otherwise.
 */
	private function __createNewRecordImpl($to, $subject) {
		$this->Create();

		$data =
		array( 'EmailRecord' =>
			array(
				'member_id' => $to,
				'subject' => $subject,
				'timestamp' => date('Y-m-d H:i:s'),
			)
		);

		return ($this->save($data) != false);
	}
}