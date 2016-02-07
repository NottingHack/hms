<?php

    App::uses('Account', 'Model');

    class AccountTest extends CakeTestCase 
    {
        public $fixtures = array(
                                 'app.Account',
                                 'app.Member',
                                 'app.RfidTag',
                                 );

        public function setUp() 
        {
        	parent::setUp();
            $this->Account = ClassRegistry::init('Account');
        }

        public function testGeneratePaymentRef()
        {
            $paymentRef = $this->Account->generateUniquePaymentRef();
            $prefix = 'HSNTSB';

            $this->assertIdentical( strlen($paymentRef), 16, 'Payment ref is incorrect length.' );
            $this->assertTextStartsWith( $prefix, $paymentRef );

            // Check for unsafe chars...
            // Unsafe chars are allowed in the prefix
            $unsafeChars = '015AEILNOSUZ';
            $trimmedRef = substr($paymentRef, strlen($prefix));
            for($i = 0; $i < strlen($unsafeChars); $i++)
            {
                $char = $unsafeChars[$i];
                $this->assertTextNotContains($char, $trimmedRef, "Payment ref string includes unsafe character: $char.");
            }

            $this->assertIdentical( $this->Account->find('count', array('conditions' => array( 'Account.payment_ref' => $paymentRef ) ) ), 0, 'Payment ref generated was not unique.' );
        }

        public function testSetupAccountIfNeeded()
        {
            $this->assertEqual( $this->Account->setupAccountIfNeeded(null), -1, 'Null data was handled incorrectly.' );
            $this->assertEqual( $this->Account->setupAccountIfNeeded(10), -1, 'Invalid data was handled incorrectly.' );
            
            $this->assertEqual( $this->Account->setupAccountIfNeeded(1), 1, 'Valid data was handled incorrectly.' );

            $this->assertEqual( $this->Account->setupAccountIfNeeded(-1), 9, 'Generation data was handled incorrectly.' );

            $data = $this->Account->findByAccountId(9);
            $this->assertInternalType( 'array', $data, 'Record was not saved correctly.' );

            $this->assertArrayHasKey( 'Account', $data, 'Record was not saved correctly.' );
            $this->assertInternalType( 'array', $data['Account'], 'Record was not saved correctly.' );

            $this->assertArrayHasKey( 'account_id', $data['Account'], 'Record was not saved correctly.' );
            $this->assertArrayHasKey( 'payment_ref', $data['Account'], 'Record was not saved correctly.' );
            $this->assertEqual( $data['Account']['account_id'], 9, 'Record was not saved correctly.' );
        }
    }

?>