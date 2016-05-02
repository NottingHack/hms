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
 * @package       dev.Setup.Updates
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$conn = $this->__getDbConnection('default', true);

$this->__logMessage('Update to MemberBoxes table');

$this->__logMessage('Update `member_boxes` table.');
$query = "ALTER TABLE  `member_boxes` CHANGE  `brought_date`  `bought_date` DATE NOT NULL;";
$this->__runQuery($conn, $query);

$this->__logMessage('new entries into `hms_meta` table.');
    
    // load hms settings file
    $hmsSettings = '../../../app/Config/hms.php';
    if (file_exists(makeAbsolutePath($hmsSettings))) {
        include ($hmsSettings);
    }
    
$query = "INSERT INTO `hms_meta` (`name`, `value`) VALUES
    ('label_printer_ip', $config['hms_label_printer_ip']),
    ('so_accountNumber', $config['hms_so_accountNumber']),
    ('so_sortCode', $config['hms_so_sortCode']),
    ('so_accountName', $config['hms_so_accountName']),
    ('members_guide_html', 'http://guide.nottinghack.org.uk'),
    ('members_guide_pdf', 'http://readthedocs.org/projects/nottingham-hackspace-members-guide/downloads/pdf/latest/'),
    ('rules_html', 'http://rules.nottinghack.org.uk'),
    ('access_street_door', $config['hms_access_street_door']),
    ('access_inner_door', $config['hms_access_inner_door']),
    ('access_wifi_ssid', $config['hms_access_wifi_ssid']),

    ('access_wifi_password', $config['hms_'access_wifi_password'),
    ('membership_email', $config['hms_membership_email'])
    ;
    ";
$this->__runQuery($conn, $query);
