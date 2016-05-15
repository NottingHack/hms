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
 * Model for all access log data
 */
class AccessLog extends AppModel {

/**
 * Specify the table to use
 * @var string
 */
    public $useTable = "access_log";

/**
 * Specify the primary key.
 * @var string
 */
    public $primaryKey = 'access_id';

/** 
 * Find last access for an array of memberIds
 *
 * @param array $memberIds
 * @return mixed date time or null
 */
    public function getLastAccessForMembers($memberIds) {
        $accessTimes = array();
        foreach($memberIds as $memberId) {
           $time = $this->getLastAccessForMember($memberId);
            
            $accessTimes[$memberId] = $time;
        }
        return $accessTimes;
    }

/** 
 * Find last access for memberId
 *
 * @param int $memberId
 * @return mixed date time or null
 */
    public function getLastAccessForMember($memberId) {
        $result = $this->find('first' , array(
            'conditions' => array('AccessLog.member_id' => $memberId),
            'fields' => array('AccessLog.access_time'),
            'order' => 'AccessLog.access_time DESC'
            )
        );

        return Hash::get($result, 'AccessLog.access_time');
    }

}