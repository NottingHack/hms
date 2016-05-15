<p>
	Hello <?php echo $name; ?>,
</p>

<p>
	Thanks for becoming a member of Nottingham Hackspace. Here are all of the remaining details you need, though there's one last step before you'll have full 24 hour access.
</p>

<p>
	GateKeeper is our RFID entry system for which you need a suitable card set up, which we provide. Visit on the next open hack night and ask people to point you to one of the membership team, or please contact membership@nottinghack.org.uk if you are unable to attend one.
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
	<?php echo $this->Html->link($groupLink); ?>
</p>

<p>
	The hackspace rules:<br>
	<?php echo $this->Html->link($rulesHTML); ?>
</p>

<p>
	We also have a wiki, with lots of information about the tools in the Hackspace:<br>
	<?php echo $this->Html->link($wikiLink); ?>
</p>

<p>
	If you have any questions, feel free to email: <?php echo $membershipEmail; ?>
</p>

<p>
	Thanks,<br>
	Nottinghack Membership Team
</p>
