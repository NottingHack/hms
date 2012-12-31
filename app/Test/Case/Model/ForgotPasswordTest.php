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

            $this->assertIdentical( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword($correctPassword), true );
            $this->assertIdentical( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword($incorrectPassword), false );

            $this->ForgotPassword->data['ForgotPassword']['new_password'] = '0';
            $this->assertIdentical( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword(0), false );
            $this->assertIdentical( $this->ForgotPassword->newPasswordConfirmMatchesNewPassword(null), false );
        }

        public function testFindMemberWithEmail()
        {
            $this->assertIdentical( $this->ForgotPassword->findMemberWithEmail( 'm.pryce@example.org' ), true );
            $this->assertIdentical( $this->ForgotPassword->findMemberWithEmail( strtoupper('a.santini@hotmail.com') ), true );
            $this->assertIdentical( $this->ForgotPassword->findMemberWithEmail( 'CherylLCarignan@teleworm.us' ), true );
            $this->assertIdentical( $this->ForgotPassword->findMemberWithEmail( 'DorothyDRussell@dayrep.com' ), true );
            $this->assertIdentical( $this->ForgotPassword->findMemberWithEmail( 'about@example.org' ), false );
        }

        public function testCreateNewEntry()
        {
            $prevCount = $this->ForgotPassword->find('count');

            $data = $this->ForgotPassword->createNewEntry(2);
            $this->assertNotIdentical( $data, false );
            $this->assertIdentical( Hash::get($data, 'ForgotPassword.member_id'), 2);

            $this->assertIdentical($this->ForgotPassword->find('count'), $prevCount + 1);
        }
    }

?>