<?php
/**
 * 
 * PHP 5
 *
 * Copyright (C) HMS Team
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     HMS Team
 * @package       app.Model
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('AppModel', 'Model');

/**
 * Model for all tracking notifications sent to member about the possible expire of there membership
 */
class MembershipStatusNotification extends AppModel {

/**
 * The Notification was clear due to a payment before membership was revoked
 */
    const PAYMENT = 'PAYMENT';

/**
 * The Notification was cleared when the membership was revoked
 */
    const REVOKED = 'REVOKED';

/**
 * The Notification was cleared manually, likely due to audit issues
 */
    const MANUAL = 'MANUAL';

/**
 * Specify the table to use
 * @var string
 */
    public $useTable = 'membership_status_notifications';

/**
 * Specify the primary key to use.
 * @var string
 */
    public $primaryKey = 'membership_status_notification_id'; //!< Specify the primary key to use.

/**
 * fetch array of memberIds with a current issued warnings
 * @return array of memberIds
 */
    public function listAllMembersWithNotifications()
    {
        // SELLECT member_id FROM membership_status_notifications WHERE time_cleared IS NULL
        $options = array(
            'fields' => array('MembershipStatusNotification.member_id'),
            'conditions' => array('MembershipStatusNotification.time_cleared' => null),
            );

        return array_values($this->find('list', $options)); // array(member_id, ...
    }
/**
 * Create a new notification record against a member and account
 * 
 * @param  int $memberId member to issue notification to 
 * @param  int $accountId account id to go on record
 * @return bool
 */
    public function issueNotificationForMember($memberId, $accountId)
    {
        // INSERT INTO membership_status_notifications (member_id, account_id) SET ($memberId, $accountId)
        $toSave = array(
            'MembershipStatusNotification' => array(
                'member_id' => $memberId,
                'account_id' => $accountId,
                ),
            );

        $this->create();
        return $this->save($toSave);
    }

/**
 * For a given member clear and current notifications with a reason of PAYMENT
 * @param  int $memberId member id 
 * @return bool           pass/fail
 */
    public function clearNotificationsByPaymentForMember($memberId)
    {
        // UPDATE membership_status_notifications SET time_cleared = CURENT_TIMESTAMP, cleared_reason = MembershipStatusNotification::PAYMENT WHERE member_id IS $memberId and time_cleared IS NULL 
        $db = $this->getDataSource();
        $reson = $db->value(MembershipStatusNotification::PAYMENT, 'string');
        $fields = array(
            'MembershipStatusNotification.cleared_reason' => $reson,
            'MembershipStatusNotification.time_cleared' => 'NOW()', 
            );
        $conditions = array(
            'MembershipStatusNotification.member_id' => $memberId,
            'MembershipStatusNotification.time_cleared' => NULL,
         );

        return $this->updateAll($fields, $conditions);
    }

/**
 * For a given member clear any current notifications with a reason of REVOKE
 * @param  int $memberId member id
 * @return bool           pass/fail
 */
    public function clearNotificationsByRevokeForMember($memberId)
    {
        // UPDATE membership_status_notifications SET time_cleared = CURENT_TIMESTAMP, cleared_reason = MembershipStatusNotification::REVOKED WHERE member_id IS $memberId and time_cleared IS NULL   
        $db = $this->getDataSource();
        $reson = $db->value(MembershipStatusNotification::REVOKED, 'string');
        $fields = array(
            'MembershipStatusNotification.cleared_reason' => $reson,
            'MembershipStatusNotification.time_cleared' => 'NOW()', 
            );
        $conditions = array(
            'MembershipStatusNotification.member_id' => $memberId,
            'MembershipStatusNotification.time_cleared' => NULL,
         );

        return $this->updateAll($fields, $conditions); 
    }    

}