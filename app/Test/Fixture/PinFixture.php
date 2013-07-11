<?php

	class PinFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'Pin';

		public $records = array(
			array('pin_id' => 1, 'pin' => '8298', 'date_added' => '2012-12-03 16:56:02', 'expiry' => null, 'state' => 30, 'member_id' => 1),
			array('pin_id' => 2, 'pin' => '7422', 'date_added' => '2012-12-03 23:56:43', 'expiry' => null, 'state' => 30, 'member_id' => 2),
			array('pin_id' => 3, 'pin' => '5142', 'date_added' => '2012-12-18 20:15:00', 'expiry' => null, 'state' => 30, 'member_id' => 3),
			array('pin_id' => 4, 'pin' => '5436', 'date_added' => '2012-12-18 21:01:05', 'expiry' => null, 'state' => 30, 'member_id' => 4),
			array('pin_id' => 5, 'pin' => '3014', 'date_added' => '2012-12-19 19:54:12', 'expiry' => null, 'state' => 30, 'member_id' => 5),
			array('pin_id' => 6, 'pin' => '6940', 'date_added' => '2012-12-22 09:51:10', 'expiry' => null, 'state' => 30, 'member_id' => 6),
		);
	}

?>