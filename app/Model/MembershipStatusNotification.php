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
    const PAYMENT = "PAYMENT";

/**
 * The Notification was cleared when the membership was revoked
 */
    const REVOKE = "REVOKE";

/**
 * The Notification was cleared manually, likely due to audit issues
 */
    const MANUAL = "MANUAL";

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
 * Specify 'belongs to' associations.
 * @var array
 */ 
    // not actually needed for how we a munging the data
    // public $belongsTo = array(
    //         'AccountMSN' => array(
    //             'className' => 'Account',
    //             'foreignKey' => 'account_id',
    //         ),
    //         'MemberMSN' => array(
    //             'className' => 'Member',
    //             'foreignKey' => 'member_id',
    //         ),
    // );

/**
 * fetch array of memberIds with a current issued warnings
 * @return array of memberIds
 */
    public function listAllMembersWithNotifications()
    {
        return ; // array(member_id, ...)
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

    }

/**
 * For a given member clear and current notifications with a reason of PAYMENT
 * @param  int $memberId member id 
 * @return bool           pass/fail
 */
    public function clearNotificationsByPaymentForMember($memberId)
    {
        
    }

/**
 * For a given member clear any current notifications with a reason of REVOKE
 * @param  int $memberId member id
 * @return bool           pass/fail
 */
    public function clearNotificationsByRevokeForMember($memberId)
    {
        
    }    

}