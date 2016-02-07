<?php

	class RfidTagFixture extends CakeTestFixture
	{
		public $useDbConfig = 'test';
		public $import = 'RfidTag';

		public $records = array(
			array('member_id' => 1, 'rfid_serial' => '103162154', 'state' => '10', 'last_used' => '1454817855', 'friendly_name' => 'White one'),
			array('member_id' => 2, 'rfid_serial' => '1727513448', 'state' => '10', 'last_used' => '1454817855', 'friendly_name' => 'Mango'),
            array('member_id' => 3, 'rfid_serial' => '178100790', 'state' => '10', 'last_used' => '1454817855', 'friendly_name' => ''),
            array('member_id' => 4, 'rfid_serial' => '1846700113', 'state' => '10', 'last_used' => '1454817856', 'friendly_name' => 'White one'),
            array('member_id' => 5, 'rfid_serial' => '158317848', 'state' => '20', 'last_used' => '1454817455', 'friendly_name' => 'Lost'),
            array('member_id' => 6, 'rfid_serial' => '1740899925', 'state' => '10', 'last_used' => '1454817255', 'friendly_name' => ''),
            array('member_id' => 7, 'rfid_serial' => '105994420', 'state' => '10', 'last_used' => '1454810055', 'friendly_name' => 'Oyster'),
		);
	}

?>