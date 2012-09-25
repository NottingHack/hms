<?php

App::uses('AppHelper', 'View/Helper');

# AT [16/09/2012] List exists to render a list items in a nice way
class ListHelper extends AppHelper {

	public function output($list, $delimeter = ', ', $noneText = 'None')
	{
		$numItems = count($list);
		if($numItems > 0)
		{
			$output = '';
			for($i = 0; $i < $numItems; $i++)
			{
				$output .= $list[$i];
				if($i < $numItems - 1)
				{
					$output .= $delimeter;
				}
			}
			return $output;
		}
		return $noneText;
	}
}

?>
