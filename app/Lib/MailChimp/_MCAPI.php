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
 * @package       app.Lib.MailChimp
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('ConnectionManager', 'Model');

/**
 * Dummy class that pretends to access the MailChimp API
 * but actually accesses a local table.
 */
class MCAPI {

/**
 * Which database config to use.
 * @var string
 */
	private $__useDbConfig = 'default';

/**
 * The last error message.
 * @var string
 */
	private $__errorMessage = '';

/**
 * The last error code.
 * @var integer
 */
	private $__errorCode = 0;

/**
 * The datasource to query.
 * @var DataSource
 */
	private $__dbSource = null;

/**
 * Constructor
 * 
 * @param string $apiKey Not used.
 * @param bool $secure Not used.
 */
	public function __construct($apiKey, $secure = false) {
		$this->__initDbSource();
	}

/**
 * Set which database config we should use.
 * 
 * @param string $configName The name of the config to use.
 */
	public function setConfig($configName) {
		$this->__useDbConfig = $configName;
		$this->__initDbSource();
	}

/**
 * Get data about one or more lists.
 * 
 * @param $filters Only return lists that match these filters.
 * @param int $start Not used.
 * @param int $limit Not used.
 */
	public function lists($filters = array(), $start = 0, $limit = 25) {
		$query = "SELECT * FROM `mailinglists`";
		if (isset($filters['list_id'])) {
			$query .= " WHERE `id` = '{$filters['list_id']}'";
		}
		$listInfo = $this->__runQuery($query);

		// Have to 'massage' the data slightly
		$statsKeys = array(
			'member_count',
			'unsubscribe_count',
			'cleaned_count',
			'member_count_since_send',
			'unsubscribe_count_since_send',
			'cleaned_count_since_send',
			'campaign_count',
			'grouping_count',
			'group_count',
			'merge_var_count',
			'avg_sub_rate',
			'avg_unsub_rate',
			'target_sub_rate',
			'open_rate',
			'click_rate',
		);

		for ($i = 0; $i < $listInfo['total']; $i++) {
			$statsArray = array();
			foreach ($listInfo['data'][$i] as $key => $value) {
				if (in_array($key, $statsKeys)) {
					// This data belongs in the stat block
					$statsArray[$key] = $value;
					unset($listInfo['data'][$i][$key]);
				}
			}

			$listInfo['data'][$i]['stats'] = $statsArray;
			$listInfo['data'][$i]['modules'] = array();
		}

		return $listInfo;
	}

/**
 * Subscribe an e-mail address to a list.
 * 
 * @param string $id The id of the list to subscribe to.
 * @param string $emailAddress The e-mail address to subscribe.
 * @param string $mergeVars Not used.
 * @param string $emailType Not used.
 * @param bool $doubleOption Not used.
 * @param bool $updateExisting Not used.
 * @param bool $replaceInterests Not used.
 * @param bool $sendWelcome Not used.
 */
	public function listSubscribe($id, $emailAddress, $mergeVars = null, $emailType = 'html', $doubleOption = true, $updateExisting = false, $replaceInterests = true, $sendWelcome = false) {
		$timestamp = date('Y-m-d H:i:s');
		$query = "INSERT INTO `mailinglist_subscriptions` (`mailinglist_id`, `email`, `timestamp`) VALUES ('$id', '$emailAddress', '$timestamp')";
		$this->__runQuery($query);
		return true;
	}

/**
 * Unsubscribe an e-mail address from a list.
 * 
 * @param string $id The id of the list to unsubscrbe from.
 * @param string $emailAddress The e-mail address to unsubscribe.
 * @param bool $deleteMember Not used.
 * @param bool $sendGoodbye Not used.
 * @param bool $sendNotify Not used.
 */
	public function listUnsubscribe($id, $emailAddress, $deleteMember = false, $sendGoodbye = true, $sendNotify = true) {
		$query = "DELETE FROM `mailinglist_subscriptions` WHERE `mailinglist_id` = '$id' AND `email` = '$emailAddress'";
		$this->__runQuery($query);
		return true;
	}

/**
 * List details about e-mail addresses subscribed to a list.
 * 
 * @param string $id The list to get subscribers from.
 * @param string $status Not used.
 * @param string $since Not used.
 * @param int $start Not used.
 * @param int $limit Not used.
 */
	public function listMembers($id, $status = 'subscribed', $since = null, $start = 0, $limit = 100) {
		$query = "SELECT `email`, `timestamp` FROM `mailinglist_subscriptions` WHERE `mailinglist_id` = '$id'";
		return $this->__runQuery($query);
	}

/**
 * Create the DatabaseSource object.
 */
	private function __initDbSource() {
		$this->__dbSource = ConnectionManager::getDataSource($this->__useDbConfig);
	}

/**
 * Run an SQL query, return the results in a manner consistent with the real API.
 * 
 * @param string $query The query to run.
 */
	private function __runQuery($query) {
		$result = $this->__dbSource->execute($query);

		$data = array();
		while (($row = $result->fetch(PDO::FETCH_ASSOC))) {
			array_push($data, $row);
		}

		return array(
			'total' => count($data),
			'data' => $data,
		);
	}
}