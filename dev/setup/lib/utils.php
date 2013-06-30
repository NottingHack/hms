<?php

	//! Given a relative path from this file, get an absolute path.
	/*!
		@param string $path The relative path to convert.
		@retval string The absolute path.
	*/
	function makeAbsolutePath($path)
	{
		if(count($path) > 0)
		{
			$firstChar = $path[0];
			if(	$firstChar != '/' &&
				$firstChar != '\\' )
			{
				$path = '/' . $path;
			}
		}
		return dirname(__FILE__) . $path;
	}

?>