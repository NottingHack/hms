<!-- File: /app/View/BankTransactions/history.ctp -->

<?php
    $this->Html->addCrumb('Members', '/members');
    $this->Html->addCrumb(isset($member['username']) ? $member['username'] : $member['email'], '/members/view/' . $member['id']);
    $this->Html->addCrumb('Membership Payments', '/banktransactions/history/' . $member['id']);
?>
<?php if (isset($lastTrasaction)) { ?>
<dl>
<dt>Last Payment Date</dt>
<dd><?php echo $lastTrasaction['transaction_date']; ?></dd>
<dt>Bank</dt>
<dd><?php echo $lastTrasaction['bank']; ?></dd>
</dl>
<?php } else { ?>
<table>
	<tr>
		<th>Date</th>
		<th>Amount</th>
        <th>Bank Account</th>
	</tr>
<?php
	foreach ($bankTransactionsList as $transaction)
	{
		echo "\t<tr>\n";
		echo "\t\t<td>" . $transaction['transaction_date'] . "</td>\n";
		echo "\t\t<td>" . $this->Currency->output($transaction['amount']*100) . "</td>\n";
		echo "\t\t<td>" . $transaction['bank'] . "</td>\n";
		echo "\t</tr>\n";
	}
echo "</table>\n";


if(count($bankTransactionsList) == 0)
{
	echo "No payments found!<br />\n";
}
} //end else
?>
<div class="paginate">
	<?php echo $this->Paginator->numbers(); ?>
</div>
