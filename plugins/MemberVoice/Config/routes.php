<?php

Router::parseExtensions('json');

Router::connect('/membervoice/ideas', array('plugin' => 'MemberVoice', 'controller' => 'MemberVoiceIdeas', 'action' => 'index'));
Router::connect('/membervoice/ideas/:action/*', array('plugin' => 'MemberVoice', 'controller' => 'MemberVoiceIdeas'));

Router::connect('/membervoice/comments', array('plugin' => 'MemberVoice', 'controller' => 'MemberVoiceComments', 'action' => 'index'));
Router::connect('/membervoice/comments/:action/*', array('plugin' => 'MemberVoice', 'controller' => 'MemberVoiceComments'));

?>