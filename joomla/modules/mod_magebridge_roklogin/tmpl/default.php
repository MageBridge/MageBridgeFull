<?php
/**
 * Joomla! module MageBridge Login for RocketTheme templates
 *
 * @author Yireo (info@yireo.com)
 * @package MageBridge
 * @copyright Copyright 2015
 * @license GNU Public License
 * @link http://www.yireo.com/
 */
		
// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<?php if ($magebridge->hasPrototypeJs() == true) : ?>
<script language="javascript" type="text/javascript">
$('login-button').observe('click', function() { 
	if ($('login-panel-surround').getStyle('visibility') == 'hidden' || $('login-panel-surround').getStyle('display') == 'none') {
		new Effect.SlideDown('login-panel-surround', {duration: 0.3});
		$('login-panel-surround').setStyle({visibility:'visible'});
	} else {
		new Effect.SlideUp('login-panel-surround', {duration: 0.3});
	}
});
</script>
<?php endif; ?>

<?php if ($type == 'logout_link') : ?>
<div>
	<form action="<?php echo JRoute::_('index.php?option=com_user'); ?>" method="post" name="login" id="login">
		<?php if ( $params->get('greeting') ) : ?>
		<div><?php echo JText::sprintf('Hello', $name); ?></div>
		<?php endif; ?>
		<?php if ($show_account_links): ?>
		<ul>
			<?php if ($params->get('account_link', 2)) : ?><li><a href="<?php echo $account_url;?>"><?php echo JText::_('Account settings') ?></a></li><?php endif; ?>
			<?php if ($params->get('wishlist_link', 1)) : ?><li><a href="<?php echo $wishlist_url;?>"><?php echo JText::_('Wishlist') ?></a></li><?php endif; ?>
			<?php if ($params->get('cart_link', 1)) : ?><li><a href="<?php echo $cart_url;?>"><?php echo JText::_('Shopping Cart') ?></a></li><?php endif; ?>
			<?php if ($params->get('checkout_link', 1)) : ?><li><a href="<?php echo $checkout_url;?>"><?php echo JText::_('Checkout') ?></a></li><?php endif; ?>
		</ul>
		<?php endif; ?>
		<input type="submit" name="Submit" class="button" value="<?php echo JText::_('Logout') ?>" />
		<input type="hidden" name="option" value="com_user" />
		<input type="hidden" name="task" value="logout" />
		<input type="hidden" name="return" value="<?php echo $return_url ?>" />
		<?php echo JHTML::_( 'form.token' ); ?>
	</form>
</div>
<?php else : ?> 
<div>
	<form action="<?php echo JRoute::_('index.php?option=com_user'); ?>" method="post" name="login" id="login">
		<?php if ( $params->get('pretext') ) : ?>
		<?php echo $params->get('pretext'); ?>
		<br />
		<?php endif; ?>
		<div class="username-block">
			<label for="username_login"><?php echo JText::_('Username') ?></label>
			<input class="inputbox" type="text" id="username_login" size="12" name="username" />
		</div>
		<div class="password-block">
			<label for="password_login"><?php echo JText::_('Password') ?></label>
			<input type="password" class="inputbox" id="password_login" size="12" name="passwd" />
		</div>
		<div class="login-extras">
			<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
			<label for="remember_login"><?php echo JText::_('Remember me') ?></label>
			<input type="checkbox" name="remember" id="remember_login" value="yes" checked="checked" />
			<?php endif; ?>
			<input type="submit" value="<?php echo JText::_('Login') ?>" class="button" name="Login" />
			<ul>
				<li><a href="<?php echo JRoute::_( 'index.php?option=com_user&view=reset' ); ?>"><?php echo JText::_('Forgot your password'); ?></a></li>
				<li><a href="<?php echo JRoute::_( 'index.php?option=com_user&view=remind' ); ?>"><?php echo JText::_('Forgot your username'); ?></a></li>
				<?php if (JComponentHelper::getParams( 'com_users' )->get('allowUserRegistration')) : ?>
				<li><a href="<?php echo JRoute::_( 'index.php?option=com_user&view=register' ); ?>"><?php echo JText::_('Register'); ?></a></li>
				<?php endif; ?>
			</ul>
			<input type="hidden" name="option" value="com_user" />
			<input type="hidden" name="task" value="login" />
			<input type="hidden" name="return" value="<?php echo $return_url ?>" />
			<?php echo JHTML::_( 'form.token' ); ?>
		</div>
	</form>
</div>
<?php endif; ?>
