<?php

    App::uses('Account', 'Model');

    class AccountTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.Account', 'app.Member' );

        public function setUp() 
        {
        	parent::setUp();
            $this->Account = ClassRegistry::init('Account');
        }

        public function testGeneratePaymentRef()
        {
            $paymentRef = $this->Account->generatePaymentRef();
            $prefix = 'HSNOTTS';

            $this->assertIdentical( strlen($paymentRef), 18 );
            $this->assertTextStartsWith( $prefix, $paymentRef );

            // Check for unsafe chars...
            // Unsafe chars are allowed in the prefix
            $unsafeChars = '015AEILNOSUZ';
            $trimmedRef = substr($paymentRef, strlen($prefix));
            for($i = 0; $i < strlen($unsafeChars); $i++)
            {
                $char = $unsafeChars[$i];
                $this->assertTextNotContains($char, $trimmedRef, "Payment ref string includes unsafe character: $char");
            }

            $this->assertIdentical( $this->Account->find('count', array('conditions' => array( 'Account.payment_ref' => $paymentRef ) ) ), 0 );
        }
    }

?>