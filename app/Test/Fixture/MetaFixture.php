<?php
    
    class MetaFixture extends CakeTestFixture
    {
        public $useDbConfig = 'test';
        public $import = 'Meta';
        
        public $records = array(
                                array('name' => 'member_box_cost', 'value' => '-500'),
                                array('name' => 'member_box_individual_limit', 'value' => '3'),
                                array('name' => 'member_box_limit', 'value' => '129'),
                                array('name' => 'label_printer_ip', 'value' => 'localhost'),
                                array('name' => 'so_accountNumber', 'value' => '19098596'),
                                array('name' => 'so_sortCode', 'value' => '60-24-77'),
                                array('name' => 'so_accountName', 'value' => 'Nottinghack'),
                                array('name' => 'members_guide_html', 'value' => 'http://guide.nottinghack.org.uk'),
                                array('name' => 'members_guide_pdf', 'value' => 'http://readthedocs.org/projects/nottingham-hackspace-members-guide/downloads/pdf/latest/'),
                                array('name' => 'rules_html', 'value' => 'http://rules.nottinghack.org.uk'),
                                array('name' => 'access_street_door', 'value' => '1234'),
                                array('name' => 'access_inner_door', 'value' => '1234'),
                                array('name' => 'access_wifi_ssid', 'value' => 'HSNOTTS'),
                                array('name' => 'access_wifi_password', 'value' => '123456'),
                                array('name' => 'membership_email', 'value' => 'membership@localhost'),
                                );
    }
    
    ?>