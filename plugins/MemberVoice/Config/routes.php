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
 * @package       plugins.MemberVoice.Config
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */


Router::parseExtensions('json');

Router::connect('/membervoice/ideas', array('plugin' => 'MemberVoice', 'controller' => 'MemberVoiceIdeas', 'action' => 'index'));
Router::connect('/membervoice/ideas/:action/*', array('plugin' => 'MemberVoice', 'controller' => 'MemberVoiceIdeas'));

Router::connect('/membervoice/comments', array('plugin' => 'MemberVoice', 'controller' => 'MemberVoiceComments', 'action' => 'index'));
Router::connect('/membervoice/comments/:action/*', array('plugin' => 'MemberVoice', 'controller' => 'MemberVoiceComments'));