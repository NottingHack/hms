<?php

class MemberFixture extends CakeTestFixture 
{
	public $useDbConfig = 'test';
	public $fields = array(
		'member_id' => array('type' => 'integer', 'key' => 'primary'),
		'name' => array('type' => 'string', 'length' => 50, 'null' => true),
		'email' => array('type' => 'string', 'length' => 100, 'null' => true),
		'join_date' => 'date',
		'handle' => array('type' => 'string', 'length' => 100, 'null' => true),
		'unlock_text' => array('type' => 'string', 'length' => 95, 'null' => true),
		'balance' => array('type' => 'integer', 'default' => '0', 'null' => false),
		'credit_limit' => array('type' => 'integer', 'default' => '0', 'null' => false),
		'member_status' => array('type' => 'integer', 'null' => true),
		'username' => array('type' => 'string', 'length' => 50, 'null' => true),
		'account_id' => array('type' => 'integer', 'null' => true),
		'address_1' => array('type' => 'string', 'length' => 100, 'null' => true),
		'address_2' => array('type' => 'string', 'length' => 100, 'null' => true),
		'address_city' => array('type' => 'string', 'length' => 100, 'null' => true),
		'address_postcode' => array('type' => 'string', 'length' => 100, 'null' => true),
		'contact_number' => array('type' => 'string', 'length' => 20, 'null' => true),
	);

	public function init()
	{
		$this->records = array(
			array('member_id' => 1, 'name' => 'Test McTestson', 'email' => 'about@example.org', 'join_date' => date(), 'handle' => 'TestMc', 'unlock_text' => 'Back again?', 'balance' => -200, 'credit_limit' => 5000, 'member_status' => 2, 'username' => 'TestMcu', 'account_id' => null, 'address_1' => 'Flat 3d', 'address_2' => 'Fake Street', 'address_city' => 'Faketon', 'address_postcode' => 'NG1 3BQ', 'contact_number' => '07962530814');
		);

		parent::init();
	}
}

?>