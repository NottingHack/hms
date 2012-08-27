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
	        )
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

			if( isset( $this->data['Member']['member_number'] ) === false &&
				$this->data['Member']['member_status'] == 2)
			{
				# We're setting this member to be a 'current member' for the first time, need to modify some things

				# Set the member number and join date
				$this->data['Member']['member_number'] = $this->find( 'count', array( 'conditions' => array( 'Member.member_number !=' => null ) ) );
				$this->data['Member']['join_date'] = date( 'Y-m-d' );
				$this->data['Group']['group_id'] = 2;

				print_r($this->data);

				return false;
			}

			return true;
		}
	}
?>