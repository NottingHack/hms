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

            $this->assertTrue( $this->ChangePassword->newPasswordConfirmMatchesNewPassword(array('new_password_confirm' => $correctPassword)), 'Password failed to match.' );
            $this->assertFalse( $this->ChangePassword->newPasswordConfirmMatchesNewPassword(array('new_password_confirm' => $incorrectPassword)), 'Password matched when it should not have.' );

            $this->ChangePassword->data['ChangePassword']['new_password'] = '0';
            $this->assertFalse( $this->ChangePassword->newPasswordConfirmMatchesNewPassword(array('new_password_confirm' => 0)), 'Password matched across types.' );
            $this->assertFalse( $this->ChangePassword->newPasswordConfirmMatchesNewPassword(array('new_password_confirm' => null)), 'Password matched across types.' );
        }
    }

?>