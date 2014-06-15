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
 * @package       plugins.Tools.Config
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


Router::parseExtensions('json');

Router::connect('/tools', array('plugin' => 'Tools', 'controller' => 'ToolsTools', 'action' => 'index'));
Router::connect('/tools/:action/*', array('plugin' => 'Tools', 'controller' => 'ToolsTools'));
