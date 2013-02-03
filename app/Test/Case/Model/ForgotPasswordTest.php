<?php

    App::uses('ForgotPassword', 'Model');
    App::uses('Member', 'Model');

    class ForgotPasswordTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.ForgotPassword', 'app.Member', 'app.Status', 'app.Account', 'app.Group', 'app.GroupsMember', 'app.Pin', 'app.StatusUpdate');

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

            $this->assertTrue( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword($correctPassword), 'Password failed to match.' );
            $this->assertFalse( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword($incorrectPassword), 'Password matched when it should not have.' );

            $this->ForgotPassword->data['ForgotPassword']['new_password'] = '0';
            $this->assertFalse( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword(0), 'Password matched across types.' );
            $this->assertFalse( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword(null), 'Password matched across types.' );
        }

        public function testFindMemberWithEmail()
        {
            $this->assertTrue( $this->ForgotPassword->findMemberWithEmail( 'm.pryce@example.org' ), 'Failed to find member with e-mail: m.pryce@example.org.' );
            $this->assertTrue( $this->ForgotPassword->findMemberWithEmail( strtoupper('a.santini@hotmail.com') ), 'Failed to find member with e-mail: A.SANTINIT@HOTMAIL.COM.' );
            $this->assertTrue( $this->ForgotPassword->findMemberWithEmail( 'CherylLCarignan@teleworm.us' ), 'Failed to find member with e-mail: CherylLCarignan@teleworm.us.' );
            $this->assertTrue( $this->ForgotPassword->findMemberWithEmail( 'DorothyDRussell@dayrep.com' ), 'Failed to find member with e-mail: DorothyDRussell@dayrep.com.' );
            $this->assertFalse( $this->ForgotPassword->findMemberWithEmail( 'about@example.org' ), 'Found member with e-mail: about@example.org.' );
        }

        public function testCreateNewEntry()
        {
            $prevCount = $this->ForgotPassword->find('count');

            $data = $this->ForgotPassword->createNewEntry(2);
            $this->assertNotIdentical( $data, false, 'Returned false.' );
            $this->assertIdentical( Hash::get($data, 'ForgotPassword.member_id'), 2, 'Returned and array with the wrong member id.' );

            $this->assertIdentical( $this->ForgotPassword->find('count'), $prevCount + 1, 'Failed to update database correctly.' );
        }
    }

?>