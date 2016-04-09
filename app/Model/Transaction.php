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
class Transaction extends AppModel {

	const TYPE_VEND = "VEND";			// Transaction relates to either vending machine purchace, or a payment received by note acceptor
	const TYPE_MANUAL = "MANUAL";	// Transaction is a manually entered (via web interface) record of a payment or purchase
    const TYPE_TOOL = "TOOL";
    const TYPE_MEMBERBOX = "MEMBERBOX";
    const STATE_COMPLETE = "COMPLETE";
    const STATE_PENDING = "PENDING";
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
 * @param bool $paginate If true, return a query to retrieve a page of the data, otherwise return the data.
 * @param array $conditions An array of conditions to decide which member records to access.
 * @return array A list of transactions or query to reports a list of transactions
 */
	public function getTransactionList($paginate, $conditions = array()) {
		$findOptions = array(
			'conditions' => $conditions,
			'order' => 'Transaction.transaction_datetime DESC'
		);

		if ($paginate) {
			return $findOptions;
		}

		$info = $this->find( 'all', $findOptions );

		return $info;
	}

/**
 * Record a new transaction for a member
 *
 * @param int $memberId
 * @param int $amount in pence, negative is charge, postive is credit
 * @param string $type
 * @param string $description
 * @param int $recordedBy person who recorded the transaction, can be null if same as $memberID
 * @return bool
 */
    public function recordTransaction($memberId, $amount, $type, $description, $recordedBy = null) {
        // check member is not null
        if ($memberId == null) {
            return false;
        }
        
        // check amount is number
        if (!is_numeric($amount)) {
            return false;
        }

        // if recordedBy
        if ($memberId == $recordedBy) {
            $recordedBy = null;
        }
        
        // grab member and double check credit
        $member = $this->Member->getMemberSummaryForMember($memberId, false);
        if (($member['Member']['balance'] + $amount) < (-1 *$member['Member']['credit_limit'])) {
            return false;
        }
        
        // prep records
        $transaction = array(
                             'Transaction' => array(
                                                    'member_id' => $memberId,
                                                    'amount' => $amount,
                                                    'transaction_type' => $type,
                                                    'transaction_status' => Transaction::STATE_COMPLETE,
                                                    'transaction_desc' => $description,
                                                    'recorded_by' => $recordedBy,
                                                    )
        
                            );
        
        $dataSource = $this->getDataSource();
        $dataSource->begin();
        
        // create new transaction
        $this->create();
        
        if(!$this->save($transaction)) {
            $dataSource->rollback();
            return false;
        }
        
        // update memberBalance
        if (!$this->Member->updateBalanceForMember($member, $amount)) {
            $dataSource->rollback();
            return false;
        }
        
        $dataSource->commit();
        return true;
        
    }
}
