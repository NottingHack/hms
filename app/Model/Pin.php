<?php
	class Pin extends AppModel {
		
		public $useTable = "pins";

		public $primaryKey = 'pin_id';

	    public $belongsTo = array(
	    	"Member" => array(
						"className" => "Member",
						"foreignKey" => "member_id",
						"type" => "inner"
			)
    	);

	    public $validate = array(
	        'pin' => array(
	            'rule' => 'notEmpty'
	        ),
	        'member_id' => array(
	        	'rule' => 'notEmpty'
	        ),
	        'unlock_text' => array(
	            'rule' => 'notEmpty'
	        )
	    );

		public static function generate_pin()
		{
			# Currently a PIN is a 4 digit number between 1000 and 9999
			return rand(1000, 9999);
		}

		public function generate_unique_pin()
		{
			# A loop hiting the database? Why not...
			$pin = 0;
			do
			{
				$pin = Pin::generate_pin();
			} while ( $this->find( 'count', array( 'conditions' => array( 'Pin.pin' => $pin ) ) ) > 0 );

			return $pin;
		}
	}
?>