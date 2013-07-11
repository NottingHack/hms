<?php

	class MailingListsFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $table = 'mailinglists';

		public $fields = array(
			'id' => array('type' => 'string', 'length' => 10, 'key' => 'primary'),
			'web_id' => array('type' => 'string', 'length' => 5, 'null' => false),
			'name' => array('type' => 'text', 'null' => false),
			'date_created' => array('type' => 'text', 'null' => false),
			'email_type_option' => array('type' => 'integer', 'null' => false),
			'use_awesomebar' => array('type' => 'integer', 'null' => false),
			'default_from_name' => array('type' => 'text', 'null' => false),
			'default_from_email' => array('type' => 'text', 'null' => false),
			'default_subject' => array('type' => 'text', 'null' => false),
			'default_language' => array('type' => 'text', 'null' => false),
			'list_rating' => array('type' => 'float', 'null' => false),
			'subscribe_url_short' => array('type' => 'text', 'null' => false),
			'subscribe_url_long' => array('type' => 'text', 'null' => false),
			'beamer_address' => array('type' => 'text', 'null' => false),
			'visibility' => array('type' => 'text', 'null' => false),
			'member_count' => array('type' => 'integer', 'null' => false),
			'unsubscribe_count' => array('type' => 'integer', 'null' => false),
			'cleaned_count' => array('type' => 'integer', 'null' => false),
			'member_count_since_send' => array('type' => 'integer', 'null' => false),
			'unsubscribe_count_since_send' => array('type' => 'integer', 'null' => false),
			'cleaned_count_since_send' => array('type' => 'integer', 'null' => false),
			'campaign_count' => array('type' => 'integer', 'null' => false),
			'grouping_count' => array('type' => 'integer', 'null' => false),
			'group_count' => array('type' => 'integer', 'null' => false),
			'merge_var_count' => array('type' => 'integer', 'null' => false),
			'avg_sub_rate' => array('type' => 'integer', 'null' => false),
			'avg_unsub_rate' => array('type' => 'integer', 'null' => false),
			'target_sub_rate' => array('type' => 'integer', 'null' => false),
			'open_rate' => array('type' => 'float', 'null' => false),
			'click_rate' => array('type' => 'float', 'null' => false),
		);

		public $records = array(
			array('id' => 'us8gz1v8rq', 'web_id' => '30569', 'name' => 'Nottingham Hackspace Announcements', 	'date_created' => '2012-06-28 19:12:00', 'email_type_option' => '1', 'use_awesomebar' => '0', 'default_from_name' => 'Nottingham Hackspace', 'default_from_email' => 'info@nottinghack.org.uk', 'default_subject' => 'An Announcement From Nottingham Hackspace', 	'default_language' => 'en', 'list_rating' => '3.5', 'subscribe_url_short' => 'http://eepurl.com/ncaln', 'subscribe_url_long' => 'http://nottinghack.us5.list-manage.com/subscribe?u=a4e59e4c29bd40e76419a037b&id=0a6da449c9', 'beamer_address' => 'YTRlNTllNGMyOWJkNDBlNzY0MTlhMDM3Yi02YTkzMzc3ZS05ZTU5LTQ2ZmUtOTQ5Ni04ODQyYTAzOWVlN2Y=@campaigns.mailchimp.com', 'visibility' => 'pub', 'member_count' => '276', 	'unsubscribe_count' => '6', 'cleaned_count' => '1', 'member_count_since_send' => '8', 'unsubscribe_count_since_send' => '0', 'cleaned_count_since_send' => '0', 'campaign_count' => '24', 	'grouping_count' => '0', 'group_count' => '0', 'merge_var_count' => '2', 'avg_sub_rate' => '22', 	'avg_unsub_rate' => '1', 'target_sub_rate' => '1', 'open_rate' => '46.108140225787', 'click_rate' => '13.967310549777'),
			array('id' => '455de2ac56', 'web_id' => '64789', 'name' => 'Nottingham Hackspace The Other List', 	'date_created' => '2013-01-12 14:43:00', 'email_type_option' => '1', 'use_awesomebar' => '0', 'default_from_name' => 'Nottingham Hackspace', 'default_from_email' => 'info@nottinghack.org.uk', 'default_subject' => 'Something Else From Nottingham Hackspace', 	'default_language' => 'en', 'list_rating' => '2.3', 'subscribe_url_short' => 'http://eepurl.com/sdfet', 'subscribe_url_long' => 'http://nottinghack.us5.list-manage.com/subscribe?u=a4e59e4c29bd40e76419a037b&id=455de2ac56', 'beamer_address' => 'YTRlNTllNGMyOWJkNDBlNzY0MTlhMDM3Yi02YTkzMzc3ZS05ZTU5LTQ2ZmUtOTQ5Ni04ODQyYTAzOWVlN2Y=@campaigns.mailchimp.com', 'visibility' => 'pub', 'member_count' => '23', 	'unsubscribe_count' => '2', 'cleaned_count' => '1', 'member_count_since_send' => '3', 'unsubscribe_count_since_send' => '1', 'cleaned_count_since_send' => '0', 'campaign_count' => '2', 	'grouping_count' => '0', 'group_count' => '0', 'merge_var_count' => '2', 'avg_sub_rate' => '3', 	'avg_unsub_rate' => '1', 'target_sub_rate' => '1', 'open_rate' => '24.108140225787', 'click_rate' => '91.967310549777'),
		);
	}

?>