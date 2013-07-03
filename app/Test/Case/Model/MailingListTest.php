<?php

    App::uses('MailingList', 'Model');

    class MailingListTest extends CakeTestCase 
    {
        public $useTable = false;
        
        public $fixtures = array( 'app.Status', 'app.Member', 'app.Account', 'app.Pin', 'app.Group', 'app.GroupsMember', 'app.MailingLists', 'app.MailingListSubscriptions' );

        
        private function _createMailingListModel()
        {
            $this->MailingList = ClassRegistry::init('MailingList');
        }

        public function testCacheMailingListsEqualsMailingLists()
        {
            $this->_createMailingListModel();
            
            $uncached = $this->MailingList->listMailinglists(false);
            $cached = $this->MailingList->listMailinglists(true);

            $this->assertEqual($uncached, $cached, 'Cached and un-cached values were different.');
            $this->assertInternalType('array', $uncached, 'Uncached is not of array type');
            $this->assertInternalType('array', $cached, 'Cached is not of array type');
        }

        public function testCacheGetListEqualsGetList()
        {
            $this->_createMailingListModel();

            $allMailingLists = $this->MailingList->listMailinglists(false);
            foreach ($allMailingLists['data'] as $mailingList) 
            {
                $id = $mailingList['id'];
                $uncached = $this->MailingList->getMailinglist($id, false);
                $cached = $this->MailingList->getMailinglist($id, true);

                $this->assertEqual($uncached, $cached, 'Cached and un-cached values were different for id: ' . $id);
                $this->assertInternalType('array', $uncached, 'Uncached is not of array type for id: ' . $id);
                $this->assertInternalType('array', $cached, 'Cached is not of array type for id: ' . $id);
            }
        }

        public function testCacheListSubscribersEqualsListSubscribers()
        {
            $this->_createMailingListModel();

            $allMailingLists = $this->MailingList->listMailinglists(false);
            foreach ($allMailingLists['data'] as $mailingList) 
            {
                $id = $mailingList['id'];
                $uncached = $this->MailingList->listSubscribers($id, false);
                $cached = $this->MailingList->listSubscribers($id, true);

                $this->assertEqual($uncached, $cached, 'Cached and un-cached values were different for id: ' . $id);
                $this->assertInternalType('array', $uncached, 'Uncached is not of array type for id: ' . $id);
                $this->assertInternalType('array', $cached, 'Cached is not of array type for id: ' . $id);
            }
        }

        public function testIsEmailAddressSubscriberInvalidData()
        {
            $this->_createMailingListModel();

            $this->assertFalse($this->MailingList->isEmailAddressSubscriber('', ''), 'Invalid data was not handled correctly');
            $this->assertFalse($this->MailingList->isEmailAddressSubscriber(array(), ''), 'Invalid data was not handled correctly');
            $this->assertFalse($this->MailingList->isEmailAddressSubscriber('foo@bar.com', 54), 'Invalid data was not handled correctly');
            $this->assertFalse($this->MailingList->isEmailAddressSubscriber('foo@bar.com', array()), 'Invalid data was not handled correctly');
        }

        public function testIsEmailAddressSubscriberValidData()
        {
            $this->_createMailingListModel();

            // Test with unknown list
            $this->assertFalse($this->MailingList->isEmailAddressSubscriber('CherylLCarignan@teleworm.us', '0'), 'Unknown list not handled correctly');

            // Test email we know isn't in the list
            $this->assertFalse($this->MailingList->isEmailAddressSubscriber('foo@teleworm.us', '455de2ac56'), 'Unknown email not handled correctly');

            // Test email we know id in the list
            $this->assertTrue($this->MailingList->isEmailAddressSubscriber('EvanAtkinson@teleworm.us', '455de2ac56'), 'Known email not handled correctly');
        }

        public function testGetListsAndSubscribedStatus()
        {
            $this->_createMailingListModel();

            $data = array(
                'nonexistant@gmail.com' => array(
                    '0a6da449c9' => false, 
                    '455de2ac56' => false,
                ),

                'm.pryce@example.org' => array(
                    '0a6da449c9' => true, 
                    '455de2ac56' => false,
                ),

                'g.garratte@foobar.org' => array(
                    '0a6da449c9' => false, 
                    '455de2ac56' => true,
                ),

                'HugoJLorenz@dayrep.com' => array(
                    '0a6da449c9' => true, 
                    '455de2ac56' => true,  
                ),
            );

            foreach ($data as $email => $subscribedStatus) 
            {
                $result = $this->MailingList->getListsAndSubscribeStatus($email);

                $this->assertInternalType('array', $result, 'Result is not of array type for email ' . $email);

                $this->assertArrayHasKey('total', $result, 'No total key for email ' . $email);
                $this->assertEqual($result['total'], 2, 'Total key has incorrect value for email ' . $email);

                $this->assertArrayHasKey('data', $result, 'No data key for email ' . $email);
                $this->assertInternalType('array', $result['data'], 'Data key is not of array type for email ' . $email);
                $this->assertEqual(count($result['data']), 2, 'Data key has incorrect count for email ' . $email);

                foreach ($result['data'] as $listData)
                {
                    $this->assertArrayHasKey('subscribed', $listData, 'No subscribed key in result for id ' . $listData['id'] . ' and email ' . $email);
                    $this->assertEqual($listData['subscribed'], $subscribedStatus[$listData['id']], 'Subscribed status was not correct for id ' . $listData['id'] . ' and email ' . $email );
                }
            }
        }

        public function testUpdateSubscriptions()
        {
            $this->_createMailingListModel();

            $data = array(
                'nonexistant@gmail.com' => array(
                    'lists' => array(
                        '0a6da449c9', 
                        '455de2ac56',
                    ),
                    'results' => array(
                        array(
                            'list' => '0a6da449c9',
                            'action' => 'subscribe',
                            'successful' => true,
                            'name' => 'Nottingham Hackspace Announcements',
                        ),
                        array(
                            'list' => '455de2ac56',
                            'action' => 'subscribe',
                            'successful' => true,
                            'name' => 'Nottingham Hackspace The Other List',
                        ),
                    ),
                ),

                'm.pryce@example.org' => array(
                    'lists' => array(
                        '455de2ac56',
                    ),
                    'results' => array(
                        array(
                            'list' => '0a6da449c9',
                            'action' => 'unsubscribe',
                            'successful' => true,
                            'name' => 'Nottingham Hackspace Announcements',
                        ),
                        array(
                            'list' => '455de2ac56',
                            'action' => 'subscribe',
                            'successful' => true,
                            'name' => 'Nottingham Hackspace The Other List',
                        ),
                    ),
                ),

                'g.garratte@foobar.org' => array(
                    'lists' => array(
                    ),
                    'results' => array(
                        array(
                            'list' => '455de2ac56',
                            'action' => 'unsubscribe',
                            'successful' => true,
                            'name' => 'Nottingham Hackspace The Other List',
                        ),
                    ),
                ),

                'HugoJLorenz@dayrep.com' => array(
                    'lists' => array(
                    ),
                    'results' => array(
                        array(
                            'list' => '0a6da449c9',
                            'action' => 'unsubscribe',
                            'successful' => true,
                            'name' => 'Nottingham Hackspace Announcements',
                        ),
                        array(
                            'list' => '455de2ac56',
                            'action' => 'unsubscribe',
                            'successful' => true,
                            'name' => 'Nottingham Hackspace The Other List',
                        ),
                    ),
                ),
            );

            foreach ($data as $email => $actionData) 
            {
                $this->assertEqual( $this->MailingList->updateSubscriptions($email, $actionData['lists']), $actionData['results'], 'Data for email ' . $email . ' was not handled correctly.');
            }            
        }
    }

?>