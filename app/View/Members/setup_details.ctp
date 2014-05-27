<!-- File: /app/View/Member/setup_details.ctp -->

<?php
	echo $this->Html->css('google_address_autofill.css');
	echo $this->Html->script('https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places');
	echo $this->Html->script('google_address_autofill.js');

	echo("<p>Nottingham Hackspace is an incorporated non-profit, run entirely by members. As such, we have to maintain a membership register for inspection by Companies House. Any information you provide won't be used for anything other than hackspace business, and cetainly won't be passed on or sold to any third parties.</p>");
	echo("<p>By submitting address information below, you'll receive instructions on how to set up a standing order for hackspace membership.</p>");

	echo $this->Form->create('Member');
	echo '<div id="address_map"></div>';
	echo $this->Form->hidden('member_id');
	echo $this->Form->input('address_1', array( 'label' => 'Address part 1 (House name/number and street)' ) );
	echo $this->Form->input('address_2', array( 'label' => 'Address part 2' ) );
	echo $this->Form->input('address_city', array( 'label' => 'City' ) );
	echo $this->Form->input('address_postcode', array( 'label' => 'Postcode' ) );

	echo $this->Form->input('contact_number' );

	echo $this->Form->end('Submit');
?>