<?php

	App::uses('ConsumableRequestController', 'Controller');

	App::build(array('TestController' => array('%s' . 'Test' . DS . 'Lib' . DS)), App::REGISTER);
	App::uses('HmsControllerTestBase', 'TestController');

	class ConsumableRequestControllerTest extends HmsControllerTestBase
	{
		public $fixtures = array( 
            'app.ConsumableRequest',
            'app.ConsumableRequestStatus',
            'app.ConsumableSupplier',
            'app.ConsumableArea',
            'app.ConsumableRepeatPurchase',
            'app.ConsumableRequestComment',
            'app.Member',
            'app.Group',
        );

		public function setUp() 
        {
        	parent::setUp();

        	$this->ConsumableRequestController = new ConsumableRequestController();
        	$this->ConsumableRequestController->constructClasses();
        }

		public function test_Index_SetsRequestsInView()
		{
			$this->testAction('/consumableRequest/index');

			$expectedResult = array(
                'request_id' => 1,
                'title' => 'a',
                'detail' => 'a',
                'url' => 'a',
                'supplier_id' => null,
                'area_id' => null,
                'repeat_purchase_id' => null,
                'request_status_id' => 1,
                'member_id' => null,
                'timestamp' => '2013-08-31 09:00:00',
                'status' => array(
                    'request_status_id' => 1,
                    'name' => 'Pending',
                ),
                'supplier' => array(
                    'supplier_id' => null,
                    'name' => null,
                    'description' => null,
                    'address' => null,
                    'url' => null,
                ),
                'area' => array(
                    'area_id' => null,
                    'name' => null,
                    'description' => null,
                ),
                'repeatPurchase' => array(
                    'repeat_purchase_id' => null,
                    'name' => null,
                    'description' => null,
                    'min' => null,
                    'max' => null,
                    'area_id' => null,
                ),
                'comments' => array(
                    array(
                        'request_comment_id' => 1,
                        'text' => 'a',
                        'member_id' => null,
                        'timestamp' => '2013-08-31 09:00:00',
                        'request_id' => 1,
                    ),
                    array(
                        'request_comment_id' => 2,
                        'text' => 'b',
                        'member_id' => 1,
                        'timestamp' => '2013-08-31 10:00:00',
                        'request_id' => 1,
                    ),
                ),
                'member' => array(
                    'member_id' => null,
                    'username' => null,
                ),
            );

			$this->assertEquals( 7, count($this->vars['requests']) );
			$this->assertEquals( $expectedResult, $this->vars['requests'][0] );
		}

		public function test_View_WithNonNumericId_Redirects()
		{
			$this->testAction('/consumableRequest/view/a');

			$this->assertArrayHasKey('Location', $this->headers);
		}

		public function test_View_WithNegativeId_Redirects()
		{
			$this->testAction('/consumableRequest/view/-1');
			
			$this->assertArrayHasKey('Location', $this->headers);
		}

		public function test_View_WithZeroId_Redirects()
		{
			$this->testAction('/consumableRequest/view/0');
			
			$this->assertArrayHasKey('Location', $this->headers);	
		}

		public function test_View_WithNonExistantId_SetsViewVars()
		{
			$this->testAction('/consumableRequest/view/100');
			
			$this->assertEquals( array(), $this->vars['request'] );
		}

		public function test_View_WithValidId_SetsViewVars()
		{
			$this->testAction('/consumableRequest/view/1');

			$expectedResult = array(
                'request_id' => 1,
                'title' => 'a',
                'detail' => 'a',
                'url' => 'a',
                'supplier_id' => null,
                'area_id' => null,
                'repeat_purchase_id' => null,
                'request_status_id' => 1,
                'member_id' => null,
                'timestamp' => '2013-08-31 09:00:00',
                'status' => array(
                    'request_status_id' => 1,
                    'name' => 'Pending',
                ),
                'supplier' => array(
                    'supplier_id' => null,
                    'name' => null,
                    'description' => null,
                    'address' => null,
                    'url' => null,
                ),
                'area' => array(
                    'area_id' => null,
                    'name' => null,
                    'description' => null,
                ),
                'repeatPurchase' => array(
                    'repeat_purchase_id' => null,
                    'name' => null,
                    'description' => null,
                    'min' => null,
                    'max' => null,
                    'area_id' => null,
                ),
                'comments' => array(
                    array(
                        'request_comment_id' => 1,
                        'text' => 'a',
                        'member_id' => null,
                        'timestamp' => '2013-08-31 09:00:00',
                        'request_id' => 1,
                    ),
                    array(
                        'request_comment_id' => 2,
                        'text' => 'b',
                        'member_id' => 1,
                        'timestamp' => '2013-08-31 10:00:00',
                        'request_id' => 1,
                    ),
                ),
                'member' => array(
                    'member_id' => null,
                    'username' => null,
                ),
            );

			$this->assertEquals( $expectedResult, $this->vars['request'] );
		}
	}

?>