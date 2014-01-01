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
 * @package       dev.Setup.Common
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Generate realistic(ish) data for mailing lists.
 */
class MailingListDataGenerator {

/**
 * Array of mailing lists.
 * @var array
 */
	private $__mailingLists = array();

/**
 * Array of mailing list subscriptions.
 * @var array
 */
	private $__mailingListSubscriptions = array();

/**
 * Constructor
 */
	public function __construct() {
		$this->__populateMailingLists();
	}

/**
 * Get the array of mailing lists data.
 *
 * @return array The array of mailing lists data.
 */
	public function getMailingListsData() {
		return $this->__mailingLists;
	}

/**
 * Get the array of mailing list subscriptions data.
 *
 * @return array The array of mailing list subscriptions data.
 */
	public function getMailingListSubscriptionsData() {
		return $this->__mailingListSubscriptions;
	}

/**
 * Subscribe an e-mail to a mailing list.
 *
 * @param string $email The e-mail address to subscribe.
 * @param string $listId The id of the list to subscribe them to.
 * @param int $timestamp The time the subscription happened.
 */
	public function subscribeEmailToList($email, $listId, $timestamp) {
		$recordId = count($this->__mailingListSubscriptions) + 1;

		$record = array(
			'mailinglist_id' => $listId,
			'email' => $email,
			'timestamp' => date('Y-m-d H:i:s', $timestamp),
		);

		array_push($this->__mailingListSubscriptions, $record);

		// Adjust the number of members subscribed to a list
		$numMailingLists = count($this->__mailingLists);
		for ($i = 0; $i < $numMailingLists; $i++) {
			if ($this->__mailingLists[$i]['id'] == $listId) {
				$this->__mailingLists[$i]['member_count']++;
			}
		}
	}

/**
 * Populate the mailing lists array.
 */
	private function __populateMailingLists() {
		$this->__mailingLists = array(
			array(
				'id' => 'us8gz1v8rq',
				'web_id' => '30569',
				'name' => 'Nottingham Hackspace Announcements',
				'date_created' => '2012-06-28 19:12:00',
				'email_type_option' => '1',
				'use_awesomebar' => false,
				'default_from_name' => 'Nottingham Hackspace',
				'default_from_email' => 'info@nottinghack.org.uk',
				'default_subject' => 'An Announcement From Nottingham Hackspace',
				'default_language' => 'en',
				'list_rating' => '3.5',
				'subscribe_url_short' => 'http://eepurl.com/ncaln',
				'subscribe_url_long' => 'http://nottinghack.us5.list-manage.com/subscribe?u=a4e59e4c29bd40e76419a037b&id=us8gz1v8rq',
				'beamer_address' => 'YTRlNTllNGMyOWJkNDBlNzY0MTlhMDM3Yi02YTkzMzc3ZS05ZTU5LTQ2ZmUtOTQ5Ni04ODQyYTAzOWVlN2Y=@campaigns.mailchimp.com',
				'visibility' => 'pub',
				'member_count' => 0,
				'unsubscribe_count' => 6,
				'cleaned_count' => 1,
				'member_count_since_send' => 8,
				'unsubscribe_count_since_send' => 0,
				'cleaned_count_since_send' => 0,
				'campaign_count' => 24,
				'grouping_count' => 0,
				'group_count' => 0,
				'merge_var_count' => 2,
				'avg_sub_rate' => 22,
				'avg_unsub_rate' => 1,
				'target_sub_rate' => 1,
				'open_rate' => 46.108140225787,
				'click_rate' => 13.967310549777,
			),
			array(
				'id' => '455de2ac56',
				'web_id' => '64789',
				'name' => 'Nottingham Hackspace The Other List',
				'date_created' => '2013-01-12 14:43:00',
				'email_type_option' => '1',
				'use_awesomebar' => false,
				'default_from_name' => 'Nottingham Hackspace',
				'default_from_email' => 'info@nottinghack.org.uk',
				'default_subject' => 'Something Else From Nottingham Hackspace',
				'default_language' => 'en',
				'list_rating' => '2.3',
				'subscribe_url_short' => 'http://eepurl.com/sdfet',
				'subscribe_url_long' => 'http://nottinghack.us5.list-manage.com/subscribe?u=a4e59e4c29bd40e76419a037b&id=455de2ac56',
				'beamer_address' => 'YTRlNTllNGMyOWJkNDBlNzY0MTlhMDM3Yi02YTkzMzc3ZS05ZTU5LTQ2ZmUtOTQ5Ni04ODQyYTAzOWVlN2Y=@campaigns.mailchimp.com',
				'visibility' => 'pub',
				'member_count' => 0,
				'unsubscribe_count' => 2,
				'cleaned_count' => 1,
				'member_count_since_send' => 3,
				'unsubscribe_count_since_send' => 1,
				'cleaned_count_since_send' => 0,
				'campaign_count' => 2,
				'grouping_count' => 0,
				'group_count' => 0,
				'merge_var_count' => 2,
				'avg_sub_rate' => 3,
				'avg_unsub_rate' => 1,
				'target_sub_rate' => 1,
				'open_rate' => 24.108140225787,
				'click_rate' => 91.967310549777,
			),
		);
	}
}