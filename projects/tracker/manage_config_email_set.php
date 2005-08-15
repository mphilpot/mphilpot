<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: manage_config_email_set.php,v 1.5 2005/04/22 01:59:29 thraxisp Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path.'email_api.php' );

	$t_can_change_level = min( config_get_access( 'notify_flags' ), config_get_access( 'default_notify_flags' ) );
	access_ensure_global_level( $t_can_change_level );

	$t_redirect_url = 'manage_config_email_page.php';
	$t_project = helper_get_current_project();

	$f_flags			= gpc_get( 'flag', array() );
	$f_thresholds		= gpc_get( 'flag_threshold', array() );
	$f_actions_access	= gpc_get_int( 'notify_actions_access' );

	html_page_top1( lang_get( 'manage_email_config' ) );
	html_meta_redirect( $t_redirect_url );
	html_page_top2();

	$t_access = current_user_get_access_level();
	$t_can_change_flags = $t_access >= config_get_access( 'notify_flags' );
	$t_can_change_defaults = $t_access >= config_get_access( 'default_notify_flags' );

	# build a list of the possible actions and flags
	$t_valid_actions = array( 'new', 'owner', 'reopened', 'deleted', 'bugnote' );
	if( config_get( 'enable_sponsorship' ) == ON ) {
		$t_valid_actions[] = 'sponsor';
	}
	if( config_get( 'enable_relationship' ) == ON ) {
		$t_valid_actions[] = 'relationship';
	}
	
	$t_statuses = get_enum_to_array( config_get( 'status_enum_string' ) );
    ksort( $t_statuses );
    reset( $t_statuses );

	foreach( $t_statuses as $t_status => $t_label ) {
		$t_valid_actions[] = $t_label;
	}
	$t_valid_flags = array( 'reporter', 'handler', 'monitor' , 'bugnotes' );

	# initialize the thresholds
	foreach( $t_valid_actions as $t_action ) {
		$t_thresholds_min[$t_action] = NOBODY;
		$t_thresholds_max[$t_action] = ANYBODY;
	}


	# parse flags and thresholds
	foreach( $f_flags as $t_flag_value ) {
		list( $t_action, $t_flag ) = split( ':', $t_flag_value );
		$t_flags[$t_action][$t_flag] = true;
	}
	foreach( $f_thresholds as $t_threshold_value ) {
		list( $t_action, $t_threshold ) = split( ':', $t_threshold_value );
		if ( $t_threshold < $t_thresholds_min[$t_action] ) {
			$t_thresholds_min[$t_action] = $t_threshold;
		}
		if ( $t_threshold > $t_thresholds_max[$t_action] ) {
			$t_thresholds_max[$t_action] = $t_threshold;
		}
	}

	# if we can set defaults, find them
	if ( $t_can_change_defaults ) {
		$t_first = true;

		# for flags, assume they are true, unless one of the actions has them off
		foreach ( $t_valid_flags as $t_flag ) {
			$t_default_flags[$t_flag] = true;
			foreach ( $t_valid_actions as $t_action ) {
				if ( ! isset( $t_flags[$t_action][$t_flag] ) ) {
					unset( $t_default_flags[$t_flag] );
				}
			}
		}
		# for thresholds, find the subset that matches all of the actions
		$t_default_min = ANYBODY;
		$t_default_max = NOBODY;
		foreach ( $t_valid_actions as $t_action ) {
			if ( $t_default_max > $t_thresholds_max[$t_action] ) {
				$t_default_max = $t_thresholds_max[$t_action];
			}
			if ( $t_default_min < $t_thresholds_min[$t_action] ) {
				$t_default_min = $t_thresholds_min[$t_action];
			}
		}
		$t_default_flags['threshold_min'] = $t_default_min;
		$t_default_flags['threshold_max'] = $t_default_max;

		config_set( 'default_notify_flags', $t_default_flags, NO_USER, $t_project, $f_actions_access );
	} else {
		$t_default_flags = config_get( 'default_notify_flags' );
	}

	# set the values for specific actions if different from the defaults
	foreach ( $t_valid_actions as $t_action ) {
		$t_action_printed = false;
		foreach ( $t_valid_flags as $t_flag ) {
			if ( ! isset( $t_default_flags[$t_flag] ) ) {
				$t_default_flags[$t_flag] = false;
			}
			if ( isset( $t_flags[$t_action][$t_flag] ) <> $t_default_flags[$t_flag] ) {
				$t_notify_flags[$t_action][$t_flag] = isset( $t_flags[$t_action][$t_flag] );
			}
		}
		if ( $t_default_flags['threshold_min'] <> $t_thresholds_min[$t_action] ) {
			$t_notify_flags[$t_action]['threshold_min'] = $t_thresholds_min[$t_action];
		}
		if ( $t_default_flags['threshold_max'] <> $t_thresholds_max[$t_action] ) {
			$t_notify_flags[$t_action]['threshold_max'] = $t_thresholds_max[$t_action];
		}
	}
	if ( isset( $t_notify_flags ) ) {
		config_set( 'notify_flags', $t_notify_flags, NO_USER, $t_project, $f_actions_access );
	}


?>

<br />
<div align="center">
<?php
	print_bracket_link( $t_redirect_url, lang_get( 'proceed' ) );
?>
</div>

<?php html_page_bottom1( __FILE__ ) ?>
