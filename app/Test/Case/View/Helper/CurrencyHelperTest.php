<?php

App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('CurrencyHelper', 'View/Helper');

class CurrencyHelperTest extends CakeTestCase {
    public function setUp() {
    	parent::setUp();
	    $Controller = new Controller();
	    $View = new View($Controller);
	    $this->Currency = new CurrencyHelper($View);
    }

    public function testCurrency() {
    	$result = $this->Currency->output(500);
    	$this->assertContains('£5.00', $result);

    	$result = $this->Currency->output(5);
    	$this->assertContains('5p', $result);

    	$result = $this->Currency->output(-20);
    	$this->assertContains('-', $result);
    	$this->assertContains('-20p', $result);
    	$this->assertContains('span class="currency_negative"', $result);

    	$result = $this->Currency->output(500000);
    	$this->assertContains('£5,000.00', $result);
    }
}

?>