<!-- File: /app/View/Member/setup_details.ctp -->

<?
	echo $this->Form->create('Member');
	echo $this->Form->hidden('member_id');

	echo $this->Form->input('address_1', array( 'label' => 'Address part 1 (House name/number and street)' ) );
	echo $this->Form->input('address_2', array( 'label' => 'Address part 2' ) );
	echo $this->Form->input('address_city', array( 'label' => 'City' ) );
	echo $this->Form->input('address_postcode', array( 'label' => 'Postcode' ) );

	echo $this->Form->input('contact_number' );

	echo $this->Form->end('Update');
?>