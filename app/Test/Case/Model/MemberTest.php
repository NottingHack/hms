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
            $memberList = $this->Member->getMemberSummaryAll(false);

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

        public function testFormatMemberInfo()
        {
            $this->assertIdentical( count($this->Member->formatMemberInfo( array() )), 0, 'FormatMemberInfo of an empty array did not return and empty array.' );


            $memberList = $this->Member->formatMemberInfo( $this->Member->find('all') );
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

        public function testGetEmailsForMembersInGroup()
        {
            $this->assertEqual( count($this->Member->getEmailsForMembersInGroup(0)), 0, 'Incorrect return value for non-existant group.' );

            $this->assertEqual( $this->Member->getEmailsForMembersInGroup(Group::CURRENT_MEMBERS), array( 'm.pryce@example.org', 'a.santini@hotmail.com', 'g.viles@gmail.com', 'k.savala@yahoo.co.uk', 'j.easterwood@googlemail.com' ), 'Incorrect return value for Current Member group.' );
        }

        public function testTryRegisterMember()
        {
            // Test invalid data
            $this->assertIdentical( $this->Member->tryRegisterMember(null), null, 'Null data was not handles correctly.' );
            $this->assertIdentical( $this->Member->tryRegisterMember('fofe'), null, 'Non-array data was not handles correctly.' );
            $this->assertIdentical( $this->Member->tryRegisterMember(array()), null, 'Empty array data was not handles correctly.' );
            $this->assertIdentical( $this->Member->tryRegisterMember(array('Group')), null, 'Invalid array data was not handles correctly.' );
            $this->assertIdentical( $this->Member->tryRegisterMember(array('Member' => array('email'))), null, 'Invalid array data was not handles correctly.' );
            $this->assertIdentical( $this->Member->tryRegisterMember(array('Member' => array('email' => 'not a valid e-mail'))), null, 'Invalid array data was not handles correctly.' );

            // Test with a member that we already know exists, for a member who have the PROSPECTIVE_MEMBER status
            $existingEmail = 'CherylLCarignan@teleworm.us';

            $result = $this->Member->tryRegisterMember( array('Member' => array('email' => $existingEmail)) );

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
                $result = $this->Member->tryRegisterMember( array('Member' => array('email' => $email)) );

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

            // Test with a new e-mail
            $newEmail = 'foo@srsaegrttfd.com';
            $result = $this->Member->tryRegisterMember( array('Member' => array('email' => $newEmail)) );

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

            $this->assertArrayHasKey( 'Member', $record, 'Record does not have member key.' );

            $this->assertArrayHasKey( 'member_id', $record['Member'], 'Record Member does not have member_id key.' );
            $this->assertArrayHasKey( 'email', $record['Member'], 'Record Member does not have email key.' );
            $this->assertIdentical( $record['Member']['email'], $newEmail, 'Record email is incorrect.' );

            $this->assertArrayHasKey( 'member_status', $record['Member'], 'Record Member does not have member_status key.' );
            $this->assertEqual( $record['Member']['member_status'], Status::PROSPECTIVE_MEMBER, 'Record has incorrect status.' );

            $this->assertEqual( $record['Member']['member_id'], $result['memberId'], 'Result has incorrect member id.' );
        }
    }

?>