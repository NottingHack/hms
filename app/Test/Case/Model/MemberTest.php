<?php

App::uses('Member', 'Model');

class MemberTest extends CakeTestCase 
{
    public $fictures = array( 'app.member' );

    public function setUp() 
    {
    	parent::setUp();
        $this->Member = ClassRegistry::init('Member');
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
}

?>