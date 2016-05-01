<p>
	Hello <?php echo $name; ?>,
</p>

<p>
	Thanks for reinstating your membership of Nottingham Hackspace. Here is a reminder of some details  you might needu need.
</p>

<p>
	GateKeeper is our RFID entry system for which you need a suitable card set up. If you still have a card form before your membership was deactivated you will be able to carry on using that, other wise a replacement card cost £1. Visit on the next open hack night and ask people to point you to one of the membership team, or please contact membership@nottinghack.org.uk if you are unable to attend one.
</p>

<p>
    The hackspace members guide can be found at <?php echo $this->Html->link($membersGuideHTML); ?> and it is a recommended read for all members. <br/>
    A PDF version is also available <?php echo $this->Html->link('here', $membersGuidePDF); ?>
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
	<?php echo $this->Html->link($rulesHTML); ?>
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