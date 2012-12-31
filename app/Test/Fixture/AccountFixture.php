<?php

	class AccountFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'Account';

		public $records = array(
			array('account_id' => 1, 'payment_ref' => 'HSNOTTS6762KC8JD7H', 'member_id' => 1),
			array('account_id' => 2, 'payment_ref' => 'HSNOTTSK2R62GQW684', 'member_id' => 2),
			array('account_id' => 3, 'payment_ref' => 'HSNOTTSYT7H4CW3GP9', 'member_id' => 3),
			array('account_id' => 4, 'payment_ref' => 'HSNOTTSCV3TFFDGXXY', 'member_id' => 4),
			array('account_id' => 5, 'payment_ref' => 'HSNOTTSQYJPJ33VDDK', 'member_id' => 5),
			array('account_id' => 6, 'payment_ref' => 'HSNOTTSB8GYFHFC39W', 'member_id' => 6),
			array('account_id' => 7, 'payment_ref' => 'HSNOTTSFGXWGKF48QB', 'member_id' => 13),
			array('account_id' => 8, 'payment_ref' => 'HSNOTTSHVQGT3XF248', 'member_id' => 14),
		);
	}

?>