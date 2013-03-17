<?php

	App::uses('AppModel', 'Model');
	App::import('Lib/MailChimp', 'MCAPI');	
	App::uses('PhpReader', 'Configure');

	/**
	 * Model for all mailing list
	 *
	 *
	 * @package       app.Model
	 */
	class MailingList extends AppModel 
	{	
		public $useTable = false; //!< Get data from the MailChimp API instead.

		var $apiKey = '';
		var $api = null;


		//! Constructor
		/*!
			@param mixed $id The id to start the model on.
			@param string $table The table to use for this model.
			@param string ds The connection name this model is connected to.
		*/
		public function __construct($id = false, $table = null, $ds = null)
		{
			parent::__construct($id, $table, $ds);

			Configure::config('default', new PhpReader());
			Configure::load('mailchimp', 'default');

			// Set-up mailchimp
			$this->apikey = Configure::read('mailchimp_apiKey');
			$this->api = new MCAPI($this->apikey);
			$this->api->useSecure(true);
		}

		//! List summary information about all mailing lists.
		/*!
			@param bool $useCache If true use the cached result if available.
			@retval array An array of mailing list data.
		*/
		public function listMailinglists($useCache = true)
		{
			$cacheName = $this->_getCacheName(__FUNCTION__);
			if($useCache)
			{
				$cachedResult = $this->_getCachedResult($cacheName);
				if($cachedResult !== false)
				{
					return $cachedResult;
				}
			}
			$result = $this->api->lists();
			$this->_cacheResult($cacheName, $result);
			return $result;
		}

		//! Get information about a specific mailing list.
		/*!
			@param string $id The id of the list to get information about.
			@param bool $useCache If true, use the cached result if available.
			@retval array Array of data about a list.
			@sa MailingList::listMailinglists.
		*/
		public function getMailinglist($id, $useCache = true)
		{
			$cacheName = $this->_getCacheName(__FUNCTION__, array($id));
			if($useCache)
			{
				$cachedResult = $this->_getCachedResult($cacheName);
				if($cachedResult !== false)
				{
					return $cachedResult;
				}
			}
			$result = $this->api->lists(array('list_id' => $id));
			$this->_cacheResult($cacheName, $result);
			return $result;
		}

		//! Get a list of the subscribers to a mailing list.
		/*!
			@param int $listId The id of the list to get information about.
			@param bool $useCache If true, use the cached result if available.
			@retval array List of subscriber information.
		*/
		public function listSubscribers($listId, $useCache = true)
		{
			$cacheName = $this->_getCacheName(__FUNCTION__, array($listId));
			if($useCache)
			{	
				$cachedResult = $this->_getCachedResult($cacheName);
				if($cachedResult != null)
				{
					return $cachedResult;
				}
			}
			$result = $this->api->listMembers($listId, 'subscribed', null, 0, 5000 );
			$this->_cacheResult($cacheName, $result);
			return $result;
		}

		//! Subscribe an e-mail to a list.
		/*!
			@param int $listId The id of the list to subscribe to
			@param string $email The email address to subscribe.
			@retval bool True if email was subscribed successfully.
		*/
		public function subscribe($listId, $email)
		{
			# Clear the cache for members in list
			$cacheName = $this->_getCacheName('list_subscribed_members', array($listId));
			$this->_clearMailchimpCache($cacheName);
			return $this->api->listSubscribe($listId, $email);
		}

		//! Unsubscribe an e-mail to a list.
		/*!
			@param int $listId The id of the list to unsubscribe from
			@param string $email The email address to unsubscribe from.
			@retval bool True if email was unsubscribed successfully.
		*/
		public function unsubscribe($listId, $email)
		{
			# Clear the cache for members in list
			$cacheName = $this->_getCacheName('list_subscribed_members', array($listId));
			$this->_clearMailchimpCache($cacheName);
			return $this->api->listUnsubscribe($listId, $email);
		}

		//! Get the last error code that occurred.
		/*!
			@retval int The last error code that occurred.
		*/
		public function errorCode()
		{
			return $this->api->errorCode;
		}

		//! Get the last error message.
		/*!
			@retval string The last error message.
		*/
		public function errorMsg()
		{
			return $this->api->errorMessage;
		}

		//! Test to see if a certain e-mail address is subscribed to a certain list.
		/*!
			@param string $email The e-mail address to test.
			@param string $listId The id of the list to test.
			@param bool $useCache If true use the cached values from MailChimp.
			@retval bool True if email is subscribed to a list, false otherwise.
		*/
		public function isEmailAddressSubscriber($email, $listId, $useCache = true)
		{
			if(	is_string($email) &&
				is_string($listId))
			{
				$allSubscribers = $this->listSubscribers($listId, $useCache);
				if(is_array($allSubscribers))
				{
					$subscriberEmails = Hash::extract($allSubscribers, 'data.{n}.email');
					return in_array($email, $subscriberEmails);
				}
			}

			return false;
		}

		//! Get details for all mailing lists, including if an email is subscribed to that list or not.
		/*!
			@param string $email The e-mail address to check.
			@param bool $useCache If true use the cached values from MailChimp.
			@retval array An array of information about all lists.
		*/
		public function getListsAndSubscribeStatus($email, $useCache = true)
		{
			$allListData = $this->listMailinglists($useCache);

			for($i = 0; $i < count($allListData['data']); $i++)
			{
				$allListData['data'][$i]['subscribed'] = $this->isEmailAddressSubscriber($email, $allListData['data'][$i]['id'], $useCache);
			}

			return $allListData;
		}

		//! MAke it so that an email is only subscribed to certain lists.
		/*!
			@param string $email The email address.
			@param $subscriptions A list of list ids that the email address should be subscribed to, address will be unsubscribed from any list not in $subscriptions.
			@retval array Array listing the actions attempted and if they were successful or not.
		*/
		public function updateSubscriptions($email, $subscriptions)
		{
			$actionsPerformed = array();

			// Need the most up to date data, so don't use the cache
			$subscribeInfo = $this->getListsAndSubscribeStatus($email, false);
			foreach ($subscribeInfo['data'] as $subscription) 
			{
				$wantsToBeSubscribed = in_array($subscription['id'], $subscriptions);
				$isSubscribed = $subscription['subscribed'];

				if($wantsToBeSubscribed != $isSubscribed)
				{
					$actionInfo = array(
						'list' => $subscription['id'],
					);

					if($wantsToBeSubscribed)
					{
						$actionInfo['action'] = 'subscribe';
						$actionInfo['successful'] = $this->subscribe($subscription['id'], $email);
					}
					else
					{
						$actionInfo['action'] = 'unsubscribe';
						$actionInfo['successful'] = $this->unsubscribe($subscription['id'], $email);
					}

					array_push($actionsPerformed, $actionInfo);
				}
			}

			return $actionsPerformed;
		}

		//! Get the key for the cached value.
		/*!
			@ratval string Unique string for a function and params that is used as the key for the cache dictionary.
		*/
		private function _getCacheName($functionName, $params = array())
		{
			$name = $functionName;
			foreach ($params as $val) {
				$name .= $val;
			}
			return $name;
		}

		//! Write a result to the cache.
		/*!
			@param string $key The key the result should be stored with.
			@param mixed $data The result to be stored.
		*/
		private function _cacheResult($key, $data)
		{
			Cache::write($key, $data, 'default');
		}

		//! Read a result from the cache.
		/*!
			@param string $key The key the result should be read from.
			@param mixed $data The stored result, or null if it doesn't exist.
		*/
		private function _getCachedResult($key)
		{
			return Cache::read($key, 'default');
		}

		//! Clear some data from the cache
		/*!
			@param string $key The key to clear
		*/
		private function _clearMailchimpCache($key)
		{
			Cache::delete($key, 'default');
		}
	}
?>