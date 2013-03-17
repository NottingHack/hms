<?php

    App::uses('MailingList', 'Model');

    class MailingListTest extends CakeTestCase 
    {
        public $fixtures = array( 'app.Status', 'app.Member', 'app.Account', 'app.Pin', 'app.Group', 'app.GroupsMember' );

        public function getMailingListMock($listId)
        {
            $data = array(
                '0a6da449c9' => array(
                    'id' => '0a6da449c9',
                    'web_id' => '30569',
                    'name' => 'Nottingham Hackspace Announcements',
                    'date_created' => '2012-06-28 19:12:00',
                    'email_type_option' => '1',
                    'use_awesomebar' => false,
                    'default_from_name' => 'Nottingham Hackspace',
                    'default_from_email' => 'info@nottinghack.org.uk',
                    'default_subject' => 'An Announcement From Nottingham Hackspace',
                    'default_language' => 'en',
                    'list_rating' => '3.5',
                    'subscribe_url_short' => 'http://eepurl.com/ncaln',
                    'subscribe_url_long' => 'http://nottinghack.us5.list-manage.com/subscribe?u=a4e59e4c29bd40e76419a037b&id=0a6da449c9',
                    'beamer_address' => 'YTRlNTllNGMyOWJkNDBlNzY0MTlhMDM3Yi02YTkzMzc3ZS05ZTU5LTQ2ZmUtOTQ5Ni04ODQyYTAzOWVlN2Y=@campaigns.mailchimp.com',
                    'visibility' => 'pub',
                    'stats' => array(
                        'member_count' => 276,
                        'unsubscribe_count' => 6,
                        'cleaned_count' => 1,
                        'member_count_since_send' => 8,
                        'unsubscribe_count_since_send' => 0,
                        'cleaned_count_since_send' => 0,
                        'campaign_count' => 24,
                        'grouping_count' => 0,
                        'group_count' => 0,
                        'merge_var_count' => 2,
                        'avg_sub_rate' => 22,
                        'avg_unsub_rate' => 1,
                        'target_sub_rate' => 1,
                        'open_rate' => 46.108140225787,
                        'click_rate' => 13.967310549777,
                    ),
                    'modules' => array(),
                ),

                '455de2ac56' => array(
                    'id' => '455de2ac56',
                    'web_id' => '64789',
                    'name' => 'Nottingham Hackspace The Other List',
                    'date_created' => '2013-01-12 14:43:00',
                    'email_type_option' => '1',
                    'use_awesomebar' => false,
                    'default_from_name' => 'Nottingham Hackspace',
                    'default_from_email' => 'info@nottinghack.org.uk',
                    'default_subject' => 'Something Else From Nottingham Hackspace',
                    'default_language' => 'en',
                    'list_rating' => '2.3',
                    'subscribe_url_short' => 'http://eepurl.com/sdfet',
                    'subscribe_url_long' => 'http://nottinghack.us5.list-manage.com/subscribe?u=a4e59e4c29bd40e76419a037b&id=455de2ac56',
                    'beamer_address' => 'YTRlNTllNGMyOWJkNDBlNzY0MTlhMDM3Yi02YTkzMzc3ZS05ZTU5LTQ2ZmUtOTQ5Ni04ODQyYTAzOWVlN2Y=@campaigns.mailchimp.com',
                    'visibility' => 'pub',
                        'stats' => array(
                            'member_count' => 23,
                            'unsubscribe_count' => 2,
                            'cleaned_count' => 1,
                            'member_count_since_send' => 3,
                            'unsubscribe_count_since_send' => 1,
                            'cleaned_count_since_send' => 0,
                            'campaign_count' => 2,
                            'grouping_count' => 0,
                            'group_count' => 0,
                            'merge_var_count' => 2,
                            'avg_sub_rate' => 3,
                            'avg_unsub_rate' => 1,
                            'target_sub_rate' => 1,
                            'open_rate' => 24.108140225787,
                            'click_rate' => 91.967310549777,
                        ),
                        'modules' => array(),
                    ),
            );
            
            if(array_key_exists($listId, $data))
            {
                return $data[$listId];
            }

            return false;
        }

        public function listMailingListsMock()
        {
            $data = array(
                'total' => 2,
                'data' => array(
                    $this->getMailingListMock('0a6da449c9'),
                    $this->getMailingListMock('455de2ac56'),
                ),
            );

            return $data;
        }

        public function listSubscribersMock($listId)
        {
            switch($listId)
            {
                case '0a6da449c9':
                    return array(
                        'total' => 45,
                        'data' => array(
                            array(
                                'email' => 'm.pryce@example.org',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'a.santini@hotmail.com',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'g.viles@gmail.com',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'k.savala@yahoo.co.uk',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'j.easterwood@googlemail.com',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'CherylLCarignan@teleworm.us',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'MelvinJFerrell@dayrep.com',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'DorothyDRussell@dayrep.com',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'HugoJLorenz@dayrep.com',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                        ),
                    );
                    break;

                case '455de2ac56':
                    return array(
                        'total' => 45,
                        'data' => array(
                            array(
                                'email' => 'EvanAtkinson@teleworm.us',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'RyanMiles@dayrep.com',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'RoyJForsman@teleworm.us',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'BettyCParis@teleworm.us',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'HugoJLorenz@dayrep.com',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'DorothyDRussell@dayrep.com',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'MelvinJFerrell@dayrep.com',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'CherylLCarignan@teleworm.us',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                            array(
                                'email' => 'g.garratte@foobar.org',
                                'timestamp' => '2012-06-28 19:12:00',
                            ),
                        ),
                    );
                    break;
            }

            return false;
        }

        private function _createMailingListModel($useMock)
        {
            if($useMock)
            {
                $this->MailingList = $this->getMock(
                    'MailingList',
                    array(
                        'listMailinglists',
                        'getMailinglist',
                        'listSubscribers',
                        'subscribe',
                        'unsubscribe',
                        'errorCode',
                        'errorMsg',
                    )
                );

                $this->MailingList->expects($this->any())->method('listMailingLists')->will($this->returnCallback(array($this, 'listMailingListsMock')));
                $this->MailingList->expects($this->any())->method('getMailinglist')->will($this->returnCallback(array($this, 'getMailingListMock')));
                $this->MailingList->expects($this->any())->method('listSubscribers')->will($this->returnCallback(array($this, 'listSubscribersMock')));
                $this->MailingList->expects($this->any())->method('subscribe')->will($this->returnValue(true));
                $this->MailingList->expects($this->any())->method('unsubscribe')->will($this->returnValue(true));

            }
            else
            {
                $this->MailingList = ClassRegistry::init('MailingList');
            }
        }

        public function testCacheMailingListsEqualsMailingLists()
        {
            $this->_createMailingListModel(false);

            $uncached = $this->MailingList->listMailinglists(false);
            $cached = $this->MailingList->listMailinglists(true);

            $this->assertEqual($uncached, $cached, 'Cached and un-cached values were different.');
            $this->assertInternalType('array', $uncached, 'Uncached is not of array type');
            $this->assertInternalType('array', $cached, 'Cached is not of array type');
        }

        public function testCacheGetListEqualsGetList()
        {
            $this->_createMailingListModel(false);

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
            $this->_createMailingListModel(false);

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
            $this->_createMailingListModel(true);

            $this->assertFalse($this->MailingList->isEmailAddressSubscriber('', ''), 'Invalid data was not handled correctly');
            $this->assertFalse($this->MailingList->isEmailAddressSubscriber(array(), ''), 'Invalid data was not handled correctly');
            $this->assertFalse($this->MailingList->isEmailAddressSubscriber('foo@bar.com', 54), 'Invalid data was not handled correctly');
            $this->assertFalse($this->MailingList->isEmailAddressSubscriber('foo@bar.com', array()), 'Invalid data was not handled correctly');
        }

        public function testIsEmailAddressSubscriberValidData()
        {
            $this->_createMailingListModel(true);

            // Test with unknown list
            $this->assertFalse($this->MailingList->isEmailAddressSubscriber('CherylLCarignan@teleworm.us', '0'), 'Unknown list not handled correctly');

            // Test email we know isn't in the list
            $this->assertFalse($this->MailingList->isEmailAddressSubscriber('foo@teleworm.us', '455de2ac56'), 'Unknown email not handled correctly');

            // Test email we know id in the list
            $this->assertTrue($this->MailingList->isEmailAddressSubscriber('EvanAtkinson@teleworm.us', '455de2ac56'), 'Known email not handled correctly');
        }

        public function testGetListsAndSubscribedStatus()
        {
            $this->_createMailingListModel(true);

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
            $this->_createMailingListModel(true);

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
                        ),
                        array(
                            'list' => '455de2ac56',
                            'action' => 'subscribe',
                            'successful' => true,
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
                        ),
                        array(
                            'list' => '455de2ac56',
                            'action' => 'subscribe',
                            'successful' => true,
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
                        ),
                        array(
                            'list' => '455de2ac56',
                            'action' => 'unsubscribe',
                            'successful' => true,
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