<?php

	class BankTransactionFixture extends CakeTestFixture
	{
		public $useDbConfig = 'test';
		public $import = 'BankTransaction';
//        public $tabel = 'bank_transactions';
//        public $primaryKey = 'bank_transaction_id';	
		public $records = array(
            array('bank_transaction_id' => '1','date' => '2015-03-02','description' => 'Mathew Pryce, HSNOTTS6762KC8JD','amount' => '10','bank_id' => '1','account_id' => '1'),
            array('bank_transaction_id' => '2','date' => '2015-02-02','description' => 'Mathew Pryce, HSNOTTS6762KC8JD','amount' => '10','bank_id' => '1','account_id' => '1'),
            array('bank_transaction_id' => '3','date' => '2015-03-04','description' => 'Mathew Pryce HSNOTTS6762KC8JD','amount' => '10','bank_id' => '2','account_id' => '1'),
            array('bank_transaction_id' => '4','date' => '2015-03-02','description' => '5000001','amount' => '371','bank_id' => '1','account_id' => null)
        );
	}

?>