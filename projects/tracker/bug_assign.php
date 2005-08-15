<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: bug_assign.php,v 1.40 2005/02/12 20:01:03 jlatour Exp $
	# --------------------------------------------------------
?>
<?php
	# Assign bug to user then redirect to viewing page
?>
<?php
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'bug_api.php' );
?>
<?php
	$f_bug_id = gpc_get_int( 'bug_id' );
	$f_handler_id = gpc_get_int( 'handler_id', auth_get_current_user_id() );

	access_ensure_bug_level( config_get( 'update_bug_assign_threshold', config_get( 'update_bug_threshold' ) ), $f_bug_id );

	$t_bug_sponsored = sponsorship_get_amount( sponsorship_get_all_ids( $f_bug_id ) ) > 0;
	if ( $t_bug_sponsored ) {
		if ( !access_has_bug_level( config_get( 'assign_sponsored_bugs_threshold' ), $f_bug_id ) ) {
			trigger_error( ERROR_SPONSORSHIP_ASSIGNER_ACCESS_LEVEL_TOO_LOW, ERROR );
		}
	}

	if ( $f_handler_id != NO_USER ) {
		access_ensure_bug_level( config_get( 'handle_bug_threshold' ), $f_bug_id, $f_handler_id );

		if ( $t_bug_sponsored ) {
			if ( !access_has_bug_level( config_get( 'handle_sponsored_bugs_threshold' ), $f_bug_id, $f_handler_id ) ) {
				trigger_error( ERROR_SPONSORSHIP_HANDLER_ACCESS_LEVEL_TOO_LOW, ERROR );
			}
		}
	}

	bug_assign( $f_bug_id, $f_handler_id );

	print_successful_redirect_to_bug( $f_bug_id );
?>
