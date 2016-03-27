<p>
	Hello,
</p>

<p>
	Thanks for becoming a member of Nottingham Hackspace. Here are all of the remaining details you need, though there's one last step before you'll have full 24 hour access.
</p>

<p>
	GateKeeper is our RFID entry system for which you need a suitable card set up, which we provide. Visit on the next open hack night and ask people to point you to one of the membership team, or please contact membership@nottinghack.org.uk if you are unable to attend one.
</p>

<p>
	Here's a PDF hackspace manual, it can also be found at guide.nottinghack.org.uk<br>
	<?php echo $manLink; ?>
</p>

<p>
	In terms of access, the street door code is <?php echo $outerDoorCode; ?>, and all other doors, including the doors in the stairwell and studio, are <?php echo $innerDoorCode; ?>. Obviously, please do not share these with non-members.
</p>

<p>
	Wifi access:<br>
	SSID: <?php echo $wifiSsid; ?><br>
	Pass: <?php echo $wifiPass; ?><br>
</p>

<p>
	Our Google Group is where a lot of online discussion takes place:<br>
	<?php echo $this->Html->link('https://groups.google.com/group/nottinghack?hl=en'); ?>
</p>

<p>
	The hackspace rules:<br>
	<?php echo $this->Html->link('http://rules.nottinghack.org.uk'); ?>
</p>

<p>
	We also have a wiki, with lots of information about the tools in the Hackspace:<br>
	<?php echo $this->Html->link('http://wiki.nottinghack.org.uk/wiki'); ?>
</p>

<p>
	If you have any questions, feel free to email: membership@nottinghack.org.uk
</p>

<p>
	Thanks,<br>
	Nottinghack Membership Team
</p>
