<?php

App::uses('AppHelper', 'View/Helper');

# Helper to display currency in a nice way
class CurrencyHelper extends AppHelper {

	var $helpers = array('Number');

	public function output($pennys)
	{
		$output = '';
		$moneyStr = $this->Number->currency($pennys / 100, 'GBP', array( 'places' => 2, 'negative' => '-') );
		if($pennys < 0)
		{
			$output = sprintf('<span class="currency_negative">%s</span>', $moneyStr);
		}
		else
		{
			$output = $moneyStr;
		}

		return $output;
	}
}

?>
