<p>
	Hello,
</p>

<p>
	Thanks for becoming a member of Nottingham Hackspace. Here are all of the remaining details you need, though there's one last step before you'll have full 24 hour access.
</p>

<p>
	GateKeeper is our RFID entry system, for which you need a suitable card set up. If you have a CityCard, Nottingham Library card, or Oyster Card, it will work with it, otherwise, we have blank RFIDs for a £1 deposit. Please contact someone from the membership team to arrange a meeting, or visit on the next open hack night and ask people to point you to one of the team.
</p>

<p>
	Here's a calendar showing when people from the membership team could potentially meet you at the space:
	<?php echo $this->Html->link('https://www.google.com/calendar/embed?src=bmnnboh643lt5gl0b2ku45c4p0%40group.calendar.google.com&ctz=Europe/London'); ?>
</p>

<p>
	NOTE: This doesn't indicate when they *will* be at the space, just when they could potentially meet you to do gatekeeper setup and answer any other questions you might have. Switch to week view to see hours in which people can be available. If there are any issues please contact the member admin team at membership@nottinghack.org.uk
</p>

<p>
	Here's a PDF hackspace manual:<br>
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
	Here again is the google group:<br>
	<?php echo $this->Html->link('https://groups.google.com/group/nottinghack?hl=en'); ?>
</p>

<p>
	and the hackspace rules:<br>
	<?php echo $this->Html->link('http://wiki.nottinghack.org.uk/wiki/Category:Rules'); ?>
</p>

<p>
	If you have any questions, feel free to email: membership@nottinghack.org.uk
</p>

<p>
	Thanks,<br>
	Nottinghack Member Admin Team
</p>