<?php

    App::uses('Member', 'Model');
    App::uses('MailingListTest', 'Test/Case/Model');

    class MemberTest extends CakeTestCase 
    {
        public $fixtures = array(
                                 'app.GroupsMember',
                                 'app.member',
                                 'app.Status',
                                 'app.Group',
                                 'app.Account',
                                 'app.Pin',
                                 'app.StatusUpdate',
                                 'app.ForgotPassword',
                                 'app.MailingLists',
                                 'app.MailingListSubscriptions',
                                 'app.EmailRecord',
                                 'app.RfidTag',
                                 'app.BankTransaction',
                                 'app.Bank',
                                 );

        public function setUp() 
        {
            parent::setUp();

            $this->Member = ClassRegistry::init('Member');
            App::uses('MailingList', 'Model');
            $this->Member->mailingList = new MailingList(false, null, 'test');

            // use a mock Kerberos behaviour during tests
            $this->Member->Behaviors->unload('KrbAuth');
            $this->Member->Behaviors->load('MockKrbAuth');
        }

        public function testPasswordConfirmMatchesPassword()
        {
            $testEmail = 'fub@example.org';
            $anotherTestEmail = 'bar@example.org';

            $this->Member->data['Member']['password'] = $testEmail;

            $this->assertTrue($this->Member->passwordConfirmMatchesPassword(array('password_confirm' => $testEmail)), 'Password failed to match');
            $this->assertFalse($this->Member->passwordConfirmMatchesPassword(array('password_confirm' => $anotherTestEmail)), 'Password matched when it should not have.');
            $this->assertFalse($this->Member->passwordConfirmMatchesPassword(array('password_confirm' => '')), 'Password matched when it should not have.');
            $this->assertFalse($this->Member->passwordConfirmMatchesPassword(array('password_confirm' => null)), 'Password matched when it should not have.');

            $this->Member->data['Member']['password'] = 0;
            $this->assertFalse($this->Member->passwordConfirmMatchesPassword(array('password_confirm' => '0')), 'Password matched across types.');
        }

        public function testCheckUniqueUsername()
        {
            $testUsernameTaken = 'StrippingDemonic';
            $testUsernameNotTaken = 'TheAwfulGamer';    // Chosen by online random name generator.
                                                        // guaranteed to be random.

            $this->Member->data['Member']['member_id'] = 900;

            $this->assertFalse($this->Member->checkUniqueUsername(array('username' => $testUsernameTaken)), 'Username StrippingDemonic was not taken.');
            $this->assertFalse($this->Member->checkUniqueUsername(array('username' => strtolower($testUsernameTaken))), 'Username strippingdemonic was not taken.');
            $this->assertFalse($this->Member->checkUniqueUsername(array('username' => strtoupper($testUsernameTaken))), 'Username STRIPPINGDEMONIC was not taken.');

            $this->assertTrue($this->Member->checkUniqueUsername(array('username' => $testUsernameNotTaken)), 'Username TheAwfulGamer was taken.');
            $this->assertTrue($this->Member->checkUniqueUsername(array('username' => strtolower($testUsernameNotTaken))), 'Username theawfulgamer was taken.');
            $this->assertTrue($this->Member->checkUniqueUsername(array('username' => strtoupper($testUsernameNotTaken))), 'Username THEAWFULGAMER was taken.');

            $this->Member->data['Member']['member_id'] = 1;

            $this->assertTrue($this->Member->checkUniqueUsername(array('username' => $testUsernameTaken)), 'Username test failed to exclude member 1.');
            $this->assertTrue($this->Member->checkUniqueUsername(array('username' => strtolower($testUsernameTaken))), 'Username test failed to exclude member 1.');
            $this->assertTrue($this->Member->checkUniqueUsername(array('username' => strtoupper($testUsernameTaken))), 'Username test failed to exclude member 1.');
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
                $this->assertTrue($this->Member->checkEmailMatch(array('email' => $testEmails[$i])), 'Email for member ' . $i + 1 . ' did not match ' . $testEmails[$i] . '.');
                for($j = 0; $j < count($testEmails); $j++)
                {
                    if($i != $j)
                    {
                        $this->assertFalse($this->Member->checkEmailMatch(array('email' => $testEmails[$j])), 'Email for member ' . $i + 1 . ' matched ' . $testEmails[$j] . '.');
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
            $memberList = $this->Member->getMemberSummaryAll(false);

            $this->assertIdentical( count($memberList), $this->Member->find('count'), 'All members not included.' );
            $this->assertInternalType( 'array', $memberList, 'memberList is not an array.' );

            foreach ($memberList as $memberInfo)
            {
                $this->assertArrayHasKey( 'id', $memberInfo, 'Member has no id.' ); 
                $this->assertGreaterThan( 0, $memberInfo['id'], 'Member id is invalid.' );

                $this->assertArrayHasKey( 'firstname', $memberInfo, 'Member has no firstname.' ); 
                $this->assertArrayHasKey( 'surname', $memberInfo, 'Member has no surname.' ); 
                $this->assertArrayHasKey( 'email', $memberInfo, 'Member has no email.' ); 
                $this->assertArrayHasKey( 'groups', $memberInfo, 'Member has no groups.' ); 

                foreach ($memberInfo['groups'] as $group) 
                {
                    $this->assertArrayHasKey( 'id', $group, 'Group has no id.' ); 
                    $this->assertArrayHasKey( 'description', $group, 'Group has no description.' );
                    $this->assertInternalType( 'string', $group['description'], 'Group description is not a string.' );
                }

                $this->assertArrayHasKey( 'status', $memberInfo, 'Member has no status.' ); 
                $this->assertInternalType( 'array', $memberInfo['status'], 'No array by the name of status.' );
            }


            $query = $this->Member->getMemberSummaryAll(true);
            $this->assertArrayHasKey( 'conditions', $query, 'Query has no conditions key.' );
            $this->assertInternalType( 'array', $query['conditions'], 'Query conditions is not an array.' );
        }

        public function testGetMemberSummaryForStatus()
        {
            $this->assertIdentical( count($this->Member->getMemberSummaryForStatus(false, 0)), 0, 'Status 0 has some members.');
            $this->assertIdentical( count($this->Member->getMemberSummaryForStatus(false, 7)), 0, 'Status 7 has some members.');

            $memberList = $this->Member->getMemberSummaryForStatus(false, 1);

            $this->assertIdentical( count($memberList), $this->Member->find('count', array( 'conditions' => array('Member.member_status' => 1) )), 'All members not included.' );
            $this->assertInternalType( 'array', $memberList, 'memberList is not an array.' );

            foreach ($memberList as $memberInfo)
            {
                $this->assertArrayHasKey( 'id', $memberInfo, 'Member has no id.' ); 
                $this->assertGreaterThan( 0, $memberInfo['id'], 'Member id is invalid.' );

                $this->assertArrayHasKey( 'firstname', $memberInfo, 'Member has no firstname.' ); 
                $this->assertArrayHasKey( 'surname', $memberInfo, 'Member has no surname.' ); 
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

            $query = $this->Member->getMemberSummaryForStatus(true, 1);
            $this->assertArrayHasKey( 'conditions', $query, 'Query has no conditions key.' );
            $this->assertInternalType( 'array', $query['conditions'], 'Query conditions is not an array.' );
        }

        public function testGetMemberSummaryForSerchQuery()
        {
            $memberList = $this->Member->getMemberSummaryForSearchQuery(false, 'and');

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

            $query = $this->Member->getMemberSummaryForSearchQuery(true, 'foo');
            $this->assertArrayHasKey( 'conditions', $query, 'Query has no conditions key.' );
            $this->assertInternalType( 'array', $query['conditions'], 'Query conditions is not an array.' );
        }

        public function testGetMemberSummaryForMember()
        {
            $this->assertEqual( $this->Member->getMemberSummaryForMember(0), array(), 'Invalid data handled incorrectly.');
            $this->assertEqual( $this->Member->getMemberSummaryForMember(null), array(), 'Invalid data handled incorrectly.');
            $this->assertEqual( $this->Member->getMemberSummaryForMember(-1), array(), 'Invalid data handled incorrectly.');

            $memberInfo = $this->Member->getMemberSummaryForMember(1);

            $this->assertInternalType( 'array', $memberInfo, 'memberList is not an array.' );

            $this->assertArrayHasKey( 'id', $memberInfo, 'Member has no id.' ); 
            $this->assertGreaterThan( 0, $memberInfo['id'], 'Member id is invalid.' );

            $this->assertArrayHasKey( 'firstname', $memberInfo, 'Member has no firstname.' ); 
            $this->assertArrayHasKey( 'surname', $memberInfo, 'Member has no surname.' ); 
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

        public function testFormatMemberInfoList()
        {
            $this->assertIdentical( count($this->Member->formatMemberInfoList( array(), false )), 0, 'FormatMemberInfo of an empty array did not return and empty array.' );


            $memberList = $this->Member->formatMemberInfoList( $this->Member->find('all'), false );
            foreach ($memberList as $memberInfo)
            {
                $this->assertArrayHasKey( 'id', $memberInfo, 'Member has no id.' ); 
                $this->assertGreaterThan( 0, $memberInfo['id'], 'Member id is invalid.' );

                $this->assertArrayHasKey( 'bestName', $memberInfo, 'Member has no best name.' );

                $this->assertArrayHasKey( 'firstname', $memberInfo, 'Member has no firstname.' ); 
                $this->assertArrayHasKey( 'surname', $memberInfo, 'Member has no surname.' ); 
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

        public function testCreateNewMemberInfo()
        {
            $testEmail = 'foo@bar.co.uk';
            $result = $this->Member->createNewMemberInfo($testEmail);

            $this->assertInternalType( 'array', $result, 'Result is not an array.' );
            $this->assertArrayHasKey( 'Member', $result, 'Result has no Member array.' );

            $this->assertInternalType( 'array', $result['Member'], 'Result Member is not an array.' );

            $this->assertArrayHasKey( 'email', $result['Member'], 'Result Member has no email.' );
            $this->assertIdentical( $result['Member']['email'], $testEmail, 'Email is incorrect.' );

            $this->assertArrayHasKey( 'member_status', $result['Member'], 'Result Member has no member status.' );
            $this->assertIdentical( $result['Member']['member_status'], Status::PROSPECTIVE_MEMBER, 'Member status is incorrect.' );
        }

        public function testGetIdForMember()
        {
            $this->assertIdentical( $this->Member->getStatusForMember(null), 0, 'Null data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(0), 0, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(-1), 0, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array()), 0, 'Empty array was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member')), 0, 'Invalid array was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array())), 0, 'Invalid array was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_id' => 0))), 0, 'Invalid member_id was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_id' => -1))), 0, 'Invalid member_id was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_id' => null))), 0, 'Invalid member_id was not handled correctly.' );

            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_id' => 1))), 5, 'Member id is incorrect.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_id' => 6))), 6, 'Member id is incorrect.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_id' => 7))), 1, 'Member id is incorrect.' );

            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_status' => 1))), 1, 'Member id is incorrect.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_status' => 1, 'member_id' => 7))), 1, 'Member id is incorrect.' );

            $this->assertIdentical( $this->Member->getStatusForMember(1), 5, 'Member id is incorrect.' );
            $this->assertIdentical( $this->Member->getStatusForMember(6), 6, 'Member id is incorrect.' );
            $this->assertIdentical( $this->Member->getStatusForMember(7), 1, 'Member id is incorrect.' );
        }

        public function testGetStatusForMember()
        {
            $this->assertIdentical( $this->Member->getStatusForMember(null), 0, 'Null data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(0), 0, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(-1), 0, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array()), 0, 'Empty array was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member')), 0, 'Invalid array was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array())), 0, 'Invalid array was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_id' => 0))), 0, 'Invalid member_id was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_id' => -1))), 0, 'Invalid member_id was not handled correctly.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_id' => null))), 0, 'Invalid member_id was not handled correctly.' );

            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_id' => 1))), 5, 'Member status is incorrect.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_id' => 6))), 6, 'Member status is incorrect.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_id' => 7))), 1, 'Member status is incorrect.' );

            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_status' => 1))), 1, 'Member status is incorrect.' );
            $this->assertIdentical( $this->Member->getStatusForMember(array('Member' => array('member_status' => 1, 'member_id' => 7))), 1, 'Member status is incorrect.' );

            $this->assertIdentical( $this->Member->getStatusForMember(1), 5, 'Member status is incorrect.' );
            $this->assertIdentical( $this->Member->getStatusForMember(6), 6, 'Member status is incorrect.' );
            $this->assertIdentical( $this->Member->getStatusForMember(7), 1, 'Member status is incorrect.' );
        }

        public function testGetEmailForMember()
        {
            $this->assertIdentical( $this->Member->getEmailForMember(null), null, 'Null data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getEmailForMember(0), null, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getEmailForMember(-1), null, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getEmailForMember(array()), null, 'Empty array was not handled correctly.' );
            $this->assertIdentical( $this->Member->getEmailForMember(array('Member')), null, 'Invalid array was not handled correctly.' );
            $this->assertIdentical( $this->Member->getEmailForMember(array('Member' => array())), null, 'Invalid array was not handled correctly.' );
            $this->assertIdentical( $this->Member->getEmailForMember(array('Member' => array('member_id' => 0))), null, 'Invalid member_id was not handled correctly.' );
            $this->assertIdentical( $this->Member->getEmailForMember(array('Member' => array('member_id' => -1))), null, 'Invalid member_id was not handled correctly.' );
            $this->assertIdentical( $this->Member->getEmailForMember(array('Member' => array('member_id' => null))), null, 'Invalid member_id was not handled correctly.' );

            $this->assertIdentical( $this->Member->getEmailForMember(array('Member' => array('member_id' => 1))), 'm.pryce@example.org', 'Member email is incorrect.' );
            $this->assertIdentical( $this->Member->getEmailForMember(array('Member' => array('member_id' => 6))), 'g.garratte@foobar.org', 'Member email is incorrect.' );
            $this->assertIdentical( $this->Member->getEmailForMember(array('Member' => array('member_id' => 7))), 'CherylLCarignan@teleworm.us', 'Member email is incorrect.' );

            $this->assertIdentical( $this->Member->getEmailForMember(array('Member' => array('email' => 'foo@bar.co.uk'))), 'foo@bar.co.uk', 'Member email is incorrect.' );
            $this->assertIdentical( $this->Member->getEmailForMember(array('Member' => array('email' => 'foo@bar.co.uk', 'member_id' => 7))), 'foo@bar.co.uk', 'Member email is incorrect.' );

            $this->assertIdentical( $this->Member->getEmailForMember(1), 'm.pryce@example.org', 'Member email is incorrect.' );
            $this->assertIdentical( $this->Member->getEmailForMember(6), 'g.garratte@foobar.org', 'Member email is incorrect.' );
            $this->assertIdentical( $this->Member->getEmailForMember(7), 'CherylLCarignan@teleworm.us', 'Member email is incorrect.' );
        }

        public function testGetEmailsForMembersInGroup()
        {
            $this->assertEqual( count($this->Member->getEmailsForMembersInGroup(0)), 0, 'Incorrect return value for non-existant group.' );

            $this->assertEqual( $this->Member->getEmailsForMembersInGroup(Group::CURRENT_MEMBERS), array( 'm.pryce@example.org', 'a.santini@hotmail.com', 'g.viles@gmail.com', 'k.savala@yahoo.co.uk', 'j.easterwood@googlemail.com' ), 'Incorrect return value for Current Member group.' );
        }


        public function testRegisterMember()
        {
            // Test invalid data
            $this->assertIdentical( $this->Member->registerMember(null), null, 'Null data was not handled correctly.' );
            $this->assertIdentical( $this->Member->registerMember('fofe'), null, 'Non-array data was not handled correctly.' );
            $this->assertIdentical( $this->Member->registerMember(array()), null, 'Empty array data was not handled correctly.' );
            $this->assertIdentical( $this->Member->registerMember(array('Group')), null, 'Invalid array data was not handled correctly.' );
            $this->assertIdentical( $this->Member->registerMember(array('Member' => array('email'))), null, 'Invalid array data was not handled correctly.' );
            $this->assertIdentical( $this->Member->registerMember(array('Member' => array('email' => 'not a valid e-mail'))), null, 'Invalid array data was not handled correctly.' );

            // Test with a member that we already know exists, for a member who have the PROSPECTIVE_MEMBER status
            $existingEmail = 'CherylLCarignan@teleworm.us';

            $result = $this->Member->registerMember( array('Member' => array('email' => $existingEmail), 'MailingLists' => array('MailingLists' => array())) );

            $this->assertNotIdentical( $result, null, 'Result should be non-null.' );
            $this->assertInternalType( 'array', $result, 'Result should be an array.' );

            $this->assertArrayHasKey( 'email', $result, 'Result does not have e-mail.' );
            $this->assertIdentical( $result['email'], $existingEmail, 'Result has incorrect e-mail.' );

            $this->assertArrayHasKey( 'createdRecord', $result, 'Result does not have createdRecord.' );
            $this->assertFalse( $result['createdRecord'], 'Result has incorrect createdRecord.' );

            $this->assertArrayHasKey( 'status', $result, 'Result does not have status.' );
            $this->assertIdentical( $result['status'], Status::PROSPECTIVE_MEMBER, 'Result has incorrect status.' );

            $this->assertArrayHasKey( 'memberId', $result, 'Result does not have member id.' );

            $upperCaseEmail = strtoupper($existingEmail);

            $this->assertNotIdentical( $result, null, 'Result should be non-null.' );
            $this->assertInternalType( 'array', $result, 'Result should be an array.' );

            $this->assertArrayHasKey( 'email', $result, 'Result does not have e-mail.' );
            $this->assertIdentical( $result['email'], $existingEmail, 'Result has incorrect e-mail.' );

            $this->assertArrayHasKey( 'createdRecord', $result, 'Result does not have createdRecord.' );
            $this->assertFalse( $result['createdRecord'], 'Result has incorrect createdRecord.' );

            $this->assertArrayHasKey( 'status', $result, 'Result does not have status.' );
            $this->assertIdentical( $result['status'], Status::PROSPECTIVE_MEMBER, 'Result has incorrect status.' );

            $this->assertArrayHasKey( 'memberId', $result, 'Result does not have member id.' );

            // Test with a member that we already know exists, for a member who does not have the PROSPECTIVE_MEMBER status
            $testData = array(
                'm.pryce@example.org' => Status::CURRENT_MEMBER,
                'DorothyDRussell@dayrep.com' => Status::PRE_MEMBER_1,
                'BettyCParis@teleworm.us' => Status::PRE_MEMBER_2,
                'RyanMiles@dayrep.com' => Status::PRE_MEMBER_3,
                'g.garratte@foobar.org' => Status::EX_MEMBER,
            );

            foreach ($testData as $email => $status) 
            {
                $result = $this->Member->registerMember( array('Member' => array('email' => $email)) );

                $this->assertNotIdentical( $result, null, 'Result should be non-null.' );
                $this->assertInternalType( 'array', $result, 'Result should be an array.' );

                $this->assertArrayHasKey( 'email', $result, 'Result does not have e-mail.' );
                $this->assertIdentical( $result['email'], $email, 'Result has incorrect e-mail.' );

                $this->assertArrayHasKey( 'createdRecord', $result, 'Result does not have createdRecord.' );
                $this->assertFalse( $result['createdRecord'], 'Result has incorrect createdRecord.' );

                $this->assertArrayHasKey( 'status', $result, 'Result does not have status.' );
                $this->assertIdentical( $result['status'], $status, 'Result has incorrect status.' );

                $this->assertArrayHasKey( 'memberId', $result, 'Result does not have member id.' );
            }

            $beforeTimestamp = time();

            // Test with a new e-mail
            $newEmail = 'foo@srsaegrttfd.com';
            $result = $this->Member->registerMember( array('Member' => array('email' => $newEmail), 'MailingLists' => array('MailingLists' => array('us8gz1v8rq'))) );

            $afterTimestamp = time();

            $this->assertNotIdentical( $result, null, 'Result should be non-null.' );
            $this->assertInternalType( 'array', $result, 'Result should be an array.' );

            $this->assertArrayHasKey( 'email', $result, 'Result does not have e-mail.' );
            $this->assertEqual( $result['email'], $newEmail, 'Result has incorrect e-mail.' );

            $this->assertArrayHasKey( 'createdRecord', $result, 'Result does not have createdRecord.' );
            $this->assertTrue( $result['createdRecord'], 'Result has incorrect createdRecord.' );

            $this->assertArrayHasKey( 'status', $result, 'Result does not have status.' );
            $this->assertEqual( $result['status'], Status::PROSPECTIVE_MEMBER, 'Result has incorrect status.' );

            $this->assertArrayHasKey( 'memberId', $result, 'Result does not have member id.' );

            $this->assertArrayHasKey( 'mailingLists', $result, 'Result does not have mailingLists.' );
            $this->assertEqual( $result['mailingLists'], array(array('list' => 'us8gz1v8rq', 'action' => 'subscribe', 'successful' => true, 'name' => 'Nottingham Hackspace Announcements')), 'Result has incorrect mailingLists.' );


            $record = $this->Member->findByEmail($newEmail);

            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );
            $this->assertArrayHasKey( 'Member', $record, 'Record does not have member key.' );
            $this->assertArrayHasKey( 'member_id', $record['Member'], 'Record Member does not have member_id key.' );
            $this->assertArrayHasKey( 'email', $record['Member'], 'Record Member does not have email key.' );
            $this->assertIdentical( $record['Member']['email'], $newEmail, 'Record email is incorrect.' );
            $this->assertArrayHasKey( 'member_status', $record['Member'], 'Record Member does not have member_status key.' );
            $this->assertEqual( $record['Member']['member_status'], Status::PROSPECTIVE_MEMBER, 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['member_id'], $result['memberId'], 'Result has incorrect member id.' );
            
            $this->assertArrayHasKey( 'StatusUpdate', $record, 'Record does not have status update key.' );
            $this->assertEqual( count($record['StatusUpdate']), 1, 'Record has incorrect number of status updates.' );
            $this->assertEqual( count($record['StatusUpdate']), 1, 'Record has incorrect number of status updates.' );
            $this->assertArrayHasKey( 'id', $record['StatusUpdate'][0], 'Record does not have id key.' );
            $this->assertArrayHasKey( 'member_id', $record['StatusUpdate'][0], 'Record does not have member_id key.' );
            $this->assertArrayHasKey( 'admin_id', $record['StatusUpdate'][0], 'Record does not have admin_id key.' );
            $this->assertArrayHasKey( 'old_status', $record['StatusUpdate'][0], 'Record does not have old_status key.' );
            $this->assertArrayHasKey( 'new_status', $record['StatusUpdate'][0], 'Record does not have new_status key.' );
            $this->assertArrayHasKey( 'timestamp', $record['StatusUpdate'][0], 'Record does not have timestamp key.' );
            $this->assertEqual( $record['StatusUpdate'][0]['id'], 4, 'Record has incorrect id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['member_id'], 15, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['admin_id'], 15, 'Record has incorrect admin_id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['old_status'], 0, 'Record has incorrect old_status.' );
            $this->assertEqual( $record['StatusUpdate'][0]['new_status'], Status::PROSPECTIVE_MEMBER, 'Record has incorrect new_status.' );
            $this->assertGreaterThanOrEqual( $beforeTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp.' );
            $this->assertLessThanOrEqual( $afterTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp.' );
        }

        public function testRegisterMemberOnlySavesIdEmailMemberStatus()
        {
            $newEmail = 'foo@srsaegrttfd.com';

            $data = array(
                'Member' => array(
                    'member_id' => 4,
                    'firstname' => 'Kelly',
                    'surname' => 'Savala',
                    'email' => $newEmail,
                    'join_date' => '2010-09-22',
                    'handle' => 'bildestonelectrician',
                    'unlock_text' => 'Hey Kelly',
                    'balance' => -5649,
                    'credit_limit' => 5000,
                    'member_status' => 5,
                    'username' => 'huskycolossus',
                    'account_id' => 4,
                    'address_1' => '8 Elm Close',
                    'address_2' => 'Tetsworth',
                    'address_city' => 'Thame',
                    'address_postcode' => 'OX9 7AP',
                    'contact_number' => '079 0644 8720'
                )
            );

            $result = $this->Member->registerMember( $data );

            $this->assertNotIdentical( $result, null, 'Result should be non-null.' );
            $this->assertInternalType( 'array', $result, 'Result should be an array.' );

            $this->assertArrayHasKey( 'email', $result, 'Result does not have e-mail.' );
            $this->assertEqual( $result['email'], $newEmail, 'Result has incorrect e-mail.' );

            $this->assertArrayHasKey( 'createdRecord', $result, 'Result does not have createdRecord.' );
            $this->assertTrue( $result['createdRecord'], 'Result has incorrect createdRecord.' );

            $this->assertArrayHasKey( 'status', $result, 'Result does not have status.' );
            $this->assertEqual( $result['status'], Status::PROSPECTIVE_MEMBER, 'Result has incorrect status.' );

            $this->assertArrayHasKey( 'memberId', $result, 'Result does not have member id.' );


            $record = $this->Member->findByEmail($newEmail);

            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );

            $this->assertEqual( $record['Member']['firstname'], null, 'Record has incorrect firstname.' );
            $this->assertEqual( $record['Member']['surname'], null, 'Record has incorrect surname.' );
            $this->assertEqual( $record['Member']['email'], 'foo@srsaegrttfd.com', 'Record has incorrect email.' );
            $this->assertEqual( $record['Member']['join_date'], '0000-00-00', 'Record has incorrect join date.' );
            $this->assertEqual( $record['Member']['unlock_text'], null, 'Record has incorrect unlock text.' );
            $this->assertEqual( $record['Member']['balance'], 0, 'Record has incorrect balance.' );
            $this->assertEqual( $record['Member']['credit_limit'], 0, 'Record has incorrect credit limit.' );
            $this->assertEqual( $record['Member']['member_status'], Status::PROSPECTIVE_MEMBER, 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['username'], null, 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['account_id'], null, 'Record has incorrect account id.' );
            $this->assertEqual( $record['Member']['address_1'], null, 'Record has incorrect address 1.' );
            $this->assertEqual( $record['Member']['address_2'], null, 'Record has incorrect address 2.' );
            $this->assertEqual( $record['Member']['address_city'], null, 'Record has incorrect address city.' );
            $this->assertEqual( $record['Member']['address_postcode'], null, 'Record has incorrect address postcode.' );
            $this->assertEqual( $record['Member']['contact_number'], null, 'Record has incorrect contact number.' );
        }

        public function testSetupLoginInvalidInput()
        {
            $this->assertFalse( $this->Member->setupLogin(null, null), 'Null data was not handled correctly.' );
            $this->assertFalse( $this->Member->setupLogin(-1, array()), 'Invalid id was not handled correctly.' );
            $this->assertFalse( $this->Member->setupLogin(0, array()), 'Invalid id was not handled correctly.' );
            $this->assertFalse( $this->Member->setupLogin(2076, array()), 'Invalid id was not handled correctly.' );

            $this->assertFalse( $this->Member->setupLogin(7, 'ferfe'), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->setupLogin(7, null), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->setupLogin(7, array()), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->setupLogin(7, array('Member')), 'Invalid data was not handled correctly.' );


            $data = array(
                'Member' => array(
                    'firstname' => 'Foo',
                    'surname' => 'Barson',
                    'username' => 'fubbby',
                    'email' => 'not@correct.com',
                    'password' => 'hunter2',
                    'password_confirm' => 'hunter2',
                )
            );

            $this->assertFalse( $this->Member->setupLogin(7, $data), 'Invalid email was not handled correctly.' );
        }

        public function testSetupLoginThorws()
        {
            $data = array(
                1 => array(
                    'Member' => array(
                        'firstname' => 'Foo',
                        'surname' => 'Barson',
                        'username' => 'fubbby',
                        'email' => 'm.pryce@example.org',
                        'password' => 'hunter2',
                        'password_confirm' => 'hunter2',
                    )
                ),
                6 => array(
                    'Member' => array(
                        'firstname' => 'Foo',
                        'surname' => 'Barson',
                        'username' => 'fubbby',
                        'email' => 'g.garratte@foobar.org',
                        'password' => 'hunter2',
                        'password_confirm' => 'hunter2',
                    )
                ),
                9 => array(
                    'Member' => array(
                        'firstname' => 'Foo',
                        'surname' => 'Barson',
                        'username' => 'fubbby',
                        'email' => 'DorothyDRussell@dayrep.com',
                        'password' => 'hunter2',
                        'password_confirm' => 'hunter2',
                    )
                ),
                10 => array(
                    'Member' => array(
                        'firstname' => 'Foo',
                        'surname' => 'Barson',
                        'username' => 'fubbby',
                        'email' => 'HugoJLorenz@dayrep.com',
                        'password' => 'hunter2',
                        'password_confirm' => 'hunter2',
                    )
                ),
                11 => array(
                    'Member' => array(
                        'firstname' => 'Foo',
                        'surname' => 'Barson',
                        'username' => 'fubbby',
                        'email' => 'BettyCParis@teleworm.us',
                        'password' => 'hunter2',
                        'password_confirm' => 'hunter2',
                    )
                ),
                12 => array(
                    'Member' => array(
                        'firstname' => 'Foo',
                        'surname' => 'Barson',
                        'username' => 'fubbby',
                        'email' => 'RoyJForsman@teleworm.us',
                        'password' => 'hunter2',
                        'password_confirm' => 'hunter2',
                    )
                ),
                13 => array(
                    'Member' => array(
                        'firstname' => 'Foo',
                        'surname' => 'Barson',
                        'username' => 'fubbby',
                        'email' => 'RyanMiles@dayrep.com',
                        'password' => 'hunter2',
                        'password_confirm' => 'hunter2',
                    )
                ),
                14 => array(
                    'Member' => array(
                        'firstname' => 'Foo',
                        'surname' => 'Barson',
                        'username' => 'fubbby',
                        'email' => 'EvanAtkinson@teleworm.us',
                        'password' => 'hunter2',
                        'password_confirm' => 'hunter2',
                    )
                ),
            );

            foreach ($data as $memberId => $memberData)
            {
                $threw = false;
                try
                {
                    $this->Member->setupLogin($memberId, $memberData);
                }
                catch(InvalidStatusException $e)
                {
                    $threw = true;
                }
                
                $this->assertTrue( $threw, 'SetupLogin for member id ' . $memberId . ' failed to throw.' );                
            }
        }

        public function testSetupLoginValidData()
        {
            $data = array(
                'Member' => array(
                    'firstname' => 'Foo',
                    'surname' => 'Barson',
                    'username' => 'fubbby',
                    'email' => 'CherylLCarignan@teleworm.us',
                    'password' => 'hunter2',
                    'password_confirm' => 'hunter2',
                )
            );

            $beforeTimestamp = time();
            $this->assertTrue( $this->Member->setupLogin(7, $data), 'Valid data was not handled correctly.' );
            $afterTimestamp = time();

            $record = $this->Member->findByMemberId(7);

            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );
            $this->assertArrayHasKey( 'Member', $record, 'Record does not have member key.' );
            $this->assertArrayHasKey( 'member_id', $record['Member'], 'Record Member does not have member_id key.' );
            $this->assertArrayHasKey( 'email', $record['Member'], 'Record Member does not have email key.' );
            $this->assertIdentical( $record['Member']['email'], 'CherylLCarignan@teleworm.us', 'Record email is incorrect.' );
            $this->assertArrayHasKey( 'member_status', $record['Member'], 'Record Member does not have member_status key.' );
            $this->assertEqual( $record['Member']['member_status'], Status::PRE_MEMBER_1, 'Record has incorrect status.' );
            $this->assertArrayHasKey( 'username', $record['Member'], 'Record Member does not have username key.' );
            $this->assertEqual( $record['Member']['username'], 'fubbby', 'Record has incorrect username.' );
            $this->assertArrayHasKey( 'firstname', $record['Member'], 'Record Member does not firstname name key.' );
            $this->assertEqual( $record['Member']['firstname'], 'Foo', 'Record has incorrect firstname.' );
            $this->assertArrayHasKey( 'surname', $record['Member'], 'Record Member does not surname name key.' );
            $this->assertEqual( $record['Member']['surname'], 'Barson', 'Record has incorrect surname.' );

            $this->assertArrayHasKey( 'StatusUpdate', $record, 'Record does not have status update key.' );
            $this->assertEqual( count($record['StatusUpdate']), 1, 'Record has incorrect number of status updates.' );
            $this->assertArrayHasKey( 'id', $record['StatusUpdate'][0], 'Record does not have id key.' );
            $this->assertArrayHasKey( 'member_id', $record['StatusUpdate'][0], 'Record does not have member_id key.' );
            $this->assertArrayHasKey( 'admin_id', $record['StatusUpdate'][0], 'Record does not have admin_id key.' );
            $this->assertArrayHasKey( 'old_status', $record['StatusUpdate'][0], 'Record does not have old_status key.' );
            $this->assertArrayHasKey( 'new_status', $record['StatusUpdate'][0], 'Record does not have new_status key.' );
            $this->assertArrayHasKey( 'timestamp', $record['StatusUpdate'][0], 'Record does not have timestamp key.' );
            $this->assertEqual( $record['StatusUpdate'][0]['id'], 4, 'Record has incorrect id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['member_id'], 7, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['admin_id'], 7, 'Record has incorrect admin_id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['old_status'], Status::PROSPECTIVE_MEMBER, 'Record has incorrect old_status.' );
            $this->assertEqual( $record['StatusUpdate'][0]['new_status'], Status::PRE_MEMBER_1, 'Record has incorrect new_status.' );
            $this->assertGreaterThanOrEqual( $beforeTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp.' );
            $this->assertLessThanOrEqual( $afterTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp.' );
        }

        public function testSetupLoginOnlySavesNameUsernameHandleMemberStatus()
        {
            $data = array(
                'Member' => array(
                    'member_id' => 9,
                    'firstname' => 'Foo',
                    'surname' => 'Barson',
                    'email' => 'CherylLCarignan@teleworm.us',
                    'join_date' => '2010-09-22',
                    'handle' => 'bildestonelectrician',
                    'unlock_text' => 'Hey Kelly',
                    'balance' => -5649,
                    'credit_limit' => 5000,
                    'member_status' => 5,
                    'username' => 'fubbby',
                    'account_id' => 4,
                    'address_1' => '8 Elm Close',
                    'address_2' => 'Tetsworth',
                    'address_city' => 'Thame',
                    'address_postcode' => 'OX9 7AP',
                    'contact_number' => '079 0644 8720',
                    'password' => 'hunter2',
                    'password_confirm' => 'hunter2',
                )
            );

            $this->assertTrue( $this->Member->setupLogin( 7, $data ), 'Extra data not handled correctly.' );

            $record = $this->Member->findByMemberId(7);

            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );

            $this->assertEqual( $record['Member']['member_id'], 7, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['Member']['firstname'], 'Foo', 'Record has incorrect firstname.' );
            $this->assertEqual( $record['Member']['surname'], 'Barson', 'Record has incorrect surname.' );
            $this->assertEqual( $record['Member']['email'], 'CherylLCarignan@teleworm.us', 'Record has incorrect email.' );
            $this->assertEqual( $record['Member']['join_date'], '0000-00-00', 'Record has incorrect join date.' );
            $this->assertEqual( $record['Member']['unlock_text'], null, 'Record has incorrect unlock text.' );
            $this->assertEqual( $record['Member']['balance'], 0, 'Record has incorrect balance.' );
            $this->assertEqual( $record['Member']['credit_limit'], 0, 'Record has incorrect credit limit.' );
            $this->assertEqual( $record['Member']['member_status'], Status::PRE_MEMBER_1, 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['username'], 'fubbby', 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['account_id'], null, 'Record has incorrect account id.' );
            $this->assertEqual( $record['Member']['address_1'], null, 'Record has incorrect address 1.' );
            $this->assertEqual( $record['Member']['address_2'], null, 'Record has incorrect address 2.' );
            $this->assertEqual( $record['Member']['address_city'], null, 'Record has incorrect address city.' );
            $this->assertEqual( $record['Member']['address_postcode'], null, 'Record has incorrect address postcode.' );
            $this->assertEqual( $record['Member']['contact_number'], null, 'Record has incorrect contact number.' );
        }

        public function testSetupDetailsInvalidData()
        {
            $this->assertFalse( $this->Member->setupDetails(null, null), 'Null data was not handled correctly.' );
            $this->assertFalse( $this->Member->setupDetails(-1, array()), 'Invalid id was not handled correctly.' );
            $this->assertFalse( $this->Member->setupDetails(0, array()), 'Invalid id was not handled correctly.' );
            $this->assertFalse( $this->Member->setupDetails(2076, array()), 'Invalid id was not handled correctly.' );

            $this->assertFalse( $this->Member->setupDetails(9, 'ferfe'), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->setupDetails(9, null), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->setupDetails(9, array()), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->setupDetails(9, array('Member')), 'Invalid data was not handled correctly.' );
        }

        public function testSetupDetailsThrows()
        {
            $data = array(
                'Member' => array(
                    'address_1' => '27A The Mews',
                    'address_2' => 'Test Road',
                    'address_city' => 'Testington',
                    'address_postcode' => 'DE22 7BU',
                    'contact_number' => '07973 235786',
                )
            );

            $testMemberIds = array( 1, 2, 3, 4, 5, 6, 7, 8, 11, 12, 13, 14 );
            foreach ($testMemberIds as $memberId) 
            {
                $threw = false;
                try
                {
                    $this->Member->setupDetails($memberId, $data);
                }
                catch(InvalidStatusException $e)
                {
                    $threw = true;
                }
                
                $this->assertTrue( $threw, 'SetupDetails for member id ' . $memberId . ' failed to throw.' );
            }
        }

        public function testSetupDetailsValidData()
        {
            $data = array(
                9 => array(
                    'Member' => array(
                        'address_1' => '27A The Mews',
                        'address_2' => 'Test Road',
                        'address_city' => 'Testington',
                        'address_postcode' => 'DE22 7BU',
                        'contact_number' => '07973 235786',
                    )
                ),
                10 => array(
                    'Member' => array(
                        'address_1' => '323 Foo Street',
                        'address_2' => '',
                        'address_city' => 'Wibble',
                        'address_postcode' => 'WI65 2GH',
                        'contact_number' => '07956426486',
                    )
                ),
            );

            $statusUpdateCount = 4;
            foreach ($data as $memberId => $memberData) 
            {
                $beforeTimestamp = time();
                $this->assertTrue( $this->Member->setupDetails($memberId, $memberData), 'Valid data was not handled correctly.' );
                $afterTimestamp = time();

                $record = $this->Member->findByMemberId($memberId);

                $this->assertNotIdentical( $record, null, 'Could not find record for member id ' . $memberId .'.' );
                $this->assertInternalType( 'array', $record, 'Could not find record for member id ' . $memberId .'.' );
                $this->assertArrayHasKey( 'Member', $record, 'Record does not have member key for member id ' . $memberId .'.' );
                $this->assertArrayHasKey( 'member_id', $record['Member'], 'Record Member does not have member_id key for member id ' . $memberId .'.' );
                $this->assertArrayHasKey( 'address_1', $record['Member'], 'Record Member does not have address_1 key for member id ' . $memberId .'.' );
                $this->assertIdentical( $record['Member']['address_1'], $memberData['Member']['address_1'], 'Record address_1 is incorrect for member id ' . $memberId .'.' );
                $this->assertArrayHasKey( 'member_status', $record['Member'], 'Record Member does not have member_status key for member id ' . $memberId .'.' );
                $this->assertEqual( $record['Member']['member_status'], Status::PRE_MEMBER_2, 'Record has incorrect status for member id ' . $memberId .'.' );
                $this->assertArrayHasKey( 'address_2', $record['Member'], 'Record Member does not have address_2 key for member id ' . $memberId .'.' );
                $this->assertEqual( $record['Member']['address_2'], $memberData['Member']['address_2'], 'Record has incorrect address_2 for member id ' . $memberId .'.' );
                $this->assertArrayHasKey( 'address_city', $record['Member'], 'Record Member does not have address_city key for member id ' . $memberId .'.' );
                $this->assertEqual( $record['Member']['address_city'], $memberData['Member']['address_city'], 'Record has incorrect address_city for member id ' . $memberId .'.' );
                $this->assertArrayHasKey( 'address_postcode', $record['Member'], 'Record Member does not have address_postcode key for member id ' . $memberId .'.' );
                $this->assertEqual( $record['Member']['address_postcode'], $memberData['Member']['address_postcode'], 'Record has incorrect address_postcode for member id ' . $memberId .'.' );
                $this->assertArrayHasKey( 'contact_number', $record['Member'], 'Record Member does not have contact_number key for member id ' . $memberId .'.' );
                $this->assertEqual( $record['Member']['contact_number'], $memberData['Member']['contact_number'], 'Record has incorrect contact_number for member id ' . $memberId .'.' );


                $this->assertArrayHasKey( 'StatusUpdate', $record, 'Record does not have status update key ' . $memberId .'.' );
                $this->assertArrayHasKey( 'id', $record['StatusUpdate'][0], 'Record does not have id key ' . $memberId .'.' );
                $this->assertArrayHasKey( 'member_id', $record['StatusUpdate'][0], 'Record does not have member_id key ' . $memberId .'.' );
                $this->assertArrayHasKey( 'admin_id', $record['StatusUpdate'][0], 'Record does not have admin_id key ' . $memberId .'.' );
                $this->assertArrayHasKey( 'old_status', $record['StatusUpdate'][0], 'Record does not have old_status key ' . $memberId .'.' );
                $this->assertArrayHasKey( 'new_status', $record['StatusUpdate'][0], 'Record does not have new_status key ' . $memberId .'.' );
                $this->assertArrayHasKey( 'timestamp', $record['StatusUpdate'][0], 'Record does not have timestamp key ' . $memberId .'.' );
                $this->assertEqual( $record['StatusUpdate'][0]['id'], $statusUpdateCount, 'Record has incorrect id ' . $memberId .'.' );
                $this->assertEqual( $record['StatusUpdate'][0]['member_id'], $memberId, 'Record has incorrect member_id ' . $memberId .'.' );
                $this->assertEqual( $record['StatusUpdate'][0]['admin_id'], $memberId, 'Record has incorrect admin_id ' . $memberId .'.' );
                $this->assertEqual( $record['StatusUpdate'][0]['old_status'], Status::PRE_MEMBER_1, 'Record has incorrect old_status ' . $memberId .'.' );
                $this->assertEqual( $record['StatusUpdate'][0]['new_status'], Status::PRE_MEMBER_2, 'Record has incorrect new_status ' . $memberId .'.' );
                $this->assertGreaterThanOrEqual( $beforeTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp ' . $memberId .'.' );
                $this->assertLessThanOrEqual( $afterTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp ' . $memberId .'.' );

                $statusUpdateCount++;
            }
        }

        public function testSetupDetailsOnlySavesAddressContactInfoMemberStatus()
        {
            $data = array(
                'Member' => array(
                    'member_id' => 14,
                    'firstname' => 'Foo',
                    'surname' => 'Barson',
                    'email' => 'CherylLCarignan@teleworm.us',
                    'join_date' => '2010-09-22',
                    'handle' => 'bildestonelectrician',
                    'unlock_text' => 'Hey Kelly',
                    'balance' => -5649,
                    'credit_limit' => 5000,
                    'member_status' => 5,
                    'username' => 'fubbby',
                    'account_id' => 4,
                    'address_1' => '8 Elm Close',
                    'address_2' => 'Tetsworth',
                    'address_city' => 'Thame',
                    'address_postcode' => 'OX9 7AP',
                    'contact_number' => '079 0644 8720',
                )
            );

            $memberId = 9;

            $this->assertTrue( $this->Member->setupDetails($memberId, $data), 'Valid data was not handled correctly.' );

            $record = $this->Member->findByMemberId($memberId);

            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );

            $this->assertEqual( $record['Member']['member_id'], 9, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['Member']['firstname'], 'Dorothy', 'Record has incorrect firstname.' );
            $this->assertEqual( $record['Member']['surname'], 'Russell', 'Record has incorrect surname.' );
            $this->assertEqual( $record['Member']['email'], 'DorothyDRussell@dayrep.com', 'Record has incorrect email.' );
            $this->assertEqual( $record['Member']['join_date'], '0000-00-00', 'Record has incorrect join date.' );
            $this->assertEqual( $record['Member']['unlock_text'], null, 'Record has incorrect unlock text.' );
            $this->assertEqual( $record['Member']['balance'], 0, 'Record has incorrect balance.' );
            $this->assertEqual( $record['Member']['credit_limit'], 0, 'Record has incorrect credit limit.' );
            $this->assertEqual( $record['Member']['member_status'], Status::PRE_MEMBER_2, 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['username'], 'Warang29', 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['account_id'], null, 'Record has incorrect account id.' );
            $this->assertEqual( $record['Member']['address_1'], '8 Elm Close', 'Record has incorrect address 1.' );
            $this->assertEqual( $record['Member']['address_2'], 'Tetsworth', 'Record has incorrect address 2.' );
            $this->assertEqual( $record['Member']['address_city'], 'Thame', 'Record has incorrect address city.' );
            $this->assertEqual( $record['Member']['address_postcode'], 'OX9 7AP', 'Record has incorrect address postcode.' );
            $this->assertEqual( $record['Member']['contact_number'], '079 0644 8720', 'Record has incorrect contact number.' );
        }

        public function testRejectDetailsInvalidData()
        {
            $this->assertFalse( $this->Member->rejectDetails(null, null, null), 'Null data was not handled correctly.' );
            $this->assertFalse( $this->Member->rejectDetails(-1, array(), 1), 'Invalid id was not handled correctly.' );
            $this->assertFalse( $this->Member->rejectDetails(0, array(), 1), 'Invalid id was not handled correctly.' );
            $this->assertFalse( $this->Member->rejectDetails(2076, array(), 1), 'Invalid id was not handled correctly.' );

            $this->assertFalse( $this->Member->rejectDetails(11, 'ferfe', 1), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->rejectDetails(11, null, 1), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->rejectDetails(11, array(), 1), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->rejectDetails(11, array('Member'), 1), 'Invalid data was not handled correctly.' );
        }

        public function testRejectDetailsThrows()
        {
            $data = array(
                'MemberEmail' => array(
                    'subject' => 'Member details rejected',
                    'message' => 'Your member details contain too much foo.',
                )
            );

            $testMemberIds = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 13, 14 );
            foreach ($testMemberIds as $memberId) 
            {
                $threw = false;
                try
                {
                    $this->Member->rejectDetails($memberId, $data, 5);
                }
                catch(InvalidStatusException $e)
                {
                    $threw = true;
                }
                
                $this->assertTrue( $threw, 'RejectDetails for member id ' . $memberId . ' failed to throw.' );
            }
        }

        public function testRejectDetailsValidData()
        {
            $data = array(
                11 => array(
                    'MemberEmail' => array(
                        'subject' => 'Member details rejected',
                        'message' => 'Your member details contain too much foo.',
                    ),
                ),
                12 => array(
                    'MemberEmail' => array(
                        'subject' => 'Member details rejected',
                        'message' => 'Your member details contain too much foo.',
                    ),
                ),
            );

            $statusUpdateCount = 4;
            foreach ($data as $memberId => $memberData) 
            {
                $beforeTimestamp = time();
                $this->assertTrue( $this->Member->rejectDetails($memberId, $memberData, 5), 'Valid data was not handled correctly.' );
                $afterTimestamp = time();

                $record = $this->Member->findByMemberId($memberId);

                $this->assertNotIdentical( $record, null, 'Could not find record.' );
                $this->assertInternalType( 'array', $record, 'Could not find record.' );
                $this->assertArrayHasKey( 'Member', $record, 'Record does not have member key.' );
                $this->assertArrayHasKey( 'member_id', $record['Member'], 'Record Member does not have member_id key.' );
                $this->assertArrayHasKey( 'member_status', $record['Member'], 'Record Member does not have member_status key.' );
                $this->assertEqual( $record['Member']['member_status'], Status::PRE_MEMBER_1, 'Record has incorrect status.' );

                $this->assertArrayHasKey( 'StatusUpdate', $record, 'Record does not have status update key.' );
                $this->assertEqual( count($record['StatusUpdate']), 1, 'Record has incorrect number of status updates.' );
                $this->assertArrayHasKey( 'id', $record['StatusUpdate'][0], 'Record does not have id key.' );
                $this->assertArrayHasKey( 'member_id', $record['StatusUpdate'][0], 'Record does not have member_id key.' );
                $this->assertArrayHasKey( 'admin_id', $record['StatusUpdate'][0], 'Record does not have admin_id key.' );
                $this->assertArrayHasKey( 'old_status', $record['StatusUpdate'][0], 'Record does not have old_status key.' );
                $this->assertArrayHasKey( 'new_status', $record['StatusUpdate'][0], 'Record does not have new_status key.' );
                $this->assertArrayHasKey( 'timestamp', $record['StatusUpdate'][0], 'Record does not have timestamp key.' );
                $this->assertEqual( $record['StatusUpdate'][0]['id'], $statusUpdateCount, 'Record has incorrect id.' );
                $this->assertEqual( $record['StatusUpdate'][0]['member_id'], $memberId, 'Record has incorrect member_id.' );
                $this->assertEqual( $record['StatusUpdate'][0]['admin_id'], 5, 'Record has incorrect admin_id.' );
                $this->assertEqual( $record['StatusUpdate'][0]['old_status'], Status::PRE_MEMBER_2, 'Record has incorrect old_status.' );
                $this->assertEqual( $record['StatusUpdate'][0]['new_status'], Status::PRE_MEMBER_1, 'Record has incorrect new_status.' );
                $this->assertGreaterThanOrEqual( $beforeTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp.' );
                $this->assertLessThanOrEqual( $afterTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp.' );

                $statusUpdateCount++;
            }
        }

        public function testRejectDetailsOnlySavesMemberStatus()
        {
            $data = array(
                'Member' => array(
                    'member_id' => 14,
                    'firstname' => 'Foo',
                    'surname' => 'Barson',
                    'email' => 'CherylLCarignan@teleworm.us',
                    'join_date' => '2010-09-22',
                    'handle' => 'bildestonelectrician',
                    'unlock_text' => 'Hey Kelly',
                    'balance' => -5649,
                    'credit_limit' => 5000,
                    'member_status' => 5,
                    'username' => 'fubbby',
                    'account_id' => 4,
                    'address_1' => '8 Elm Close',
                    'address_2' => 'Tetsworth',
                    'address_city' => 'Thame',
                    'address_postcode' => 'OX9 7AP',
                    'contact_number' => '079 0644 8720',
                    'password' => 'hunter2',
                    'password_confirm' => 'hunter2',
                ),
                'MemberEmail' => array(
                    'subject' => 'fwafgeawfa',
                    'message' => 'Lorem ipsum sit dom ammet...',
                ),
            );

            $memberId = 11;

            $this->assertTrue( $this->Member->rejectDetails($memberId, $data, 5), 'Valid data was not handled correctly.' );

            $record = $this->Member->findByMemberId($memberId);

            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );

            $this->assertEqual( $record['Member']['member_id'], 11, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['Member']['firstname'], 'Betty', 'Record has incorrect firstname.' );
            $this->assertEqual( $record['Member']['surname'], 'Paris', 'Record has incorrect surname.' );
            $this->assertEqual( $record['Member']['email'], 'BettyCParis@teleworm.us', 'Record has incorrect email.' );
            $this->assertEqual( $record['Member']['join_date'], '0000-00-00', 'Record has incorrect join date.' );
            $this->assertEqual( $record['Member']['unlock_text'], null, 'Record has incorrect unlock text.' );
            $this->assertEqual( $record['Member']['balance'], 0, 'Record has incorrect balance.' );
            $this->assertEqual( $record['Member']['credit_limit'], 0, 'Record has incorrect credit limit.' );
            $this->assertEqual( $record['Member']['member_status'], Status::PRE_MEMBER_1, 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['username'], 'Beltonstlend51', 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['account_id'], null, 'Record has incorrect account id.' );
            $this->assertEqual( $record['Member']['address_1'], '10 Hampton Court Rd', 'Record has incorrect address 1.' );
            $this->assertEqual( $record['Member']['address_2'], null, 'Record has incorrect address 2.' );
            $this->assertEqual( $record['Member']['address_city'], 'Spelsbury', 'Record has incorrect address city.' );
            $this->assertEqual( $record['Member']['address_postcode'], 'OX7 2US', 'Record has incorrect address postcode.' );
            $this->assertEqual( $record['Member']['contact_number'], '079 0572 8737', 'Record has incorrect contact number.' );
        }

        public function testAcceptDetailsInvalidData()
        {
            $this->assertIdentical( $this->Member->acceptDetails(null, null, null), null, 'Null data was not handled correctly.' );
            $this->assertIdentical( $this->Member->acceptDetails(-1, array(), 1), null, 'Invalid id was not handled correctly.' );
            $this->assertIdentical( $this->Member->acceptDetails(0, array(), 1), null, 'Invalid id was not handled correctly.' );
            $this->assertIdentical( $this->Member->acceptDetails(2076, array(), 1), null, 'Invalid id was not handled correctly.' );

            $this->assertIdentical( $this->Member->acceptDetails(11, 'ferfe', 1), null, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->acceptDetails(11, null, 1), null, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->acceptDetails(11, array(), 1), null, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->acceptDetails(11, array('Account'), 1), null, 'Invalid data was not handled correctly.' );

            $this->assertIdentical( $this->Member->acceptDetails(11, array('Account' => array('account_id' => '3003')), 1), null, 'Invalid data was not handled correctly.' );
        }

        public function testAcceptDetailsThrows()
        {
            $data = array(
                'Account' => array(
                    'account_id' => '-1',
                )
            );

            $testMemberIds = array( 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 13, 14 );
            foreach ($testMemberIds as $memberId) 
            {
                $threw = false;
                try
                {
                    $this->Member->acceptDetails($memberId, $data, 5);
                }
                catch(InvalidStatusException $e)
                {
                    $threw = true;
                }
                
                $this->assertTrue( $threw, 'AcceptDetails for member id ' . $memberId . ' failed to throw.' );
            }
        }

        public function testAcceptDetailsNewAccount()
        {
            $data = array(
                'Account' => array(
                    'account_id' => '-1'
                ),
            );

            $beforeTimestamp = time();
            $return = $this->Member->acceptDetails( 11, $data, 5 );
            $afterTimestamp = time();

            $this->assertNotEqual( $return, null, 'Failed creating a new account.' );
            $this->assertInternalType( 'array', $return, 'Failed creating new account.' );
            $this->assertArrayHasKey( 'firstname', $return, 'Return array is invalid.' );
            $this->assertEqual( $return['firstname'], 'Betty', 'Return array has invalid firstname.' );
            $this->assertArrayHasKey( 'surname', $return, 'Return array is invalid.' );
            $this->assertEqual( $return['surname'], 'Paris', 'Return array has invalid surname.' );
            $this->assertArrayHasKey( 'email', $return, 'Return array is invalid.' );
            $this->assertEqual( $return['email'], 'BettyCParis@teleworm.us', 'Return array has invalid email.' );
            $this->assertArrayHasKey( 'paymentRef', $return, 'Return array is invalid.' );

            $record = $this->Member->findByMemberId(11);
            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );
            $this->assertArrayHasKey( 'Member', $record, 'Record does not have member key.' );
            $this->assertArrayHasKey( 'member_id', $record['Member'], 'Record Member does not have member_id key.' );
            $this->assertArrayHasKey( 'member_status', $record['Member'], 'Record Member does not have member_status key.' );
            $this->assertEqual( $record['Member']['member_status'], Status::PRE_MEMBER_3, 'Record has incorrect status.' );
            $this->assertArrayHasKey( 'account_id', $record['Member'], 'Record Member does not have account_id key.' );
            $this->assertEqual( $record['Member']['account_id'], 9, 'Record has incorrect account_id.' );

            $this->assertArrayHasKey( 'StatusUpdate', $record, 'Record does not have status update key.' );
            $this->assertEqual( count($record['StatusUpdate']), 1, 'Record has incorrect number of status updates.' );
            $this->assertArrayHasKey( 'id', $record['StatusUpdate'][0], 'Record does not have id key.' );
            $this->assertArrayHasKey( 'member_id', $record['StatusUpdate'][0], 'Record does not have member_id key.' );
            $this->assertArrayHasKey( 'admin_id', $record['StatusUpdate'][0], 'Record does not have admin_id key.' );
            $this->assertArrayHasKey( 'old_status', $record['StatusUpdate'][0], 'Record does not have old_status key.' );
            $this->assertArrayHasKey( 'new_status', $record['StatusUpdate'][0], 'Record does not have new_status key.' );
            $this->assertArrayHasKey( 'timestamp', $record['StatusUpdate'][0], 'Record does not have timestamp key.' );
            $this->assertEqual( $record['StatusUpdate'][0]['id'], 4, 'Record has incorrect id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['member_id'], 11, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['admin_id'], 5, 'Record has incorrect admin_id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['old_status'], Status::PRE_MEMBER_2, 'Record has incorrect old_status.' );
            $this->assertEqual( $record['StatusUpdate'][0]['new_status'], Status::PRE_MEMBER_3, 'Record has incorrect new_status.' );
            $this->assertGreaterThanOrEqual( $beforeTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp.' );
            $this->assertLessThanOrEqual( $afterTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp.' );
        }

        public function testAcceptDetailsExistingAccountWaitingForPayment()
        {
            $data = array(
                'Account' => array(
                    'account_id' => '8'
                ),
            );

            $prevNumAccounts = $this->Member->Account->find('count');

            $accountRecord = $this->Member->Account->findByAccountId(8);

            $beforeTimestamp = time();
            $return = $this->Member->acceptDetails( 12, $data, 5 );
            $afterTimestamp = time();

            $this->assertNotEqual( $return, null, 'Failed creating a new account.' );
            $this->assertInternalType( 'array', $return, 'Failed creating new account.' );
            $this->assertArrayHasKey( 'firstname', $return, 'Return array is invalid.' );
            $this->assertEqual( $return['firstname'], 'Roy', 'Return array has invalid firstname.' );
            $this->assertArrayHasKey( 'surname', $return, 'Return array is invalid.' );
            $this->assertEqual( $return['surname'], 'Forsman', 'Return array has invalid surname.' );
            $this->assertArrayHasKey( 'email', $return, 'Return array is invalid.' );
            $this->assertEqual( $return['email'], 'RoyJForsman@teleworm.us', 'Return array has invalid email.' );
            $this->assertArrayHasKey( 'paymentRef', $return, 'Return array is invalid.' );
            $this->assertEqual( $prevNumAccounts, $this->Member->Account->find('count'), 'An account was created!.' );
            $this->assertEqual( $return['paymentRef'], $accountRecord['Account']['payment_ref'], 'Payment ref was incorrect.' );

            $record = $this->Member->findByMemberId(12);
            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );
            $this->assertArrayHasKey( 'Member', $record, 'Record does not have member key.' );
            $this->assertArrayHasKey( 'member_id', $record['Member'], 'Record Member does not have member_id key.' );
            $this->assertArrayHasKey( 'member_status', $record['Member'], 'Record Member does not have member_status key.' );
            $this->assertEqual( $record['Member']['member_status'], Status::PRE_MEMBER_3, 'Record has incorrect status.' );
            $this->assertArrayHasKey( 'account_id', $record['Member'], 'Record Member does not have account_id key.' );
            $this->assertEqual( $record['Member']['account_id'], 8, 'Record has incorrect account_id.' );

            $this->assertArrayHasKey( 'StatusUpdate', $record, 'Record does not have status update key.' );
            $this->assertEqual( count($record['StatusUpdate']), 1, 'Record has incorrect number of status updates.' );
            $this->assertArrayHasKey( 'id', $record['StatusUpdate'][0], 'Record does not have id key.' );
            $this->assertArrayHasKey( 'member_id', $record['StatusUpdate'][0], 'Record does not have member_id key.' );
            $this->assertArrayHasKey( 'admin_id', $record['StatusUpdate'][0], 'Record does not have admin_id key.' );
            $this->assertArrayHasKey( 'old_status', $record['StatusUpdate'][0], 'Record does not have old_status key.' );
            $this->assertArrayHasKey( 'new_status', $record['StatusUpdate'][0], 'Record does not have new_status key.' );
            $this->assertArrayHasKey( 'timestamp', $record['StatusUpdate'][0], 'Record does not have timestamp key.' );
            $this->assertEqual( $record['StatusUpdate'][0]['id'], 4, 'Record has incorrect id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['member_id'], 12, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['admin_id'], 5, 'Record has incorrect admin_id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['old_status'], Status::PRE_MEMBER_2, 'Record has incorrect old_status.' );
            $this->assertEqual( $record['StatusUpdate'][0]['new_status'], Status::PRE_MEMBER_3, 'Record has incorrect new_status.' );
            $this->assertGreaterThanOrEqual( $beforeTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp.' );
            $this->assertLessThanOrEqual( $afterTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp.' );
        }

        public function testAcceptDetailsExistingAccountCurrentMember()
        {
            $data = array(
                'Account' => array(
                    'account_id' => '3'
                ),
            );

            $prevNumAccounts = $this->Member->Account->find('count');

            $accountRecord = $this->Member->Account->findByAccountId(3);

            $beforeTimestamp = time();
            $return = $this->Member->acceptDetails( 12, $data, 5 );
            $afterTimestamp = time();

            $this->assertNotEqual( $return, null, 'Failed creating a new account.' );
            $this->assertInternalType( 'array', $return, 'Failed creating new account.' );
            $this->assertArrayHasKey( 'firstname', $return, 'Return array is invalid.' );
            $this->assertEqual( $return['firstname'], 'Roy', 'Return array has invalid firstname.' );
            $this->assertArrayHasKey( 'surname', $return, 'Return array is invalid.' );
            $this->assertEqual( $return['surname'], 'Forsman', 'Return array has invalid surname.' );
            $this->assertArrayHasKey( 'email', $return, 'Return array is invalid.' );
            $this->assertEqual( $return['email'], 'RoyJForsman@teleworm.us', 'Return array has invalid email.' );
            $this->assertArrayHasKey( 'paymentRef', $return, 'Return array is invalid.' );
            $this->assertEqual( $prevNumAccounts, $this->Member->Account->find('count'), 'An account was created!.' );
            $this->assertEqual( $return['paymentRef'], $accountRecord['Account']['payment_ref'], 'Payment ref was incorrect.' );

            $record = $this->Member->findByMemberId(12);
            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );
            $this->assertArrayHasKey( 'Member', $record, 'Record does not have member key.' );
            $this->assertArrayHasKey( 'member_id', $record['Member'], 'Record Member does not have member_id key.' );
            $this->assertArrayHasKey( 'member_status', $record['Member'], 'Record Member does not have member_status key.' );
            $this->assertEqual( $record['Member']['member_status'], Status::PRE_MEMBER_3, 'Record has incorrect status.' );
            $this->assertArrayHasKey( 'account_id', $record['Member'], 'Record Member does not have account_id key.' );
            $this->assertEqual( $record['Member']['account_id'], 3, 'Record has incorrect account_id.' );

            $this->assertArrayHasKey( 'StatusUpdate', $record, 'Record does not have status update key.' );
            $this->assertEqual( count($record['StatusUpdate']), 1, 'Record has incorrect number of status updates.' );
            $this->assertArrayHasKey( 'id', $record['StatusUpdate'][0], 'Record does not have id key.' );
            $this->assertArrayHasKey( 'member_id', $record['StatusUpdate'][0], 'Record does not have member_id key.' );
            $this->assertArrayHasKey( 'admin_id', $record['StatusUpdate'][0], 'Record does not have admin_id key.' );
            $this->assertArrayHasKey( 'old_status', $record['StatusUpdate'][0], 'Record does not have old_status key.' );
            $this->assertArrayHasKey( 'new_status', $record['StatusUpdate'][0], 'Record does not have new_status key.' );
            $this->assertArrayHasKey( 'timestamp', $record['StatusUpdate'][0], 'Record does not have timestamp key.' );
            $this->assertEqual( $record['StatusUpdate'][0]['id'], 4, 'Record has incorrect id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['member_id'], 12, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['admin_id'], 5, 'Record has incorrect admin_id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['old_status'], Status::PRE_MEMBER_2, 'Record has incorrect old_status.' );
            $this->assertEqual( $record['StatusUpdate'][0]['new_status'], Status::PRE_MEMBER_3, 'Record has incorrect new_status.' );
            $this->assertGreaterThanOrEqual( $beforeTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp.' );
            $this->assertLessThanOrEqual( $afterTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp.' );
        }

        public function testAcceptDetailsOnlySavesMemberStatusAccountIdExistingAccount()
        {
            $data = array(
                'Member' => array(
                    'member_id' => 14,
                    'firstname' => 'Foo',
                    'surname' => 'Barson',
                    'email' => 'CherylLCarignan@teleworm.us',
                    'join_date' => '2010-09-22',
                    'handle' => 'bildestonelectrician',
                    'unlock_text' => 'Hey Kelly',
                    'balance' => -5649,
                    'credit_limit' => 5000,
                    'member_status' => 5,
                    'username' => 'fubbby',
                    'account_id' => 4,
                    'address_1' => '8 Elm Close',
                    'address_2' => 'Tetsworth',
                    'address_city' => 'Thame',
                    'address_postcode' => 'OX9 7AP',
                    'contact_number' => '079 0644 8720',
                    'password' => 'hunter2',
                    'password_confirm' => 'hunter2',
                ),
                'Account' => array(
                    'account_id' => '3'
                ),
            );

            $memberId = 11;

            $this->assertNotEqual( $this->Member->acceptDetails($memberId, $data, 5), null, 'Valid data was not handled correctly.' );

            $record = $this->Member->findByMemberId($memberId);

            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );

            $this->assertEqual( $record['Member']['member_id'], 11, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['Member']['firstname'], 'Betty', 'Record has incorrect firstname.' );
            $this->assertEqual( $record['Member']['surname'], 'Paris', 'Record has incorrect surname.' );
            $this->assertEqual( $record['Member']['email'], 'BettyCParis@teleworm.us', 'Record has incorrect email.' );
            $this->assertEqual( $record['Member']['join_date'], '0000-00-00', 'Record has incorrect join date.' );
            $this->assertEqual( $record['Member']['unlock_text'], null, 'Record has incorrect unlock text.' );
            $this->assertEqual( $record['Member']['balance'], 0, 'Record has incorrect balance.' );
            $this->assertEqual( $record['Member']['credit_limit'], 0, 'Record has incorrect credit limit.' );
            $this->assertEqual( $record['Member']['member_status'], Status::PRE_MEMBER_3, 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['username'], 'Beltonstlend51', 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['account_id'], 3, 'Record has incorrect account id.' );
            $this->assertEqual( $record['Member']['address_1'], '10 Hampton Court Rd', 'Record has incorrect address 1.' );
            $this->assertEqual( $record['Member']['address_2'], null, 'Record has incorrect address 2.' );
            $this->assertEqual( $record['Member']['address_city'], 'Spelsbury', 'Record has incorrect address city.' );
            $this->assertEqual( $record['Member']['address_postcode'], 'OX7 2US', 'Record has incorrect address postcode.' );
            $this->assertEqual( $record['Member']['contact_number'], '079 0572 8737', 'Record has incorrect contact number.' );
        }

        public function testAcceptDetailsOnlySavesMemberStatusAccountIdNewAccount()
        {
            $data = array(
                'Member' => array(
                    'member_id' => 14,
                    'firstname' => 'Foo',
                    'surname' => 'Barson',
                    'email' => 'CherylLCarignan@teleworm.us',
                    'join_date' => '2010-09-22',
                    'handle' => 'bildestonelectrician',
                    'unlock_text' => 'Hey Kelly',
                    'balance' => -5649,
                    'credit_limit' => 5000,
                    'member_status' => 5,
                    'username' => 'fubbby',
                    'account_id' => 4,
                    'address_1' => '8 Elm Close',
                    'address_2' => 'Tetsworth',
                    'address_city' => 'Thame',
                    'address_postcode' => 'OX9 7AP',
                    'contact_number' => '079 0644 8720',
                    'password' => 'hunter2',
                    'password_confirm' => 'hunter2',
                ),
                'Account' => array(
                    'account_id' => '-1'
                ),
            );

            $memberId = 11;

            $this->assertNotEqual( $this->Member->acceptDetails($memberId, $data, 5), null, 'Valid data was not handled correctly.' );

            $record = $this->Member->findByMemberId($memberId);

            $this->assertNotIdentical( $record, null, 'Could not find record.' );
            $this->assertInternalType( 'array', $record, 'Could not find record.' );

            $this->assertEqual( $record['Member']['member_id'], 11, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['Member']['firstname'], 'Betty', 'Record has incorrect firstname.' );
            $this->assertEqual( $record['Member']['surname'], 'Paris', 'Record has incorrect surname.' );
            $this->assertEqual( $record['Member']['email'], 'BettyCParis@teleworm.us', 'Record has incorrect email.' );
            $this->assertEqual( $record['Member']['join_date'], '0000-00-00', 'Record has incorrect join date.' );
            $this->assertEqual( $record['Member']['unlock_text'], null, 'Record has incorrect unlock text.' );
            $this->assertEqual( $record['Member']['balance'], 0, 'Record has incorrect balance.' );
            $this->assertEqual( $record['Member']['credit_limit'], 0, 'Record has incorrect credit limit.' );
            $this->assertEqual( $record['Member']['member_status'], Status::PRE_MEMBER_3, 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['username'], 'Beltonstlend51', 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['account_id'], 9, 'Record has incorrect account id.' );
            $this->assertEqual( $record['Member']['address_1'], '10 Hampton Court Rd', 'Record has incorrect address 1.' );
            $this->assertEqual( $record['Member']['address_2'], null, 'Record has incorrect address 2.' );
            $this->assertEqual( $record['Member']['address_city'], 'Spelsbury', 'Record has incorrect address city.' );
            $this->assertEqual( $record['Member']['address_postcode'], 'OX7 2US', 'Record has incorrect address postcode.' );
            $this->assertEqual( $record['Member']['contact_number'], '079 0572 8737', 'Record has incorrect contact number.' );
        }

        public function testApproveMemberInvalidData()
        {
            $this->assertIdentical( $this->Member->approveMember(null, null), null, 'Null data was not handled correctly.' );
            $this->assertIdentical( $this->Member->approveMember(-1, 0), null, 'Invalid id was not handled correctly.' );
            $this->assertIdentical( $this->Member->approveMember(0, 1), null, 'Invalid id was not handled correctly.' );
            $this->assertIdentical( $this->Member->approveMember(2076, 1), null, 'Invalid id was not handled correctly.' );
        }

        public function testApproveMemberThrows()
        {
            $testMemberIds = array( 1, 2, 3, 4, 5, 7, 8, 9, 10, 11, 12 );
            foreach ($testMemberIds as $memberId) 
            {
                $threw = false;
                try
                {
                    $this->Member->approveMember($memberId, 5);
                }
                catch(InvalidStatusException $e)
                {
                    $threw = true;
                }
                
                $this->assertTrue( $threw, 'approveMember for member id ' . $memberId . ' failed to throw.' );
            }
        }

        public function testApproveMember()
        {
            $beforeTimestamp = time();
            $return = $this->Member->approveMember( 14, 5 );
            $afterTimestamp = time();

            $this->assertNotEqual( $return, null, 'Failed approving member.' );
            $this->assertInternalType( 'array', $return, 'Failed approving member.' );
            $this->assertArrayHasKey( 'firstname', $return, 'Return array is invalid.' );
            $this->assertEqual( $return['firstname'], 'Evan', 'Return array has invalid firstname.' );
            $this->assertArrayHasKey( 'surname', $return, 'Return array is invalid.' );
            $this->assertEqual( $return['surname'], 'Atkinson', 'Return array has invalid surname.' );
            $this->assertArrayHasKey( 'email', $return, 'Return array is invalid.' );
            $this->assertEqual( $return['email'], 'EvanAtkinson@teleworm.us', 'Return array has invalid email.' );
            $this->assertArrayHasKey( 'pin', $return, 'Return array is invalid.' );

            $record = $this->Member->findByMemberId(14);

            $this->assertEqual( $record['Member']['member_id'], 14, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['Member']['firstname'], 'Evan', 'Record has incorrect firstname.' );
            $this->assertEqual( $record['Member']['surname'], 'Atkinson', 'Record has incorrect surname.' );
            $this->assertEqual( $record['Member']['email'], 'EvanAtkinson@teleworm.us', 'Record has incorrect email.' );
            $this->assertEqual( $record['Member']['join_date'], date('Y-m-d'), 'Record has incorrect join date.' );
            $this->assertEqual( $record['Member']['unlock_text'], 'Welcome Evan', 'Record has incorrect unlock text.' );
            $this->assertEqual( $record['Member']['balance'], 0, 'Record has incorrect balance.' );
            $this->assertEqual( $record['Member']['credit_limit'], 2000, 'Record has incorrect credit limit.' );
            $this->assertEqual( $record['Member']['member_status'], Status::CURRENT_MEMBER, 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['username'], 'Sookinium', 'Record has incorrect status.' );
            $this->assertEqual( $record['Member']['account_id'], 8, 'Record has incorrect account id.' );
            $this->assertEqual( $record['Member']['address_1'], '34 Wartnaby Road', 'Record has incorrect address 1.' );
            $this->assertEqual( $record['Member']['address_2'], null, 'Record has incorrect address 2.' );
            $this->assertEqual( $record['Member']['address_city'], 'Acklam', 'Record has incorrect address city.' );
            $this->assertEqual( $record['Member']['address_postcode'], 'TS5 3HD', 'Record has incorrect address postcode.' );
            $this->assertEqual( $record['Member']['contact_number'], '078 1957 5612', 'Record has incorrect contact number.' );

            $this->assertEqual( $record['Pin'][0]['state'], 40, 'Record has incorrect pin state.' );
            $this->assertEqual( $record['Pin'][0]['member_id'], 14, 'Record has incorrect pin member id.' );

            $this->assertEqual( $record['Group'], array( '0' => array( 'grp_id' => Group::CURRENT_MEMBERS, 'grp_description' => 'Current Members') ), 'Record has incorrect group.' );

            $this->assertArrayHasKey( 'StatusUpdate', $record, 'Record does not have status update key.' );
            $this->assertEqual( count($record['StatusUpdate']), 1, 'Record has incorrect number of status updates.' );
            $this->assertArrayHasKey( 'id', $record['StatusUpdate'][0], 'Record does not have id key.' );
            $this->assertArrayHasKey( 'member_id', $record['StatusUpdate'][0], 'Record does not have member_id key.' );
            $this->assertArrayHasKey( 'admin_id', $record['StatusUpdate'][0], 'Record does not have admin_id key.' );
            $this->assertArrayHasKey( 'old_status', $record['StatusUpdate'][0], 'Record does not have old_status key.' );
            $this->assertArrayHasKey( 'new_status', $record['StatusUpdate'][0], 'Record does not have new_status key.' );
            $this->assertArrayHasKey( 'timestamp', $record['StatusUpdate'][0], 'Record does not have timestamp key.' );
            $this->assertEqual( $record['StatusUpdate'][0]['id'], 4, 'Record has incorrect id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['member_id'], 14, 'Record has incorrect member_id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['admin_id'], 5, 'Record has incorrect admin_id.' );
            $this->assertEqual( $record['StatusUpdate'][0]['old_status'], Status::PRE_MEMBER_3, 'Record has incorrect old_status.' );
            $this->assertEqual( $record['StatusUpdate'][0]['new_status'], Status::CURRENT_MEMBER, 'Record has incorrect new_status.' );
            $this->assertGreaterThanOrEqual( $beforeTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp.' );
            $this->assertLessThanOrEqual( $afterTimestamp, strtotime($record['StatusUpdate'][0]['timestamp']), 'Record has incorrect timestamp.' );
        }

        public function testChangePasswordInvalidData()
        {
            $this->assertFalse( $this->Member->changePassword(null, null, null), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->Member->changePassword(0, 0, array()), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->Member->changePassword(-1, 0, array()), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->Member->changePassword(0, -1, array()), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->Member->changePassword('2', '1', array()), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->Member->changePassword(2, 1, array()), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->Member->changePassword(2, 1, array('ChangePassword')), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->Member->changePassword(2, 1, array('ChangePassword' => array())), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->Member->changePassword(2, 1, array('ChangePassword' => array('current_password'))), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->Member->changePassword(2, 1, array('ChangePassword' => array('current_password', 'new_password'))), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->Member->changePassword(2, 1, array('ChangePassword' => array('current_password', 'new_password', 'new_password_confirm'))), 'Invalid data not handled correctly.' );
            $this->assertFalse( $this->Member->changePassword(1, 1, array('ChangePassword' => array('current_password' => 'foo', 'new_password' => 'dsads', 'new_password_confirm' => 'completely different'))), 'Invalid data not handled correctly.' );
        }

        public function testChangePasswordThrows()
        {
            $data = array(
                'ChangePassword' => array(
                    'current_password' => 'foo', 
                    'new_password' => 'completely', 
                    'new_password_confirm' => 'completely'
                )
            );

            $testMemberIds = array( 7, 8 );
            foreach ($testMemberIds as $memberId) 
            {
                $threw = false;
                try
                {
                    $this->Member->changePassword($memberId, 5, $data);
                }
                catch(InvalidStatusException $e)
                {
                    $threw = true;
                }
                
                $this->assertTrue( $threw, 'changePassword for member id ' . $memberId . ' failed to throw.' );
            }
        }

        public function testChangePasswordOwnAccount()
        {
            $data = array(
                'ChangePassword' => array(
                    'current_password' => 'foo', 
                    'new_password' => 'completely', 
                    'new_password_confirm' => 'completely'
                )
            );

            $this->assertTrue( $this->Member->changePassword(2, 2, $data), 'Valid data was not handled correctly.' );
            $this->assertTrue( $this->Member->krbCheckPassword('pecanpaella', 'completely'), 'Password was not set correctly.' );
        }

        public function testChangePasswordMemberAdminOtherAccount()
        {
            $data = array(
                'ChangePassword' => array(
                    'current_password' => 'foo', 
                    'new_password' => 'wsafwe', 
                    'new_password_confirm' => 'wsafwe'
                )
            );

            $this->assertTrue( $this->Member->changePassword(3, 5, $data), 'Valid data was not handled correctly.' );
            $this->assertTrue( $this->Member->krbCheckPassword('buntweyr', 'wsafwe'), 'Password was not set correctly.' );
        }

        public function testChangePasswordMemberAdminOwnAccount()
        {
            $data = array(
                'ChangePassword' => array(
                    'current_password' => 'foo', 
                    'new_password' => 'jyty6ut65', 
                    'new_password_confirm' => 'jyty6ut65'
                )
            );

            $this->assertTrue( $this->Member->changePassword(5, 5, $data), 'Valid data was not handled correctly.' );
            $this->assertTrue( $this->Member->krbCheckPassword('chollertonbanker', 'jyty6ut65'), 'Password was not set correctly.' );
        }

        public function testCreateForgotPasswordInvalidData()
        {
            $this->assertFalse( $this->Member->createForgotPassword(null), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->createForgotPassword('fdfs'), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->createForgotPassword(-1), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->createForgotPassword(1), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->createForgotPassword(array()), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->createForgotPassword(array('ForgotPassword')), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->createForgotPassword(array('ForgotPassword' => array('email'))), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->createForgotPassword(array('ForgotPassword' => array('email' => 'totallynotavalidemail@test.org.uk'))), 'Invalid data was not handled correctly.' );
        }

        public function testCreateForgotPasswordThrows()
        {
            $testData = array(
                7 => array(
                    'ForgotPassword' => array(
                        'email' => 'CherylLCarignan@teleworm.us',
                    )
                ),
                8 => array(
                    'ForgotPassword' => array(
                        'email' => 'MelvinJFerrell@dayrep.com',
                    )
                )
            );

            foreach ($testData as $memberId => $data) 
            {
                $threw = false;
                try
                {
                    $this->Member->createForgotPassword($data);
                }
                catch(InvalidStatusException $e)
                {
                    $threw = true;
                }
                
                $this->assertTrue( $threw, 'createForgotPassword for member id ' . $memberId . ' failed to throw.' );
            }
        }

        public function testCreateForgotPassword()
        {
            $testData = array(
                1 => 'm.pryce@example.org',
                2 => 'a.santini@hotmail.com',
                3 => 'g.viles@gmail.com',
                4 => 'k.savala@yahoo.co.uk',
                5 => 'j.easterwood@googlemail.com',
                6 => 'g.garratte@foobar.org',
                9 => 'DorothyDRussell@dayrep.com',
                10 => 'HugoJLorenz@dayrep.com',
                11 => 'BettyCParis@teleworm.us',
                12 => 'RoyJForsman@teleworm.us',
                13 => 'RyanMiles@dayrep.com',
                15 => 'EvanAtkinson@teleworm.us',
            );

            foreach ($testData as $memberId => $email) 
            {
                $this->assertInternalType( 'array', $this->Member->createForgotPassword(array('ForgotPassword' => array('email' => $email))), 'Failed to create forgot password for member: ' . $memberId . '.');
            }
        }

        public function testCompleteForgotPasswordInvalidData()
        {
            $this->assertFalse( $this->Member->completeForgotPassword(null, null), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->completeForgotPassword('addwd', 'fdfs'), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->completeForgotPassword(CakeText::UUID(), -1), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->completeForgotPassword('adssd', 1), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->completeForgotPassword(CakeText::UUID(), array()), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->completeForgotPassword(CakeText::UUID(), array('ForgotPassword')), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->completeForgotPassword(CakeText::UUID(), array('ForgotPassword' => array('email'))), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->completeForgotPassword(CakeText::UUID(), array('ForgotPassword' => array('email' => 'totallynotavalidemail@test.org.uk'))), 'Invalid data was not handled correctly.' );

            $data = array(
                'ForgotPassword' => array(
                    'email' => 'totallynotavalidemail@test.org.uk',
                    'new_password' => 'adsad',
                    'new_password_confirm' => 'not the same',
                )
            );
            $this->assertFalse( $this->Member->completeForgotPassword(CakeText::UUID(), $data), 'Invalid data was not handled correctly.' );

            $data = array(
                'ForgotPassword' => array(
                    'email' => 'm.pryce@example.org',
                    'new_password' => 'thesame',
                    'new_password_confirm' => 'thesame',
                )
            );
            $this->assertFalse( $this->Member->completeForgotPassword('50b0ec45-8984-48b8-ac8a-5db90a000005', $data), 'Invalid data was not handled correctly.' );

            $data = array(
                'ForgotPassword' => array(
                    'email' => 'g.viles@gmail.com',
                    'new_password' => 'thesame',
                    'new_password_confirm' => 'thesame',
                )
            );
            $this->assertFalse( $this->Member->completeForgotPassword('50be19c8-0968-43ba-be1b-0990bcda665d', $data), 'Invalid data was not handled correctly.' );
        }

        public function testCompleteForgotPassword()
        {
            $data = array(
                'ForgotPassword' => array(
                    'email' => 'a.santini@hotmail.com',
                    'new_password' => 'thesame',
                    'new_password_confirm' => 'thesame',
                )
            );
            $this->assertTrue( $this->Member->completeForgotPassword('50b104e4-33f8-4821-b756-5e100a000005', $data), 'Valid data was not handled correctly.' );
        }

        public function testGetSoDetails()
        {
            $this->assertIdentical( $this->Member->getSoDetails(null), null, 'Null data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getSoDetails(-1), null, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getSoDetails(0), null, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getSoDetails(2076), null, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getSoDetails(array()), null, 'Invalid data was not handled correctly.' );

            $this->assertEqual( $this->Member->getSoDetails(2), array('firstname' => 'Annabelle', 'surname' => 'Santini', 'email' => 'a.santini@hotmail.com', 'paymentRef' => 'HSTSBKFK2R62GQW6'), 'Valid data was not handled correctly.' );            
        }

        public function testGetApproveDetails()
        {
            $this->assertIdentical( $this->Member->getApproveDetails(null), null, 'Null data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getApproveDetails(-1), null, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getApproveDetails(0), null, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getApproveDetails(2076), null, 'Invalid data was not handled correctly.' );
            $this->assertIdentical( $this->Member->getApproveDetails(array()), null, 'Invalid data was not handled correctly.' );

            $this->assertEqual( $this->Member->getApproveDetails(4), array('firstname' => 'Kelly', 'surname' => 'Savala', 'email' => 'k.savala@yahoo.co.uk', 'pin' => '5436'), 'Valid data was not handled correctly.' );            
        }


        public function testGetReadableAccountList()
        {
            $expectedResult = array( '-1' => 'Create new', '1' => 'Mathew Pryce', '2' => 'Annabelle Santini', '3' => 'Jessie Easterwood, Kelly Savala and Guy Viles', '6' => 'Guy Garrette', '7' => 'Ryan Miles', '8' => 'Evan Atkinson' );
            $this->assertEqual( $this->Member->getReadableAccountList(), $expectedResult, 'Invalid result.' );
        }

        public function testValidateEmailInvalidData()
        {
            $this->assertFalse( $this->Member->validateEmail(null), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->validateEmail('2123312'), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->validateEmail(-1), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->validateEmail(26), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->validateEmail(array()), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->validateEmail(array('MemberEmail')), 'Invalid data was not handled correctly.' );
            $this->assertFalse( $this->Member->validateEmail(array('MemberEmail' => array('subject', 'message'))), 'Invalid data was not handled correctly.' );
        }

        public function testValidateEmailValidData()
        {
            $data = array('subject' => 'This is a subject', 'message' => 'Lorem impus sit dom amet.');
            $this->assertEqual( $this->Member->validateEmail(array('MemberEmail' => $data)), $data, 'Valid data was not handled correctly.' );   
        }

        public function testEmailToMemberIdInvalidData()
        {
            $this->assertEqual( $this->Member->emailToMemberId(null), null, 'Null input was not handled correctly.' );
            $this->assertEqual( $this->Member->emailToMemberId(435), null, 'Numeric input was not handled correctly.' );
        }

        public function testEmailToMemberIdReturnsNullForUnknownEmail()
        {
            $this->assertEqual( $this->Member->emailToMemberId('totallymadeupemailthatdoesntexist@ffooosadff.cdf'), null, 'Unknown email input was not handled correctly.' );
        }

        public function testEmailToMemberIdHandlesSingleKnownEmail()
        {
            $this->assertEqual( $this->Member->emailToMemberId('g.garratte@foobar.org'), 6, 'Single known email input was not handled correctly.' );
        }

        public function testEmailToMemberIdHandlesMultipleKnownEmails()
        {
            $emailList = array(
                'm.pryce@example.org',      
                'a.santini@hotmail.com',
                'g.viles@gmail.com',
                'k.savala@yahoo.co.uk',
                'j.easterwood@googlemail.com',
                'g.garratte@foobar.org',
            );

            $expectedResults = array(
                1, 2, 3, 4, 5, 6
            );
            $this->assertEqual( $this->Member->emailToMemberId($emailList), $expectedResults, 'Miltuple known email input was not handled correctly.' );
        }

        public function testEmailToMemberIdExcludesUnknownEmailFromArrayResults()
        {
            $emailList = array(
                'm.pryce@example.org',      
                'a.santini@hotmail.com',
                'g.viles@gmail.com',
                'totallymadeupemailthatdoesntexist@ffooosadff.cdf',
                'k.savala@yahoo.co.uk',
                'j.easterwood@googlemail.com',
                'g.garratte@foobar.org',
            );

            $expectedResults = array(
                1, 2, 3, 4, 5, 6
            );

            $this->assertEqual( $this->Member->emailToMemberId($emailList), $expectedResults, 'Unknown e-mail was not excluded from array results.' );
        }

        public function testGetBestMemberNames()
        {
            $expectedResults = array(
                1 => 'Mathew Pryce',
                2 => 'Annabelle Santini',
                3 => 'Guy Viles',
                4 => 'Kelly Savala',
                5 => 'Jessie Easterwood',
                6 => 'Guy Garrette',
                7 => 'CherylLCarignan@teleworm.us',
                8 => 'MelvinJFerrell@dayrep.com',
                9 => 'Dorothy Russell',
                10 => 'Hugo Lorenz',
                11 => 'Betty Paris',
                12 => 'Roy Forsman',
                13 => 'Ryan Miles',
                14 => 'Evan Atkinson',
            );

            $this->assertEqual( $this->Member->getBestMemberNames(), $expectedResults, 'Results were not correct.' );
        }
    }

?>