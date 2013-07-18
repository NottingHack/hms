<?php

Router::parseExtensions('json');

Router::connect('/membervoice/ideas', array('plugin' => 'MemberVoice', 'controller' => 'MVIdeas', 'action' => 'index'));
Router::connect('/membervoice/ideas/:action/*', array('plugin' => 'MemberVoice', 'controller' => 'MVIdeas'));

Router::connect('/membervoice/comments', array('plugin' => 'MemberVoice', 'controller' => 'MVComments', 'action' => 'index'));
Router::connect('/membervoice/comments/:action/*', array('plugin' => 'MemberVoice', 'controller' => 'MVComments'));

?>