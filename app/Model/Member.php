<?php
	class Member extends AppModel {

		public $primaryKey = 'member_id';

		public $belongsTo =  array(
				"Status" => array(
						"className" => "Status",
						"foreignKey" => "member_status",
						"type" => "inner"
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

			# Check to see if this is a new record, or an edit
			if( isset($this->id) === false )
			{
				# New record, set the member status and the like
				$this->data['Member']['member_status'] = 1;
				$this->data['Member']['unlock_text'] = 'Welcome ' . $this->data['Member']['handle'];
			}

			return true;
		}
	}
?>