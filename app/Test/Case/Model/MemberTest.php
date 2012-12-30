<?php

App::uses('Member', 'Model');

class MemberTest extends CakeTestCase 
{
    public $fixtures = array( 'app.member', 'app.Status' );

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

        $this->assertIdentical($this->Member->passwordConfirmMatchesPassword($testEmail), true);
        $this->assertIdentical($this->Member->passwordConfirmMatchesPassword($anotherTestEmail), false);
        $this->assertIdentical($this->Member->passwordConfirmMatchesPassword(''), false);
        $this->assertIdentical($this->Member->passwordConfirmMatchesPassword(null), false);

        $this->Member->data['Member']['password'] = 0;
        $this->assertIdentical($this->Member->passwordConfirmMatchesPassword('0'), false);
    }

    public function testCheckUniqueUsername()
    {
        $testUsernameTaken = 'strippingdemonic';
        $testUsernameNotTaken = 'TheAwfulGamer';    // Chosen by online random name generator.
                                                    // guaranteed to be random.

        $this->Member->data['Member']['member_id'] = 900;

        $this->assertIdentical($this->Member->checkUniqueUsername($testUsernameTaken), false);
        $this->assertIdentical($this->Member->checkUniqueUsername(strtolower($testUsernameTaken)), false);
        $this->assertIdentical($this->Member->checkUniqueUsername(strtoupper($testUsernameTaken)), false);

        $this->assertIdentical($this->Member->checkUniqueUsername($testUsernameNotTaken), true);
        $this->assertIdentical($this->Member->checkUniqueUsername(strtolower($testUsernameNotTaken)), true);
        $this->assertIdentical($this->Member->checkUniqueUsername(strtoupper($testUsernameNotTaken)), true);

        $this->Member->data['Member']['member_id'] = 1;

        $this->assertIdentical($this->Member->checkUniqueUsername($testUsernameTaken), true);
        $this->assertIdentical($this->Member->checkUniqueUsername(strtolower($testUsernameTaken)), true);
        $this->assertIdentical($this->Member->checkUniqueUsername(strtoupper($testUsernameTaken)), true);
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
            $this->assertIdentical($this->Member->checkEmailMatch($testEmails[$i]), true);
            for($j = 0; $j < count($testEmails); $j++)
            {
                if($i != $j)
                {
                    $this->assertIdentical($this->Member->checkEmailMatch($testEmails[$j]), false);
                }
            }
        }
    }

    public function testBeforeSave()
    {
        $this->Member->data['Member']['balance'] = -400;
        $this->Member->beforeSave();
        $this->assertIdentical(isset($this->Member->data['Member']['balance']), false);
    }

    public function testAddEmailMustMatch()
    {
        # The Member shouldn't start out with this validation rule
        $this->assertIdentical($this->Member->validator()->getField('email')->getRule('emailMustMatch'), null);

        $this->Member->addEmailMustMatch();

        $this->assertNotIdentical($this->Member->validator()->getField('email')->getRule('emailMustMatch'), null);
    }

    public function testRemoveEmailMustMatch()
    {
        # The Member shouldn't start out with this validation rule
        $this->Member->addEmailMustMatch();
        $this->Member->removeEmailMustMatch();
        $this->assertIdentical($this->Member->validator()->getField('email')->getRule('emailMustMatch'), null);
    }

    public function testGetCountForStatus()
    {
        $this->assertIdentical($this->Member->getCountForStatus(1), 1);
        $this->assertIdentical($this->Member->getCountForStatus(2), 1);
        $this->assertIdentical($this->Member->getCountForStatus(3), 1);
        $this->assertIdentical($this->Member->getCountForStatus(4), 1);
        $this->assertIdentical($this->Member->getCountForStatus(5), 1);
        $this->assertIdentical($this->Member->getCountForStatus(6), 1);
    }

    public function testGetCount()
    {
        $this->assertIdentical($this->Member->getCount(), 6);
    }
}

?>