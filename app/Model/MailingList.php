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
App::import('Lib/MailChimp', 'MCAPI');
App::uses('PhpReader', 'Configure');

/**
 * Model for all mailing list data
 */
class MailingList extends AppModel {

/**
 * We don't use a table, we use the MCAPI wrapper to get data instead.
 * @var boolean
 */
	public $useTable = false;

/**
 * The api key.
 * @var string
 */
	private $__apiKey = '';

/**
 * The api object.
 * @var MCAPI
 */
	private $__api = null;

/**
 * Constructor
 *
 * @param mixed $id The id to start the model on.
 * @param string $table The table to use for this model.
 * @param string $ds The connection name this model is connected to.
 */
	public function __construct($id = false, $table = null, $ds = null) {
		parent::__construct($id, $table, $ds);

		Configure::config('default', new PhpReader());
		Configure::load('mailchimp', 'default');

		// Set-up mailchimp
		$this->apikey = Configure::read('mailchimp___apiKey');
		$this->__api = new MCAPI($this->apikey, true);

		// Bit hacky, we need to tell the MCAPI class
		// which dbConfig to use, but we can't add a method on
		// to the production MCAPI class, so we have to check if
		// it exists first.
		if (method_exists($this->__api, 'setConfig')) {
			$this->__api->setConfig($this->useDbConfig);
		}
	}

/**
 * List summary information about all mailing lists.
 * 
 * @param bool $useCache If true use the cached result if available.
 * @return array An array of mailing list data.
 */
	public function listMailinglists($useCache = true) {
		$cacheName = $this->__getCacheName(__FUNCTION__);
		if ($useCache) {
			$cachedResult = $this->__getCachedResult($cacheName);
			if ($cachedResult !== false) {
				return $cachedResult;
			}
		}
		$result = $this->__api->lists();
		$this->__cacheResult($cacheName, $result);
		return $result;
	}

/**
 * Get information about a specific mailing list.
 * 
 * @param string $id The id of the list to get information about.
 * @param bool $useCache If true, use the cached result if available.
 * @return array Array of data about a list.
 * @link MailingList::listMailinglists.
 */
	public function getMailinglist($id, $useCache = true) {
		$cacheName = $this->__getCacheName(__FUNCTION__, array($id));
		if ($useCache) {
			$cachedResult = $this->__getCachedResult($cacheName);
			if ($cachedResult !== false) {
				return $cachedResult;
			}
		}
		$result = $this->__api->lists(array('list_id' => $id));
		$this->__cacheResult($cacheName, $result);
		return $result;
	}

/**
 * Get a list of the subscribers to a mailing list.
 * 
 * @param int $listId The id of the list to get information about.
 * @param bool $useCache If true, use the cached result if available.
 * @return array List of subscriber information.
 */
	public function listSubscribers($listId, $useCache = true) {
		$cacheName = $this->__getCacheName(__FUNCTION__, array($listId));
		if ($useCache) {
			$cachedResult = $this->__getCachedResult($cacheName);
			if ($cachedResult != null) {
				return $cachedResult;
			}
		}
		$result = $this->__api->listMembers($listId, 'subscribed', null, 0, 5000 );
		$this->__cacheResult($cacheName, $result);
		return $result;
	}

/**
 * Subscribe an e-mail to a list.
 * 
 * @param int $listId The id of the list to subscribe to
 * @param string $email The email address to subscribe.
 * @return bool True if email was subscribed successfully.
 */
	public function subscribe($listId, $email) {
		// Clear the cache for members in list
		$cacheName = $this->__getCacheName('list_subscribed_members', array($listId));
		$this->__clearMailchimpCache($cacheName);
		return $this->__api->listSubscribe($listId, $email);
	}

/**
 * Unsubscribe an e-mail to a list.
 * 
 * @param int $listId The id of the list to unsubscribe from
 * @param string $email The email address to unsubscribe from.
 * @return bool True if email was unsubscribed successfully.
 */
	public function unsubscribe($listId, $email) {
		// Clear the cache for members in list
		$cacheName = $this->__getCacheName('list_subscribed_members', array($listId));
		$this->__clearMailchimpCache($cacheName);
		return $this->__api->listUnsubscribe($listId, $email);
	}

/**
 * Get the last error code that occurred.
 * 
 * @return int The last error code that occurred.
 */
	public function errorCode() {
		return $this->__api->errorCode;
	}

/**
 * Get the last error message.
 * 
 * @return string The last error message.
 */
	public function errorMsg() {
		return $this->__api->errorMessage;
	}

/**
 * Test to see if a certain e-mail address is subscribed to a certain list.
 * 
 * @param string $email The e-mail address to test.
 * @param string $listId The id of the list to test.
 * @param bool $useCache If true use the cached values from MailChimp.
 * @return bool True if email is subscribed to a list, false otherwise.
 */
	public function isEmailAddressSubscriber($email, $listId, $useCache = true) {
		if (is_string($email) && is_string($listId)) {
			$allSubscribers = $this->listSubscribers($listId, $useCache);
			if (is_array($allSubscribers)) {
				$subscriberEmails = Hash::extract($allSubscribers, 'data.{n}.email');
				return in_array($email, $subscriberEmails);
			}
		}

		return false;
	}

/**
 * Get details for all mailing lists, including if an email is subscribed to that list or not.
 * 
 * @param string $email The e-mail address to check.
 * @param bool $useCache If true use the cached values from MailChimp.
 * @return array An array of information about all lists.
 */
	public function getListsAndSubscribeStatus($email, $useCache = true) {
		$allListData = $this->listMailinglists($useCache);

		$numData = count($allListData['data']);
		for ($i = 0; $i < $numData; $i++) {
			$allListData['data'][$i]['subscribed'] = $this->isEmailAddressSubscriber($email, $allListData['data'][$i]['id'], $useCache);
		}

		return $allListData;
	}

/**
 * Make it so that an email is only subscribed to certain lists.
 * 
 * @param string $email The email address.
 * @param $subscriptions A list of list ids that the email address should be subscribed to, address will be unsubscribed from any list not in $subscriptions.
 * @return array Array listing the actions attempted and if they were successful or not.
 */
	public function updateSubscriptions($email, $subscriptions) {
		$actionsPerformed = array();

		// Need the most up to date data, so don't use the cache
		$subscribeInfo = $this->getListsAndSubscribeStatus($email, false);

		foreach ($subscribeInfo['data'] as $subscription) {
			$wantsToBeSubscribed = in_array($subscription['id'], $subscriptions);
			$isSubscribed = $subscription['subscribed'];

			if ($wantsToBeSubscribed != $isSubscribed) {
				$actionInfo = array(
					'list' => $subscription['id'],
					'name' => $subscription['name'],
				);

				if ($wantsToBeSubscribed) {
					$actionInfo['action'] = 'subscribe';
					$actionInfo['successful'] = $this->subscribe($subscription['id'], $email);
				} else {
					$actionInfo['action'] = 'unsubscribe';
					$actionInfo['successful'] = $this->unsubscribe($subscription['id'], $email);
				}

				array_push($actionsPerformed, $actionInfo);
			}
		}

		return $actionsPerformed;
	}

/**
 * Get the key for the cached value.
 *
 * @param string $functionName The name of the function called.
 * @param array $params Params passed to the function (will be part of the function name).
 * @return string Unique string for a function and params that is used as the key for the cache dictionary.
 */
	private function __getCacheName($functionName, $params = array()) {
		$name = $functionName;
		foreach ($params as $val) {
			$name .= $val;
		}
		return $name;
	}

/**
 * Write a result to the cache.
 * 
 * @param string $key The key the result should be stored with.
 * @param mixed $data The result to be stored.
 */
	private function __cacheResult($key, $data) {
		Cache::write($key, $data, 'default');
	}

/**
 * Read a result from the cache.
 * 
 * @param string $key The key the result should be read from.
 * @param mixed $data The stored result, or null if it doesn't exist.
 */
	private function __getCachedResult($key) {
		return Cache::read($key, 'default');
	}

/**
 * Clear some data from the cache
 * 
 * @param string $key The key to clear
 */
	private function __clearMailchimpCache($key) {
		Cache::delete($key, 'default');
	}
}