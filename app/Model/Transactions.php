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
App::uses('Status', 'Model');

/**
 * Model for all transaction data
 *
 *
 * @package       app.Model
 */
class Transactions extends AppModel {

	const TYPE_VEND = "VEND";			// Transaction relates to either vending machine purchace, or a payment received by note acceptor
	const TYPE_MANUAL = "MANUAL";	// Transaction is a manually entered (via web interface) record of a payment or purchase

/**
 * Specify the table to use.
 * @var string
 */
	public $useTable = 'transactions';

/**
 * Specify the primary key.
 * @var string
 */
	public $primaryKey = 'transaction_id';

/**
 * Specify 'belongs to' associations.
 * @var array
 */
	public $belongsTo = array(
			'Member' => array(
			'className' => 'Member',
			'foreignKey' => 'member_id',
			'type' => 'inner'
			)
		);

/**
 * Get a list of transations for a member
 * 
 * @param $paginate If true, return a query to retrieve a page of the data, otherwise return the data.
 * @param array $conditions An array of conditions to decide which member records to access.
 * @return array A list of transactions or query to reports a list of transactions
 */
	public function getTransactionList($paginate, $conditions = array()) {
		$findOptions = array(
			'conditions' => $conditions,
			'order' => 'Transactions.transaction_datetime DESC'
		);

		if ($paginate) {
			return $findOptions;
		}

		$info = $this->find( 'all', $findOptions );

		return $info;
	}

}
