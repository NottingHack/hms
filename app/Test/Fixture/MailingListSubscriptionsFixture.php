<?php

	class MailingListSubscriptionsFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $table = 'mailinglist_subscriptions';

		public $fields = array(
			'id' => array('type' => 'integer', 'key' => 'primary'),
			'mailinglist_id' => array('type' => 'string', 'length' => 10),
			'email' => array('type' => 'text', 'length' => 5, 'null' => false),
			'timestamp' => array('type' => 'text', 'null' => false),
		);

		public $records = array(
			array( 'id' => '1', 'mailinglist_id' => '0a6da449c9', 'email' => 'm.pryce@example.org', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '2', 'mailinglist_id' => '0a6da449c9', 'email' => 'a.santini@hotmail.com', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '3', 'mailinglist_id' => '0a6da449c9', 'email' => 'g.viles@gmail.com', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '4', 'mailinglist_id' => '0a6da449c9', 'email' => 'k.savala@yahoo.co.uk', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '5', 'mailinglist_id' => '0a6da449c9', 'email' => 'j.easterwood@googlemail.com', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '6', 'mailinglist_id' => '0a6da449c9', 'email' => 'CherylLCarignan@teleworm.us', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '7', 'mailinglist_id' => '0a6da449c9', 'email' => 'MelvinJFerrell@dayrep.com', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '8', 'mailinglist_id' => '0a6da449c9', 'email' => 'DorothyDRussell@dayrep.com', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '9', 'mailinglist_id' => '0a6da449c9', 'email' => 'HugoJLorenz@dayrep.com', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '10', 'mailinglist_id' => '0a6da449c9', 'email' => 'alreadySubscribed@dayrep.com', 'timestamp' => '2012-02-15 08:56:00' ),
			array( 'id' => '11', 'mailinglist_id' => '455de2ac56', 'email' => 'EvanAtkinson@teleworm.us', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '12', 'mailinglist_id' => '455de2ac56', 'email' => 'RyanMiles@dayrep.com', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '13', 'mailinglist_id' => '455de2ac56', 'email' => 'RoyJForsman@teleworm.us', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '14', 'mailinglist_id' => '455de2ac56', 'email' => 'BettyCParis@teleworm.us', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '15', 'mailinglist_id' => '455de2ac56', 'email' => 'HugoJLorenz@dayrep.com', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '16', 'mailinglist_id' => '455de2ac56', 'email' => 'DorothyDRussell@dayrep.com', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '17', 'mailinglist_id' => '455de2ac56', 'email' => 'MelvinJFerrell@dayrep.com', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '18', 'mailinglist_id' => '455de2ac56', 'email' => 'CherylLCarignan@teleworm.us', 'timestamp' => '2012-06-28 19:12:00' ),
			array( 'id' => '19', 'mailinglist_id' => '455de2ac56', 'email' => 'g.garratte@foobar.org', 'timestamp' => '2012-06-28 19:12:00' ),
		);

		public function creaete($db)
		{
			$ret = parent::create($db);
			var_dump($ret);
			exit();
		}
	}

?>