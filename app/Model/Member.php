<?php
	class Member extends AppModel {

		public $primaryKey = 'member_id';

		public $belongsTo =  array(
				"Status" => array(
						"className" => "Status",
						"foreignKey" => "member_status",
						"type" => "inner"
				),
		);

		public $hasOne = array(
	        'Pin' => array(
	            'className'    => 'Pin',
	            'dependent'    => true
	        ),
	        'MemberAuth' => array(
	            'className'    => 'MemberAuth',
	            'dependent'    => true
	        ),
	    );

		public $hasAndBelongsToMany = array(
	        'Group' =>
	            array(
	                'className'              => 'Group',
	                'joinTable'              => 'member_group',
	                'foreignKey'             => 'member_id',
	                'associationForeignKey'  => 'grp_id',
	                'unique'                 => true,
	                'conditions'             => '',
	                'fields'                 => '',
	                'order'                  => '',
	                'limit'                  => '',
	                'offset'                 => '',
	                'finderQuery'            => '',
	                'deleteQuery'            => '',
	                'insertQuery'            => ''
	            )
	    );

		public $validate = array(
	        'name' => array(
	            'rule' => 'notEmpty'
	        ),
	        'email' => array(
	        	'email'
	        ),
	        'handle' => array(
	            'rule' => 'notEmpty'
	        )
	    );


		public function beforeSave($options = array()) {
			# Have to do a few things before we save

			if( isset( $this->data['Member'] ) &&
				isset( $this->data['Member']['member_number'] ) === false &&
				$this->data['Member']['member_status'] == 2)
			{
				# We're setting this member to be a 'current member' for the first time, need to modify some things

				# Set the member number and join date
				# Member number is totally fucked up with hard-coded entries and missing entries, so we need to find what the highest number is
				$highestMemberNumber = $this->find( 'first', array( 'conditions' => array( 'Member.member_number !=' => null),  'order' => 'Member.member_number DESC', 'fields' => 'Member.member_number' ) );
				$this->data['Member']['member_number'] = $highestMemberNumber['Member']['member_number'] + 1;
				$this->data['Member']['join_date'] = date( 'Y-m-d' );
				# Group 2 is for current members
				$this->data['Group']['group_id'] = 2;
			}

			return true;
		}
	}
?>