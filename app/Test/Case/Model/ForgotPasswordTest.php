<?php

    App::uses('ForgotPassword', 'Model');
    App::uses('Member', 'Model');

    class ForgotPasswordTest extends CakeTestCase 
    {
        public $fixtures = array(
                                 'app.ForgotPassword',
                                 'app.Member',
                                 'app.Status',
                                 'app.Account',
                                 'app.Group',
                                 'app.GroupsMember',
                                 'app.Pin',
                                 'app.StatusUpdate',
                                 'app.RfidTag',
                                 );

        public function setUp() 
        {
        	parent::setUp();
            $this->ForgotPassword = ClassRegistry::init('ForgotPassword');
        }

        public function testNewPasswordConfirmMatchesNewPassword()
        {
            $correctPassword = 'yD9zBw8q';
            $incorrectPassword = '5SdjMFaL';

            $this->ForgotPassword->data['ForgotPassword']['new_password'] = $correctPassword;

            $this->assertTrue( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword(array('new_password_confirm' => $correctPassword)), 'Password failed to match.' );
            $this->assertFalse( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword(array('new_password_confirm' => $incorrectPassword)), 'Password matched when it should not have.' );

            $this->ForgotPassword->data['ForgotPassword']['new_password'] = '0';
            $this->assertFalse( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword(array('new_password_confirm' => 0)), 'Password matched across types.' );
            $this->assertFalse( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword(array('new_password_confirm' => null)), 'Password matched across types.' );
        }

        public function testFindMemberWithEmail()
        {
            $this->assertTrue( $this->ForgotPassword->findMemberWithEmail( array('email' => 'm.pryce@example.org' )), 'Failed to find member with e-mail: m.pryce@example.org.' );
            $this->assertTrue( $this->ForgotPassword->findMemberWithEmail( array('email' => strtoupper('a.santini@hotmail.com')) ), 'Failed to find member with e-mail: A.SANTINIT@HOTMAIL.COM.' );
            $this->assertTrue( $this->ForgotPassword->findMemberWithEmail( array('email' => 'CherylLCarignan@teleworm.us' )), 'Failed to find member with e-mail: CherylLCarignan@teleworm.us.' );
            $this->assertTrue( $this->ForgotPassword->findMemberWithEmail( array('email' => 'DorothyDRussell@dayrep.com' )), 'Failed to find member with e-mail: DorothyDRussell@dayrep.com.' );
            $this->assertFalse( $this->ForgotPassword->findMemberWithEmail( array('email' => 'about@example.org' )), 'Found member with e-mail: about@example.org.' );
        }

        public function testCreateNewEntry()
        {
            $prevCount = $this->ForgotPassword->find('count');

            $result = $this->ForgotPassword->createNewEntry(2);
            $this->assertInternalType( 'string', $result, 'Valid data was not handled correctly.');
            $this->assertTrue( ForgotPassword::isValidGuid($result), 'Invalid guid returned.' );

            $this->assertIdentical( $this->ForgotPassword->find('count'), $prevCount + 1, 'Failed to update database correctly.' );
        }

        public function testIsEntryValid()
        {
            $this->assertTrue( $this->ForgotPassword->isEntryValid('50b104e4-33f8-4821-b756-5e100a000005', 2), 'Valid input was not handled correctly.' );
            $this->assertFalse( $this->ForgotPassword->isEntryValid('50b0ec45-8984-48b8-ac8a-5db90a000005', 1), 'Invalid input was not handled correctly.' );
            $this->assertFalse( $this->ForgotPassword->isEntryValid('50be19c8-0968-43ba-be1b-0990bcda665d', 3), 'Invalid input was not handled correctly.' );
            $this->assertFalse( $this->ForgotPassword->isEntryValid('50b104e4-33f8-4821-b756-5e100a000005', 1), 'Invalid input was not handled correctly.' );
            $this->assertFalse( $this->ForgotPassword->isEntryValid('50b0ec45-8984-48b8-ac8a-5db90a000005', 0), 'Invalid input was not handled correctly.' );
        }

        public function testInvalidateEntry()
        {
            $this->assertFalse( $this->ForgotPassword->expireEntry('50b0ec45-8984-48b8-ac8a-5db90a000003'), 'Invalid input was not handled correctly.' );
            $this->assertTrue( $this->ForgotPassword->expireEntry('50b104e4-33f8-4821-b756-5e100a000005'), 'Unable to expire valid guid.' );

            $record = $this->ForgotPassword->find('first', array('conditions' => array('ForgotPassword.request_guid' => '50b104e4-33f8-4821-b756-5e100a000005')));
            $this->assertTrue( is_array($record), 'Record was invalid.' );
            $this->assertArrayHasKey( 'member_id', $record['ForgotPassword'], 'Record has no member id.' );
            $this->assertArrayHasKey( 'request_guid', $record['ForgotPassword'], 'Record has no member id.' );
            $this->assertArrayHasKey( 'timestamp', $record['ForgotPassword'], 'Record has no member id.' );
            $this->assertArrayHasKey( 'expired', $record['ForgotPassword'], 'Record has no member id.' );
            $this->assertEqual( $record['ForgotPassword']['expired'], 1, 'Record was not expired.' );
        }

        public function testIsValidGuid()
        {
            $this->assertTrue( ForgotPassword::isValidGuid(String::UUID()), 'Valid input was not handled correctly.' );
            $this->assertFalse( ForgotPassword::isValidGuid(null), 'Invalid input was not handled correctly.' );
            $this->assertFalse( ForgotPassword::isValidGuid(-1), 'Invalid input was not handled correctly.' );
            $this->assertFalse( ForgotPassword::isValidGuid(1), 'Invalid input was not handled correctly.' );
            $this->assertFalse( ForgotPassword::isValidGuid('sdafrgsg'), 'Invalid input was not handled correctly.' );
            $this->assertFalse( ForgotPassword::isValidGuid(array()), 'Invalid input was not handled correctly.' );
        }
    }

?>