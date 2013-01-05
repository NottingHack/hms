<?php

    App::uses('Member', 'Model');

    class MemberTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.GroupsMember', 'app.member', 'app.Status', 'app.Group', 'app.Account', 'app.Pin' );

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

            $this->assertTrue($this->Member->passwordConfirmMatchesPassword($testEmail), 'Password failed to match');
            $this->assertFalse($this->Member->passwordConfirmMatchesPassword($anotherTestEmail), 'Password matched when it should not have.');
            $this->assertFalse($this->Member->passwordConfirmMatchesPassword(''), 'Password matched when it should not have.');
            $this->assertFalse($this->Member->passwordConfirmMatchesPassword(null), 'Password matched when it should not have.');

            $this->Member->data['Member']['password'] = 0;
            $this->assertFalse($this->Member->passwordConfirmMatchesPassword('0'), 'Password matched across types.');
        }

        public function testCheckUniqueUsername()
        {
            $testUsernameTaken = 'StrippingDemonic';
            $testUsernameNotTaken = 'TheAwfulGamer';    // Chosen by online random name generator.
                                                        // guaranteed to be random.

            $this->Member->data['Member']['member_id'] = 900;

            $this->assertFalse($this->Member->checkUniqueUsername($testUsernameTaken), 'Username StrippingDemonic was not taken.');
            $this->assertFalse($this->Member->checkUniqueUsername(strtolower($testUsernameTaken)), 'Username strippingdemonic was not taken.');
            $this->assertFalse($this->Member->checkUniqueUsername(strtoupper($testUsernameTaken)), 'Username STRIPPINGDEMONIC was not taken.');

            $this->assertTrue($this->Member->checkUniqueUsername($testUsernameNotTaken), 'Username TheAwfulGamer was taken.');
            $this->assertTrue($this->Member->checkUniqueUsername(strtolower($testUsernameNotTaken)), 'Username theawfulgamer was taken.');
            $this->assertTrue($this->Member->checkUniqueUsername(strtoupper($testUsernameNotTaken)), 'Username THEAWFULGAMER was taken.');

            $this->Member->data['Member']['member_id'] = 1;

            $this->assertTrue($this->Member->checkUniqueUsername($testUsernameTaken), 'Username test failed to exclude member 1.');
            $this->assertTrue($this->Member->checkUniqueUsername(strtolower($testUsernameTaken)), 'Username test failed to exclude member 1.');
            $this->assertTrue($this->Member->checkUniqueUsername(strtoupper($testUsernameTaken)), 'Username test failed to exclude member 1.');
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
                $this->assertTrue($this->Member->checkEmailMatch($testEmails[$i]), 'Email for member ' . $i + 1 . ' did not match ' . $testEmails[$i] . '.');
                for($j = 0; $j < count($testEmails); $j++)
                {
                    if($i != $j)
                    {
                        $this->assertFalse($this->Member->checkEmailMatch($testEmails[$j]), 'Email for member ' . $i + 1 . ' matched ' . $testEmails[$j] . '.');
                    }
                }
            }
        }

        public function testBeforeSave()
        {
            $this->Member->data['Member']['balance'] = -400;
            $this->Member->beforeSave();
            $this->assertFalse(isset($this->Member->data['Member']['balance']), 'BeforeSave failed to unset Member.balance.');
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
            $this->assertTrue( $this->Member->doesMemberExistWithEmail( 'm.pryce@example.org' ), 'Failed to find member with e-mail: m.pryce@example.org.' );
            $this->assertTrue( $this->Member->doesMemberExistWithEmail( strtoupper('a.santini@hotmail.com') ), 'Failed to find member with e-mail: A.SANTINIT@HOTMAIL.COM.' );
            $this->assertTrue( $this->Member->doesMemberExistWithEmail( 'CherylLCarignan@teleworm.us' ), 'Failed to find member with e-mail: CherylLCarignan@teleworm.us.' );
            $this->assertTrue( $this->Member->doesMemberExistWithEmail( 'DorothyDRussell@dayrep.com' ), 'Failed to find member with e-mail: DorothyDRussell@dayrep.com.' );
            $this->assertFalse( $this->Member->doesMemberExistWithEmail( 'about@example.org' ), 'Found member with e-mail: about@example.org.' );
        }

        public function testGetMemberSummaryAll()
        {
            $memberList = $this->Member->getMemberSummaryAll();

            $this->assertIdentical( count($memberList), $this->Member->find('count'), 'All members not included.' );
            $this->assertInternalType( 'array', $memberList, 'memberList is not an array.' );

            foreach ($memberList as $memberInfo)
            {
                $this->assertArrayHasKey( 'id', $memberInfo, 'Member has no id.' ); 
                $this->assertGreaterThan( 0, $memberInfo['id'], 'Member id is invalid.' );

                $this->assertArrayHasKey( 'name', $memberInfo, 'Member has no name.' ); 
                $this->assertArrayHasKey( 'email', $memberInfo, 'Member has no email.' ); 
                $this->assertArrayHasKey( 'groups', $memberInfo, 'Member has no groups.' ); 

                foreach ($memberInfo['groups'] as $group) 
                {
                    $this->assertArrayHasKey( 'id', $group, 'Group has no id.' ); 
                    $this->assertArrayHasKey( 'description', $group, 'Group has no description.' );
                    $this->assertInternalType( 'string', $group['description'], 'Group description is not a string.' );
                }

                $this->assertArrayHasKey( 'status', $memberInfo, 'Member has no status.' ); 
                $this->assertInternalType( 'array', $memberInfo['status'], 'No array by the name of status' );
            }
        }

        public function testGetMemberSummaryForStatus()
        {
            $this->assertIdentical( count($this->Member->getMemberSummaryForStatus(0)), 0, 'Status 0 has some members.');
            $this->assertIdentical( count($this->Member->getMemberSummaryForStatus(7)), 0, 'Status 7 has some members.');

            $memberList = $this->Member->getMemberSummaryForStatus(1);

            $this->assertIdentical( count($memberList), $this->Member->find('count', array( 'conditions' => array('Member.member_status' => 1) )), 'All members not included.' );
            $this->assertInternalType( 'array', $memberList, 'memberList is not an array.' );

            foreach ($memberList as $memberInfo)
            {
                $this->assertArrayHasKey( 'id', $memberInfo, 'Member has no id.' ); 
                $this->assertGreaterThan( 0, $memberInfo['id'], 'Member id is invalid.' );

                $this->assertArrayHasKey( 'name', $memberInfo, 'Member has no name.' ); 
                $this->assertArrayHasKey( 'email', $memberInfo, 'Member has no email.' ); 
                $this->assertArrayHasKey( 'groups', $memberInfo, 'Member has no groups.' ); 

                foreach ($memberInfo['groups'] as $group) 
                {
                    $this->assertArrayHasKey( 'id', $group, 'Group has no id.' ); 
                    $this->assertArrayHasKey( 'description', $group, 'Group has no description.' );
                    $this->assertInternalType( 'string', $group['description'], 'Group description is not a string.' );
                }

                $this->assertArrayHasKey( 'status', $memberInfo, 'Member has no status.' ); 
                $this->assertInternalType( 'array', $memberInfo['status'], 'No array by the name of status' );
            }
        }
    }

?>