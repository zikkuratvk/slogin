<?php
/**
 * Social Login
 *
 * @version 	1.0
 * @author		SmokerMan, Arkadiy, Joomline
 * @copyright	© 2012. All rights reserved.
 * @license 	GNU/GPL v.3 or later.
 */

// No direct access.
defined('_JEXEC') or die('(@)|(@)');
?>
<?php if ($type == 'logout') : ?>

<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form">
    <div class="login-greeting">
        <?php echo JText::sprintf('MOD_SLOGIN_HINAME', htmlspecialchars($user->get('name')));	 ?>
    </div>
    <div class="logout-button">
        <input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGOUT'); ?>" />
        <input type="hidden" name="option" value="com_users" />
        <input type="hidden" name="task" value="user.logout" />
        <input type="hidden" name="return" value="<?php echo $return; ?>" />
        <?php echo JHtml::_('form.token'); ?>
    </div>
</form>
<?php else : ?>
<?php if ($params->get('inittext')): ?>
    <div class="pretext">
        <p><?php echo $params->get('inittext'); ?></p>
    </div>
    <?php endif; ?>
<div id="slogin-buttons" class="slogin-buttons <?php echo $moduleclass_sfx?>">
    <?php if ($params->get('vkontakte')): ?>
    <a href="index.php?option=com_slogin&amp;task=vk.auth&amp;return=<?php echo $return; ?>"><span class="vkontakte">&nbsp;</span></a>
    <?php endif; ?>
    <?php if ($params->get('google')): ?>
    <a href="index.php?option=com_slogin&amp;task=google.auth&amp;return=<?php echo $return; ?>"><span class="google">&nbsp;</span></a>
    <?php endif; ?>
    <?php if ($params->get('facebook')): ?>
    <a href="index.php?option=com_slogin&amp;task=fb.auth&amp;return=<?php echo $return; ?>"><span class="facebook">&nbsp;</span></a>
    <?php endif; ?>
    <?php if ($params->get('twitter')): ?>
    <a href="index.php?option=com_slogin&amp;task=tw.auth&amp;return=<?php echo $return; ?>"><span class="twitter">&nbsp;</span></a>
    <?php endif; ?>
    <?php if ($params->get('mail')): ?>
    <a href="index.php?option=com_slogin&amp;task=mail.auth&amp;return=<?php echo $return; ?>"><span class="mail">&nbsp;</span></a>
    <?php endif; ?>
    <?php if ($params->get('odnoklassniki')): ?>
    <a href="index.php?option=com_slogin&amp;task=odnoklassniki.auth&amp;return=<?php echo $return; ?>"><span class="odnoklassniki">&nbsp;</span></a>
    <?php endif; ?>
</div>
<?php if ($params->get('pretext')): ?>
    <div class="pretext">
        <p><?php echo $params->get('pretext'); ?></p>
    </div>
    <?php endif; ?>
<?php if ($params->get('show_login_form')): ?>
    <div class="slogin-clear"></div>
    <form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" >
        <fieldset class="userdata">
            <p id="form-login-username">
                <label for="modlgn-username"><?php echo JText::_('MOD_SLOGIN_VALUE_USERNAME') ?></label>
                <input id="modlgn-username" type="text" name="username" class="inputbox"  size="18" />
            </p>
            <p id="form-login-password">
                <label for="modlgn-passwd"><?php echo JText::_('JGLOBAL_PASSWORD') ?></label>
                <input id="modlgn-passwd" type="password" name="password" class="inputbox" size="18"  />
            </p>
            <?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
            <p id="form-login-remember">
                <label for="modlgn-remember"><?php echo JText::_('MOD_SLOGIN_REMEMBER_ME') ?></label>
                <input id="modlgn-remember" type="checkbox" name="remember" class="inputbox" value="yes"/>
            </p>
            <?php endif; ?>
            <input type="submit" name="Submit" class="button" value="<?php echo JText::_('JLOGIN') ?>" />
            <input type="hidden" name="option" value="com_users" />
            <input type="hidden" name="task" value="user.login" />
            <input type="hidden" name="return" value="<?php echo $return; ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </fieldset>
        <ul>
            <li>
                <a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
                    <?php echo JText::_('MOD_SLOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
            </li>
            <li>
                <a href="<?php echo JRoute::_('index.php?option=com_users&view=remind'); ?>">
                    <?php echo JText::_('MOD_SLOGIN_FORGOT_YOUR_USERNAME'); ?></a>
            </li>
            <?php
            $usersConfig = JComponentHelper::getParams('com_users');
            if ($usersConfig->get('allowUserRegistration')) : ?>
                <li>
                    <a href="<?php echo JRoute::_('index.php?option=com_users&view=registration'); ?>">
                        <?php echo JText::_('MOD_SLOGIN_REGISTER'); ?></a>
                </li>
                <?php endif; ?>
        </ul>
        <?php if ($params->get('posttext')): ?>
        <div class="posttext">
            <p><?php echo $params->get('posttext'); ?></p>
        </div>
        <?php endif; ?>
    </form>
    <?php endif; ?>
<?php endif; ?>