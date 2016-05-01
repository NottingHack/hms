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
 * @package       app.Controller.Component
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Component', 'Controller');
App::uses('PhpReader', 'Configure');
App::uses('String', 'Utility');

/**
 * LabelPrinterComponent is a component to handle printing to a network attached zebra printer.
 */
class LabelPrinterComponent extends Component {

/**
 * priter port
 *
 */
    private $port = '9100';
    
/**
 * Print a label
 * thanks to http://stackoverflow.com/a/15956807
 *
 * @param string $templateName
 * @param array $substitutions
 * @return bool
 */
    public function printLabel($templateName, $substitutions = array()) {
        
        $this->LabelTemplate = ClassRegistry::init('LabelTemplate');
        $this->Meta = ClassRegistry::init('Meta');
        
        $template = $this->LabelTemplate->getTemplate($templateName);
        if ($template == null) {
            return false;
        }
        
        
        $label = String::insert($template, $substitutions);
        
        // Get the IP address for the printer.
        $host = $this->Meta->getValueFor('label_printer_ip');
        
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if ($socket === false) {
            return false;
        }
        
        $result = socket_connect($socket, $host, $this->port);
        if ($result === false) {
            return false;
        }
        
        socket_write($socket, $label, strlen($label));
        socket_close($socket);
        
        return true;
    }

}
