<?php

	class ConsumableAreaFixture extends CakeTestFixture 
	{
		public $useDbConfig = 'test';
		public $import = 'ConsumableArea';

		public $records = array(
			array( 'area_id' => 1, 'name' => 'a', 'description' => 'a' ),
			array( 'area_id' => 2, 'name' => 'b', 'description' => 'b' ),
		);
	}

?>