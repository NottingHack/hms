<?php

	class BankFixture extends CakeTestFixture
	{
		public $useDbConfig = 'test';
		public $import = 'Bank';
		public $records = array(
            array('bank_id' => 1, 'name' => 'Natwest'),
            array('bank_id' => 2, 'name' => 'TSB'),
            
        );
	}

?>