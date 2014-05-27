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
		$this->Html->meta('icon', null, array('inline' => false));

		$this->Html->css('hms', null, array('inline' => false));

		/* Set up jQuery UI.  We'll include jQuery later using a conditional */
		$this->Html->script('jquery-ui-1.10.3.custom.min', array('inline' => false));

		$this->Html->css('jquery/jquery-ui-1.10.3.custom.min.css', null, array('inline' => false));

		echo $this->fetch('meta');
		echo("\n");
		echo $this->fetch('css');
		echo("\n");
		/* Now actual jQuery */

		$jQuery1 = Router::url('/') . JS_URL . 'jquery-1.10.2.min.js';
		$jQuery2 = Router::url('/') . JS_URL . 'jquery-2.0.3.min.js';
	?>
		<!--[if lt IE 9]>
		    <script type="text/javascript" src="<?php echo($jQuery1); ?>"></script>
		<![endif]-->
		<!--[if gte IE 9]><!-->
		    <script type="text/javascript" src="<?php echo($jQuery2); ?>"></script>
		<!--<![endif]-->
	<?php
		echo $this->fetch('script');
		echo("\n");
	?>
</head>
<body>
	<div id="container">
		<div id="header">
			<div class="banner"></div>
			<div class="userBar">
				<div class="login">
					<?php if( isset($memberId)): ?>
						Logged in as: 
						<?php echo $this->Html->link(isset($username) ? $username : $email, array( 'plugin' => null, 'controller' => 'members', 'action' => 'view', $memberId ) ); ?>
						<span class="loginSpace">
							<?php echo $this->Html->link('Logout', array( 'plugin' => null, 'controller' => 'members', 'action' => 'logout' )) ?>
						</span>
					<?php else: ?>
						Already a member? 
						<?php echo $this->Html->link('Login', array( 'plugin' => null, 'controller' => 'members', 'action' => 'login' )) ?>
					<?php endif; ?>
				</div>
				<?php if( isset($adminNav) && count($adminNav) > 0 ): ?>
					<div class="adminNav">
						<ul>
							<?php 
								foreach ($adminNav as $text => $options) {
									echo '<li>';
									echo $this->Html->link($text, $options);
									echo '</li>';
								}
							?>
						</ul>
					</div>
					<div class="clear"></div>
				<?php endif; ?>
				<?php if( isset($userMessage) && count($userMessage) > 0 ): ?>
					<div class="userMessage">
						<ul>
							<?php 
								foreach ($userMessage as $text => $options) {
									echo '<li>';
									echo $this->Html->link($text, $options);
									echo '</li>';
								}
							?>
						</ul>
					</div>
					<div class="clear"></div>
				<?php endif; ?>
			</div>
		</div>
		<div id="content">
			<div id="crumb">
				<?php echo $this->Html->getCrumbs(' > ', 'Home'); ?>
			</div>
			<?php echo str_replace('\n', '</br>', $this->Session->flash()); ?>
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
					<ul class="xoxo">
						<li id="menu-item-328" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-328">HMS Version <?php echo $version; ?></li>
						<li id="menu-item-327" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-327"><a href="https://github.com/NottingHack/hms">Get Source</a></li>
						<li id="menu-item-329" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-329"><a href="<?php echo(Router::url('/')); ?>pages/credits">Credits</a></li>
						<li id="menu-item-330" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-330"><a href="http://nottinghack.org.uk/">Nottinghack Website</a></li>
						<li id="menu-item-332" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-332">&#169; <?php echo date('Y'); ?> Nottinghack</li>
					</ul>
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
