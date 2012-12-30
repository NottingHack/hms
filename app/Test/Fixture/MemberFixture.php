<?php

class MemberFixture extends CakeTestFixture 
{
	public $useDbConfig = 'test';
	public $import = 'Member';

	public $records = array(
		array('member_id' => 1, 'name' => 'Mathew Pryce', 		'email' => 'm.pryce@example.org', 			'join_date' => '2012-12-05', 'handle' => 'thudinfatuated', 			'unlock_text' => 'Back again?', 		'balance' => -200, 	'credit_limit' => 5000, 'member_status' => 1, 'username' => 'strippingdemonic', 	'account_id' => null, 'address_1' => '7 Pegelm Gardens', 				'address_2' => 'Hornchurch', 	'address_city' => 'Greater London', 	'address_postcode' => 'RM11 3NU', 	'contact_number' => '077 2574 6392'),
		array('member_id' => 2, 'name' => 'Annabelle Santini', 	'email' => 'a.santini@hotmail.com', 		'join_date' => '2011-02-24', 'handle' => 'mammetwarpsgrove', 		'unlock_text' => 'Welcome Annabelle', 	'balance' => 0, 	'credit_limit' => 5000, 'member_status' => 2, 'username' => 'pecanpaella', 			'account_id' => null, 'address_1' => '1 Saint Paul\'s Church Yard', 	'address_2' => 'The City', 		'address_city' => 'London', 			'address_postcode' => 'EC4M 8SH', 	'contact_number' => '077 1755 4342'),
		array('member_id' => 3, 'name' => 'Guy Viles', 			'email' => 'g.viles@gmail.com', 			'join_date' => '2010-08-18', 'handle' => 'doyltcameraman', 			'unlock_text' => 'Sup Guy', 			'balance' => -985, 	'credit_limit' => 5000, 'member_status' => 3, 'username' => 'buntweyr', 			'account_id' => null, 'address_1' => '4 Fraser Crescent', 				'address_2' => '', 				'address_city' => 'Portree', 			'address_postcode' => 'IV51 9DR', 	'contact_number' => '077 7181 0959'),
		array('member_id' => 4, 'name' => 'Kelly Savala', 		'email' => 'k.savala@yahoo.co.uk', 			'join_date' => '2010-09-22', 'handle' => 'bildestonelectrician', 	'unlock_text' => 'Hey Kelly', 			'balance' => -5649, 'credit_limit' => 5000, 'member_status' => 4, 'username' => 'huskycolossus', 		'account_id' => null, 'address_1' => '8 Elm Close', 					'address_2' => 'Tetsworth', 	'address_city' => 'Thame', 				'address_postcode' => 'OX9 7AP', 	'contact_number' => '079 0644 8720'),
		array('member_id' => 5, 'name' => 'Jessie Easterwood', 	'email' => 'j.easterwood@googlemail.com', 	'join_date' => '2010-09-22', 'handle' => 'dailyponcy', 				'unlock_text' => 'Oh dear...', 			'balance' => -3465, 'credit_limit' => 5000, 'member_status' => 5, 'username' => 'chollertonbanker', 	'account_id' => null, 'address_1' => '9 Langton Avenue', 				'address_2' => 'East Calder', 	'address_city' => 'Livingston', 		'address_postcode' => 'EH53 0DR', 	'contact_number' => '070 0036 0548'),
		array('member_id' => 6, 'name' => 'Guy Garrette', 		'email' => 'g.garratte@foobar.org', 		'join_date' => '2010-09-22', 'handle' => 'jamspare', 				'unlock_text' => 'Look behind you!', 	'balance' => -2, 	'credit_limit' => 5000, 'member_status' => 6, 'username' => 'jaytatterdemalion',	'account_id' => null, 'address_1' => '13 Market Street', 				'address_2' => 'Mossley', 		'address_city' => 'Ashton-under-Lyne', 	'address_postcode' => 'OL5 0ES', 	'contact_number' => '078 3837 0502'),
	);
}

?>