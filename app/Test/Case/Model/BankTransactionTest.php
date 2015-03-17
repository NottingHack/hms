<?php

    App::uses('BankTransaction', 'Model');

    class BankTransactionTest extends CakeTestCase
    {
        public $fixtures = array('app.BankTransaction', 'app.Bank', 'app.account' );

        public function setUp() 
        {
        	parent::setUp();
            $this->BankTransaction = ClassRegistry::init('BankTransaction');
        }
        
        public function testGetBankTransactionListAll()
        {
            $bankTransactions = $this->BankTransaction->getBankTransactionList(false);
            
            $this->assertIdentical(count( $bankTransactions), $this->BankTransaction->find('count'), 'All transcations not included.' );
            $this->assertInternalType( 'array', $bankTransactions, 'bankTransactions is not an array.' );
            
            foreach ($bankTransactions as $transaction)
            {
                debug($transaction);
                $this->assertArrayHasKey( 'id', $transaction, 'Transaction has no id.');
                $this->assertGreaterThan( 0, $transaction['id'], 'Trasnaction is is invalid.');
                
                $this->assertArrayHasKey( 'description', $transaction, 'Transaction has no description.');
                $this->assertInternalType( 'string', $transaction['description'], 'Transaction descrption is not a string.');
                $this->assertArrayHasKey( 'date', $transaction, 'Trasnasction has no date.');
                $this->assertArrayHasKey( 'amount', $transaction, 'Transaction has no amount.');
                $this->assertArrayHasKey( 'bank', $transaction, 'Transaction has no bank name.');
                $this->assertArrayHasKey( 'id', $transaction['account'], ' Transaction has no account_id.');

            }
            
            $query = $this->BankTransaction->getBankTransactionList(true);
            $this->assertArrayHasKey( 'conditions', $query, 'Query has no conditions key.');
            
        }

        public function testGetBankTransactionListForMember()
        {
            // TODO
        }
        
        public function testFormatBankTransactionList() {
            // TODO
        }
    }

?>