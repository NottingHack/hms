<?php

require_once ('Setup/Common/MailingListDataGenerator.php');

class MailingListDataGeneratorTest extends PHPUnit_Framework_TestCase {

	private $__generator;

	public function setUp() {
		$this->__generator = new MailingListDataGenerator();
	}

	public function testConstructor_WhenCalled_PopulatesMailingListData() {
		$this->assertNotEmpty($this->__generator->getMailingListsData());
	}

	public function testConstructor_WhenCalled_DoesNotPopulateMailingListSubscriptionData() {
		$this->assertEmpty($this->__generator->getMailingListSubscriptionsData());
	}

	public function testSubscribeEmailToList_WithValidData_CreatesRecord() {

		// Need to set the default time-zone so the this test can be reliable
		// (it makes use of the 'date' function the result of which relies on the
		// timezone set).
		date_default_timezone_set('Etc/UTC');

		$this->__generator->subscribeEmailToList('anything', 'anything', 0);
		$expected = array(
			'mailinglist_id' => 'anything',
			'email' => 'anything',
			'timestamp' => '1970-01-01 00:00:00',
		);
		$this->assertEquals($expected, $this->__generator->getMailingListSubscriptionsData()[0]);
	}

	public function testSubscribeEmailToList_WithValidListId_IncrementsSubscribedCount() {
		$oldMailingListData = $this->__generator->getMailingListsData();
		$idOfFirstList = $oldMailingListData[0]['id'];
		$prevSubscribedCount = $oldMailingListData[0]['member_count'];

		$this->__generator->subscribeEmailToList('anything', $idOfFirstList, 0);

		$this->assertEquals($prevSubscribedCount + 1, $this->__generator->getMailingListsData()[0]['member_count']);
	}
}