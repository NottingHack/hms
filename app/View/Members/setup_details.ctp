<!-- File: /app/View/Member/setup_details.ctp -->

<?php
	echo $this->Form->create('Member');
	echo $this->Form->hidden('member_id');

	echo("<p>Nottingham Hackspace is an incorporated non-profit, run entirely by members. As such, we have to maintain a membership register for inspection by Companies House. Any information you provide won't be used for anything other than hackspace business, and cetainly won't be passed on or sold to any third parties.</p>");
	echo("<p>By submitting address information below, you'll receive instructions on how to set up a standing order for hackspace membership.</p>");

	echo $this->Form->input('address_1', array( 'label' => 'Address part 1 (House name/number and street)' ) );
	echo $this->Form->input('address_2', array( 'label' => 'Address part 2' ) );
	echo $this->Form->input('address_city', array( 'label' => 'City' ) );
	echo $this->Form->input('address_postcode', array( 'label' => 'Postcode' ) );

	echo $this->Form->input('contact_number' );

	echo $this->Form->end('Update');
?>