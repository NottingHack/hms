<?php

    App::uses('Member', 'Model');

    class MemberTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.GroupsMember', 'app.member', 'app.Status', 'app.Group', 'app.Account' );

        public function setUp() 
        {
        	parent::setUp();
            $this->Member = ClassRegistry::init('Member');
        }

        public function testPasswordConfirmMatchesPassword()
        {
            $testEmail = 'fub@example.org';
            $anotherTestEmail = 'bar@example.org';

            $this->Member->data['Member']['password'] = $testEmail;

            $this->assertIdentical($this->Member->passwordConfirmMatchesPassword($testEmail), true, 'Password failed to match');
            $this->assertIdentical($this->Member->passwordConfirmMatchesPassword($anotherTestEmail), false, 'Password matched when it should not have.');
            $this->assertIdentical($this->Member->passwordConfirmMatchesPassword(''), false, 'Password matched when it should not have.');
            $this->assertIdentical($this->Member->passwordConfirmMatchesPassword(null), false, 'Password matched when it should not have.');

            $this->Member->data['Member']['password'] = 0;
            $this->assertIdentical($this->Member->passwordConfirmMatchesPassword('0'), false, 'Password matched across types.');
        }

        public function testCheckUniqueUsername()
        {
            $testUsernameTaken = 'StrippingDemonic';
            $testUsernameNotTaken = 'TheAwfulGamer';    // Chosen by online random name generator.
                                                        // guaranteed to be random.

            $this->Member->data['Member']['member_id'] = 900;

            $this->assertIdentical($this->Member->checkUniqueUsername($testUsernameTaken), false, 'Username StrippingDemonic was not taken.');
            $this->assertIdentical($this->Member->checkUniqueUsername(strtolower($testUsernameTaken)), false, 'Username strippingdemonic was not taken.');
            $this->assertIdentical($this->Member->checkUniqueUsername(strtoupper($testUsernameTaken)), false, 'Username STRIPPINGDEMONIC was not taken.');

            $this->assertIdentical($this->Member->checkUniqueUsername($testUsernameNotTaken), true, 'Username TheAwfulGamer was taken.');
            $this->assertIdentical($this->Member->checkUniqueUsername(strtolower($testUsernameNotTaken)), true, 'Username theawfulgamer was taken.');
            $this->assertIdentical($this->Member->checkUniqueUsername(strtoupper($testUsernameNotTaken)), true, 'Username THEAWFULGAMER was taken.');

            $this->Member->data['Member']['member_id'] = 1;

            $this->assertIdentical($this->Member->checkUniqueUsername($testUsernameTaken), true, 'Username test failed to exclude member 1.');
            $this->assertIdentical($this->Member->checkUniqueUsername(strtolower($testUsernameTaken)), true, 'Username test failed to exclude member 1.');
            $this->assertIdentical($this->Member->checkUniqueUsername(strtoupper($testUsernameTaken)), true, 'Username test failed to exclude member 1.');
        }

        public function testCheckEmailMatch()
        {
            $testEmails = array(
                'm.pryce@example.org',
                'a.santini@hotmail.com',
                'g.viles@gmail.com',     
                'k.savala@yahoo.co.uk',
                'j.easterwood@googlemail.com',
                'g.garratte@foobar.org',
            );

            for($i = 0; $i < count($testEmails); $i++)
            {
                $this->Member->data['Member']['member_id'] = $i + 1;
                $this->assertIdentical($this->Member->checkEmailMatch($testEmails[$i]), true, 'Email for member ' . $i + 1 . ' did not match ' . $testEmails[$i] . '.');
                for($j = 0; $j < count($testEmails); $j++)
                {
                    if($i != $j)
                    {
                        $this->assertIdentical($this->Member->checkEmailMatch($testEmails[$j]), false, 'Email for member ' . $i + 1 . ' matched ' . $testEmails[$j] . '.');
                    }
                }
            }
        }

        public function testBeforeSave()
        {
            $this->Member->data['Member']['balance'] = -400;
            $this->Member->beforeSave();
            $this->assertIdentical(isset($this->Member->data['Member']['balance']), false, 'BeforeSave failed to unset Member.balance.');
        }

        public function testAddEmailMustMatch()
        {
            # The Member shouldn't start out with this validation rule
            $this->assertIdentical($this->Member->validator()->getField('email')->getRule('emailMustMatch'), null, 'Email already contains validation rule named \'emailMustMatch\'.');

            $this->Member->addEmailMustMatch();

            $this->assertNotIdentical($this->Member->validator()->getField('email')->getRule('emailMustMatch'), null, 'Email doesn\'t contain validation rule named \'emailMustMatch\'.');
        }

        public function testRemoveEmailMustMatch()
        {
            # The Member shouldn't start out with this validation rule
            $this->Member->addEmailMustMatch();
            $this->Member->removeEmailMustMatch();
            $this->assertIdentical($this->Member->validator()->getField('email')->getRule('emailMustMatch'), null, 'Email still contains validation rule named \'emailMustMatch\'.');
        }

        public function testGetCountForStatus()
        {
            $this->assertIdentical($this->Member->getCountForStatus(1), 2, 'Count for members of status 1 is incorrect.');
            $this->assertIdentical($this->Member->getCountForStatus(2), 2, 'Count for members of status 2 is incorrect.');
            $this->assertIdentical($this->Member->getCountForStatus(3), 2, 'Count for members of status 3 is incorrect.');
            $this->assertIdentical($this->Member->getCountForStatus(4), 2, 'Count for members of status 4 is incorrect.');
            $this->assertIdentical($this->Member->getCountForStatus(5), 5, 'Count for members of status 5 is incorrect.');
            $this->assertIdentical($this->Member->getCountForStatus(6), 1, 'Count for members of status 6 is incorrect.');
            $this->assertIdentical($this->Member->getCountForStatus(0), 0, 'Count for members of status 0 is incorrect.');
        }

        public function testGetCount()
        {
            $this->assertIdentical($this->Member->getCount(), 14, 'Count for total members is incorrect.');
        }

        public function testDoesMemberExistWithEmail()
        {
            $this->assertIdentical( $this->Member->doesMemberExistWithEmail( 'm.pryce@example.org' ), true, 'Failed to find member with e-mail: m.pryce@example.org.' );
            $this->assertIdentical( $this->Member->doesMemberExistWithEmail( strtoupper('a.santini@hotmail.com') ), true, 'Failed to find member with e-mail: A.SANTINIT@HOTMAIL.COM.' );
            $this->assertIdentical( $this->Member->doesMemberExistWithEmail( 'CherylLCarignan@teleworm.us' ), true, 'Failed to find member with e-mail: CherylLCarignan@teleworm.us.' );
            $this->assertIdentical( $this->Member->doesMemberExistWithEmail( 'DorothyDRussell@dayrep.com' ), true, 'Failed to find member with e-mail: DorothyDRussell@dayrep.com.' );
            $this->assertIdentical( $this->Member->doesMemberExistWithEmail( 'about@example.org' ), false, 'Found member with e-mail: about@example.org.' );
        }
    }

?>