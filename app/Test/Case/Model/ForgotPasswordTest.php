<?php

    App::uses('ForgotPassword', 'Model');
    App::uses('Member', 'Model');

    class ForgotPasswordTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.ForgotPassword', 'app.Member', 'app.Status', 'app.Account', 'app.Group', 'app.GroupsMember' );

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

            $this->assertIdentical( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword($correctPassword), true, 'Password failed to match.' );
            $this->assertIdentical( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword($incorrectPassword), false, 'Password matched when it should not have.' );

            $this->ForgotPassword->data['ForgotPassword']['new_password'] = '0';
            $this->assertIdentical( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword(0), false, 'Password matched across types.' );
            $this->assertIdentical( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword(null), false, 'Password matched across types.' );
        }

        public function testFindMemberWithEmail()
        {
            $this->assertIdentical( $this->ForgotPassword->findMemberWithEmail( 'm.pryce@example.org' ), true, 'Failed to find member with e-mail: m.pryce@example.org.' );
            $this->assertIdentical( $this->ForgotPassword->findMemberWithEmail( strtoupper('a.santini@hotmail.com') ), true, 'Failed to find member with e-mail: A.SANTINIT@HOTMAIL.COM.' );
            $this->assertIdentical( $this->ForgotPassword->findMemberWithEmail( 'CherylLCarignan@teleworm.us' ), true, 'Failed to find member with e-mail: CherylLCarignan@teleworm.us.' );
            $this->assertIdentical( $this->ForgotPassword->findMemberWithEmail( 'DorothyDRussell@dayrep.com' ), true, 'Failed to find member with e-mail: DorothyDRussell@dayrep.com.' );
            $this->assertIdentical( $this->ForgotPassword->findMemberWithEmail( 'about@example.org' ), false, 'Found member with e-mail: about@example.org.' );
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