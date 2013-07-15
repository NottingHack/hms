<?php

Router::parseExtensions('json');

Router::connect('/membervoice/ideas', array('plugin' => 'MemberVoice', 'controller' => 'MVIdeas', 'action' => 'index'));
Router::connect('/membervoice/ideas/:action/*', array('plugin' => 'MemberVoice', 'controller' => 'MVIdeas'));



?>