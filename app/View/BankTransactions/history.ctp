<!-- File: /app/View/BankTransactions/history.ctp -->

<?php
    $this->Html->addCrumb('Members', '/members');
    $this->Html->addCrumb('Membership Payments', '/banktrasnactions/uploadCsv');
?>
<table>
	<tr>
		<th>Date</th>
		<th>Amount</th>
	</tr>
<?php
	foreach ($bankTransactionsList as $transaction)
	{
		echo "\t<tr>\n";
		echo "\t\t<td>" . $transaction['Transactions']['transaction_datetime'] . "</td>\n";
		echo "\t\t<td>" . $this->Currency->output($transaction['Transactions']['amount']) . "</td>\n";
		echo "\t</tr>\n";
	}
echo "</table>\n";

if(count($bankTransactionsList) == 0)
{
	echo "No payments found!<br />\n";
}

?>


<div class="paginate">
	<?php echo $this->Paginator->numbers(); ?>

</div>
