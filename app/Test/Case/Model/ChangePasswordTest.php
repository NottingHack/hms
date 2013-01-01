<?php

    App::uses('ChangePassword', 'Model');
    App::uses('Member', 'Model');

    class ChangePasswordTest extends CakeTestCase 
    {
        public function setUp() 
        {
        	parent::setUp();
            $this->ChangePassword = ClassRegistry::init('ChangePassword');
        }

        public function testNewPasswordConfirmMatchesNewPassword()
        {
            $correctPassword = 'yD9zBw8q';
            $incorrectPassword = '5SdjMFaL';

            $this->ChangePassword->data['ChangePassword']['new_password'] = $correctPassword;

            $this->assertIdentical( $this->ChangePassword->newPasswordConfirmMatchesNewPassword($correctPassword), true, 'Password failed to match.' );
            $this->assertIdentical( $this->ChangePassword->newPasswordConfirmMatchesNewPassword($incorrectPassword), false, 'Password matched when it should not have.' );

            $this->ChangePassword->data['ChangePassword']['new_password'] = '0';
            $this->assertIdentical( $this->ChangePassword->newPasswordConfirmMatchesNewPassword(0), false, 'Password matched across types.' );
            $this->assertIdentical( $this->ChangePassword->newPasswordConfirmMatchesNewPassword(null), false, 'Password matched across types.' );
        }
    }

?>