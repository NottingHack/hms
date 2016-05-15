<p>
    Hello Finance Team,
</p>
<p>
    I was unable to match the following transactions to any payment refrence or member account.
</p>
<table border="1" style="width:100%">
        <tr>
            <th>Date</th>
            <th>Description</th>
            <th>Amount</th>
            <th>Bank</th>
        </tr>
<?php foreach ($transactions as $transaction): ?>
        <tr>
            <td><?php echo $transaction['transaction_date']; ?></td>
            <td><?php echo $transaction['description']; ?></td>
            <td><?php echo $transaction['amount']; ?></td>
            <td><?php echo $transaction['bank_id']; ?></td>
        </tr>
<?php endforeach; ?>
</table>
<p>
    Please help me,<br/>
    HMS
</p>
