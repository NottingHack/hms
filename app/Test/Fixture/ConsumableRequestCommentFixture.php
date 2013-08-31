<?php

	class ConsumableRequestCommentFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'ConsumableRequestComment';

		public $records = array(
			array( 'request_comment_id' => 1, 'text' => 'a', 'member_id' => null, 'timestamp' => '2013-08-31 09:00:00', 'request_id' => 1 ),
			array( 'request_comment_id' => 2, 'text' => 'b', 'member_id' => 1, 'timestamp' => '2013-08-31 10:00:00', 'request_id' => 1 ),
			array( 'request_comment_id' => 3, 'text' => 'c', 'member_id' => 2, 'timestamp' => '2013-08-31 11:00:00', 'request_id' => 2 ),
		);
	}

?>