<p>
	Hello!
</p>

<p>
	If you'd like to become a Nottingham Hackspace member, the first step is is to <?php echo $this->Html->link('create an HMS account', array('controller' => 'members', 'action' => 'setupLogin', $memberId, 'full_base' => true)); ?>.
</p>

<p>
	After creating yourself some login details, you'll be asked to fill in some more information about yourself, namely your address and a number we can contact you on, don't worry, we won't share this information with anyone unless legally obliged to do so.
</p>

<p>
	Once you've filled in your details, one of our member admins will be notified, they'll give your information a quick check and if all is well, they'll move your membership on to the next stage. This is the part where you get the Nottingham Hackspace bank details, you'll also get a unique payment reference for your account (please use this if possible), use these details to set up a standing order for your membership fee. Membership is pay-what-you-like, and you can always change the amount you're paying if you find yourself using the space more or less than you first thought.
</p>

<p>
	When the standing order is set-up, it's time to wait, once your payment shows up in our bank account (currently this is a manual process so it may take 3 to 4 days) you'll be contacted directly by one of the member admins to arrange first-access, this is where you're given the door codes and your RFID card to get in to the space. Once that's done, you are free to visit at any time.
</p>

<p>
	A few important details: Nottingham Hackspace is incorporated as a non-profit company, registration number 07766826. Everyone who works on stuff for the hackspace is a volunteer; the hackspace has no staff, just members. So far, it has also been entirely funded and is self-sustaining through members contributions rather than grants.
</p>

<p>
	Here's the URL for the public google group:<br>
	<?php echo $this->Html->link($groupLink); ?>
</p>

<p>
	If you have any questions, just email.
</p>

<p>
	Thanks,<br>
	Nottinghack Member Admin Team
</p>