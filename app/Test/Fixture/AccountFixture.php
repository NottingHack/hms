<?php

	class AccountFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'Account';

		public $records = array(
			array('account_id' => 1, 'payment_ref' => 'HSTSBKF6762KC8JD', 'natwest_ref' => 'HSNOTTSKF6762KC8'),
			array('account_id' => 2, 'payment_ref' => 'HSTSBKFK2R62GQW6', 'natwest_ref' => 'HSNOTTSKFK2R62GQ'),
			array('account_id' => 3, 'payment_ref' => 'HSTSBKFYT7H4CW3G', 'natwest_ref' => 'HSNOTTSKFYT7H4CW'),
			array('account_id' => 4, 'payment_ref' => 'HSTSBKFCV3TFFDGX', 'natwest_ref' => 'HSNOTTSKFCV3TFFD'),
			array('account_id' => 5, 'payment_ref' => 'HSTSBKFQYJPJ33VD', 'natwest_ref' => 'HSNOTTSKFQYJPJ33'),
			array('account_id' => 6, 'payment_ref' => 'HSTSBKFB8GYFHFC3', 'natwest_ref' => 'HSNOTTSKFB8GYFHF'),
			array('account_id' => 7, 'payment_ref' => 'HSTSBKFFGXWGKF48', 'natwest_ref' => 'HSNOTTSKFFGXWGKF'),
			array('account_id' => 8, 'payment_ref' => 'HSTSBKFHVQGT3XF2', 'natwest_ref' => 'HSNOTTSKFHVQGT3X'),
		);
	}

?>