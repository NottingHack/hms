<!-- File: /app/View/Snackspace/history.ctp -->

<?php
	$this->Html->addCrumb('Snackspace', '/snackspace/history');
?>
<dl>
	<dt>
		Balance
	</dt>
	<dd>
		<?php echo $this->Currency->output($balance); ?>
	</dd>
</dl>
<table>
	<tr>
		<th>Date</th>
		<th>Type</th>
		<th>Description</th>
		<th>Amount</th>
	</tr>
<?php
	foreach ($transactionsList as $transaction)
	{
		echo "\t<tr>\n";
		echo "\t\t<td>" . $transaction['Transactions']['transaction_datetime'] . "</td>\n";
		echo "\t\t<td>" . $transaction['Transactions']['transaction_type'] . "</td>\n";
		echo "\t\t<td>" . $transaction['Transactions']['transaction_desc'] . "</td>\n";
		echo "\t\t<td>" . $this->Currency->output($transaction['Transactions']['amount']) . "</td>\n";
		echo "\t</tr>\n";
	}
echo "</table>\n";

if(count($transactionsList) == 0)
{
	echo "No transactions found!<br />\n";	
}

?>


<div class="paginate">
	<?php echo $this->Paginator->numbers(); ?>

</div>
