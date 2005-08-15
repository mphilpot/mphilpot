<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: login_page.php,v 1.43 2005/04/22 22:06:07 prichards Exp $
	# --------------------------------------------------------

	# Login page POSTs results to login.php
	# Check to see if the user is already logged in

	require_once( 'core.php' );

	$f_error		= gpc_get_bool( 'error' );
	$f_cookie_error	= gpc_get_bool( 'cookie_error' );
	$f_return		= gpc_get_string( 'return', '' );

	# Check for HTTP_AUTH. HTTP_AUTH is handled in login.php

	if ( HTTP_AUTH == config_get( 'login_method' ) ) {
		$t_uri = "login.php";

		if ( !$f_return && ON == config_get( 'allow_anonymous_login' ) ) {
			$t_uri = "login_anon.php";
		}

		if ( $f_return ) {
			$t_uri .= "?return=" . urlencode( $f_return );
		}

		print_header_redirect( $t_uri );
		exit;
	}

	html_page_top1();
	html_page_top2a();

	echo '<br /><div align="center">';

	# Display short greeting message
	echo lang_get( 'login_page_info' ) . '<br />';

	# Only echo error message if error variable is set
	if ( $f_error ) {
		echo '<font color="red">' . lang_get( 'login_error' ) . '</font>';
	}
	if ( $f_cookie_error ) {
		echo lang_get( 'login_cookies_disabled' ) . '<br />';
	}

	echo '</div>';
?>

<!-- Login Form BEGIN -->
<br />
<div align="center">
<form name="login_form" method="post" action="login.php">
<table class="width50" cellspacing="1">
<tr>
	<td class="form-title">
		<?php
			if ( !is_blank( $f_return ) ) {
			?>
				<input type="hidden" name="return" value="<?php echo htmlspecialchars( $f_return ) ?>" />
				<?php
			}
			echo lang_get( 'login_title' ) ?>
	</td>
	<td class="right">
	<?php
		if ( ON == config_get( 'allow_anonymous_login' ) ) {
			print_bracket_link( 'login_anon.php', lang_get( 'login_anonymously' ) );
		}
	?>
	</td>
</tr>
<tr class="row-1">
	<td class="category" width="25%">
		<?php echo lang_get( 'username' ) ?>
	</td>
	<td width="75%">
		<input type="text" name="username" size="32" maxlength="32" />
	</td>
</tr>
<tr class="row-2">
	<td class="category">
		<?php echo lang_get( 'password' ) ?>
	</td>
	<td>
		<input type="password" name="password" size="16" maxlength="32" />
	</td>
</tr>
<tr class="row-1">
	<td class="category">
		<?php echo lang_get( 'save_login' ) ?>
	</td>
	<td>
		<input type="checkbox" name="perm_login" />
	</td>
</tr>
<tr>
	<td class="center" colspan="2">
		<input type="submit" class="button" value="<?php echo lang_get( 'login_button' ) ?>" />
	</td>
</tr>
</table>
</form>
</div>

<?php
	PRINT '<br /><div align="center">';
	print_signup_link();
	PRINT '&nbsp;';
	print_lost_password_link();
	PRINT '</div>';

	#
	# Do some checks to warn administrators of possible security holes.
	# Since this is considered part of the admin-checks, the strings are not translated.
	#

	# Warning, if plain passwords are selected
	if ( config_get( 'login_method' ) === PLAIN ) {
		echo '<div class="warning" align="center">';
		echo '<p><font color="red"><strong>WARNING:</strong> Plain password authentication is used, this will expose your passwords to administrators.</font></p>';
		echo '</div>';
	}

	# Generate a warning if administrator/root is valid.
	$t_admin_user_id = user_get_id_by_name( 'administrator' );
	if ( $t_admin_user_id !== false ) {
		if ( user_is_enabled( $t_admin_user_id ) && auth_does_password_match( $t_admin_user_id, 'root' ) ) {
			echo '<div class="warning" align="center">';
			echo '<p><font color="red"><strong>WARNING:</strong> You should disable the default "administrator" account or change its password.</font></p>';
			echo '</div>';
		}
	}

	# Check if the admin directory is available and is readable.
	$t_admin_dir = dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR;
	if ( is_dir( $t_admin_dir ) && is_readable( $t_admin_dir ) ) {
			echo '<div class="warning" align="center">', "\n";
			echo '<p><font color="red"><strong>WARNING:</strong> Admin directory should be removed.</font></p>', "\n";
			echo '</div>', "\n";
	}
?>

<!-- Autofocus JS -->
<?php if ( ON == config_get( 'use_javascript' ) ) { ?>
<script type="text/javascript" language="JavaScript">
<!--
	window.document.login_form.username.focus();
-->
</script>
<?php } ?>

<?php html_page_bottom1a( __FILE__ ) ?>
