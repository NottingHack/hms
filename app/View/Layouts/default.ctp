<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       Cake.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

$cakeDescription = "Nottingham Hackspace Management System";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
		<?php echo $cakeDescription ?> -
		<?php echo $title_for_layout; ?>
	</title>
	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css('hms');

		echo $this->fetch('meta');
		echo $this->fetch('css');
		echo $this->fetch('script');
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<div class="banner"></div>
			<div class="userBar">
				<div class="login">
					<?php if(AuthComponent::user()): ?>
						Logged in as: 
						<?php echo $this->Html->link(AuthComponent::user('Member.name'), array( 'controller' => 'members', 'action' => 'view', AuthComponent::user('Member.member_id') ) ); ?>
						<span class="loginSpace">
							<?php echo $this->Html->link('Logout', array( 'controller' => 'members', 'action' => 'logout' )) ?>
						</span>
					<?php else: ?>
						<?php echo $this->Html->link('Login', array( 'controller' => 'members', 'action' => 'login' )) ?>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div id="content">
			<div id="crumb">
				<?php echo $this->Html->getCrumbs(' > ', 'Home'); ?>
			</div>
			<?php echo $this->Session->flash(); ?>

			<?php echo $this->fetch('content'); ?>
			<?php
				# AT [16/09/2012] Use the NavHelper to render any navigation links
				if(	isset($navLinks) && 
					count($navLinks) > 0)
				{
					echo $this->Nav->output($navLinks); 
				}
			?>
		</div>
		<div id="footer">
			<div id="footer-widget-area" role="complementary">
				<div id="first" class="widget-area">
				</div><!-- #first .widget-area -->

				<div id="second" class="widget-area">
					<ul class="xoxo">
						<li id="nav_menu-4" class="widget-container widget_nav_menu"><div class="menu-bottom-follow-container"><ul id="menu-bottom-follow" class="menu"><li id="menu-item-329" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-329"><a href="http://twitter.com/#!/hsnotts">twitter</a></li>
						<li id="menu-item-328" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-328"><a href="http://groups.google.com/group/nottinghack">google group</a></li>
						<li id="menu-item-327" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-327"><a href="http://www.flickr.com/photos/nottinghack">flickr</a></li>
						<li id="menu-item-330" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-330"><a href="http://www.youtube.com/user/nottinghack">youtube</a></li>
						<li id="menu-item-332" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-332"><a href="http://www.facebook.com/pages/NottingHack/106946729335123">facebook</a></li>
					</ul>
				</div>
				</div><!-- #second .widget-area -->

				<div id="third" class="widget-area">
					<ul class="xoxo">
						<li id="text-6" class="widget-container widget_text">
							<div class="textwidget">
								<div style="text-size: 0.8em">
									Nottingham Hackspace Ltd<br />
									<br />
									<br />
									No. 07766826<br />
									Reg. in England & Wales
								</div>
							</div>
						</li>
					</ul>
				</div><!-- #third .widget-area -->

				<div id="fourth" class="widget-area">
					<ul class="xoxo">
						<li id="text-5" class="widget-container widget_text">			
							<div class="textwidget">Unit F6 BizSpace<br />
							Roden House Business Centre<br />
							Nottingham<br />
							NG3 1JH</div>
						</li>
					</ul>
				</div><!-- #fourth .widget-area -->

			</div><!-- #footer-widget-area -->
		</div>
	</div>
	<?php echo $this->element('sql_dump'); ?>
</body>
</html>
