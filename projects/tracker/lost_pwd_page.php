<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: lost_pwd_page.php,v 1.7 2005/04/22 22:27:50 prichards Exp $
	# --------------------------------------------------------
	# ======================================================================
	# Author: Marcello Scata' <marcelloscata at users.sourceforge.net> ITALY
	# ======================================================================

	require_once( 'core.php' );

	# lost password feature disabled or reset password via email disabled -> stop here!
	if( OFF == config_get( 'lost_password_feature' ) ||
		OFF == config_get( 'send_reset_password' )  ||
	 	OFF == config_get( 'enable_email_notification' ) ) {
		trigger_error( ERROR_LOST_PASSWORD_NOT_ENABLED, ERROR );
	}

	html_page_top1();
	html_page_top2a();

	echo "<br />";
?>
<br />
<div align="center">
<form name="lost_password_form" method="post" action="lost_pwd.php">
<table class="width50" cellspacing="1">
<tr>
	<td class="form-title" colspan="2">
		<?php echo lang_get( 'lost_password_title' ) ?>
	</td>
</tr>
<?php
		$t_allow_passwd = helper_call_custom_function( 'auth_can_change_password', array() );
  if ( $t_allow_passwd ) {
?>
<tr class="row-1">
	<td class="category" width="25%">
		<?php echo lang_get( 'username' ) ?>
	</td>
	<td width="75%">
		<input type="text" name="username" size="32" maxlength="32" />
	</td>
</tr>
<tr class="row-2">
	<td class="category" width="25%">
		<?php echo lang_get( 'email' ) ?>
	</td>
	<td width="75%">
		<?php print_email_input( 'email', '' ) ?>
	</td>
</tr>
<tr>
	<td colspan="2">
		<br/>
		<?php echo lang_get( 'lost_password_info' ) ?>
		<br/><br/>
	</td>
</tr>
<tr>
	<td class="center" colspan="2">
		<input type="submit" class="button" value="<?php echo lang_get( 'submit_button' ) ?>" />
	</td>
</tr>
<?php
  }else{
?>
<tr>
	<td colspan="2">
		<br/>
		<?php echo lang_get( 'no_password_request' ) ?>
		<br/><br/>
	</td>
</tr>
<?php
  }
?>

</table>
</form>
</div>

<?php
	PRINT '<br /><div align="center">';
	print_login_link();
	PRINT '&nbsp;';
	print_signup_link();
	PRINT '</div>';

	if ( ON == config_get( 'use_javascript' ) ) {
?>
<!-- Autofocus JS -->
<?php if ( ON == config_get( 'use_javascript' ) ) { ?>
<script type="text/javascript" language="JavaScript">
<!--
	window.document.lost_password_form.username.focus();
-->
</script>
<?php } ?>

<?php
	}

	html_page_bottom1a( __FILE__ );
?>
