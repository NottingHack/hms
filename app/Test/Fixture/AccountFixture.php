<?php

	class AccountFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'Account';

		public $records = array(
			array('account_id' => 1, 'payment_ref' => 'HSTSBKF6762KC8JD'),
			array('account_id' => 2, 'payment_ref' => 'HSTSBKFK2R62GQW6'),
			array('account_id' => 3, 'payment_ref' => 'HSTSBKFYT7H4CW3G'),
			array('account_id' => 4, 'payment_ref' => 'HSTSBKFCV3TFFDGX'),
			array('account_id' => 5, 'payment_ref' => 'HSTSBKFQYJPJ33VD'),
			array('account_id' => 6, 'payment_ref' => 'HSTSBKFB8GYFHFC3'),
			array('account_id' => 7, 'payment_ref' => 'HSTSBKFFGXWGKF48'),
			array('account_id' => 8, 'payment_ref' => 'HSTSBKFHVQGT3XF2'),
		);
	}

?>