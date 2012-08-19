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
			if( isset($this->data->id) === false )
			{
				# New record, set the member status and the like
				$this->data['Member']['member_status'] = 1;
				$this->data['Member']['unlock_text'] = 'Welcome ' . $this->data['Member']['handle'];
			}

			return true;
		}

		public function afterSave(boolean $created) {
			# Todo: e-mail those that need e-mailing
		}
	}
?>