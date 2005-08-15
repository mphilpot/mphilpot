<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: manage_config_work_threshold_page.php,v 1.6 2005/04/22 22:27:50 prichards Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );
	require_once( $t_core_path . 'email_api.php' );

	html_page_top1( lang_get( 'manage_threshold_config' ) );
	html_page_top2();

	print_manage_menu( 'adm_permissions_report.php' );
	print_manage_config_menu( 'manage_config_work_threshold_page.php' );

    $t_user = auth_get_current_user_id();
	$t_project = helper_get_current_project();
	$t_access = user_get_access_level( $t_user, $t_project );
	$t_show_submit = false;

	$t_access_levels = explode_enum_string( config_get( 'access_levels_enum_string' ) );

	function get_section_begin( $p_section_name ) {
		global $t_access_levels;

		echo '<table class="width100">';
		echo '<tr><td class="form-title" colspan="' . ( count( $t_access_levels ) + 2 ) . '">' . strtoupper( $p_section_name ) . '</td></tr>' . "\n";
		echo '<tr><td class="form-title" width="40%" rowspan="2">' . lang_get( 'perm_rpt_capability' ) . '</td>';
		echo '<td class="form-title"style="text-align:center"  width="40%" colspan="' . count( $t_access_levels ) . '">' . lang_get( 'access_levels' ) . '</td>';
		echo '<td class="form-title" style="text-align:center" rowspan="2">&nbsp;' . lang_get( 'alter_level' ) . '&nbsp;</td></tr><tr>';
		foreach( $t_access_levels as $t_access_level ) {
			$t_entry_array = explode_enum_arr( $t_access_level );
			echo '<td class="form-title" style="text-align:center">&nbsp;' . get_enum_to_string( lang_get( 'access_levels_enum_string' ), $t_entry_array[0] ) . '&nbsp;</td>';
		}
		echo '</tr>' . "\n";
	}

	function get_capability_row( $p_caption, $p_threshold, $p_all_projects_only=false ) {
	    global $t_user, $t_project, $t_show_submit, $t_access_levels;

		$t_needed_access = config_get( $p_threshold );
		$t_can_change = access_has_project_level( config_get_access( $p_threshold ), $t_project, $t_user )
		          && ( ( ALL_PROJECTS == $t_project ) || ! $p_all_projects_only );

		echo '<tr ' . helper_alternate_class() . '><td>' . string_display( $p_caption ) . '</td>';
		foreach( $t_access_levels as $t_access_level ) {
			$t_entry_array = explode_enum_arr( $t_access_level );
			if ( is_array( $t_needed_access ) ) {
		        $t_set = in_array( $t_entry_array[0], $t_needed_access );
		    } else {
		        $t_set = $t_entry_array[0] >= $t_needed_access;
		    }

			if ( $t_can_change ) {
			    $t_checked = $t_set ? "CHECKED" : "";
			    $t_value = "<input type=\"checkbox\" name=\"flag_thres_" . $p_threshold . "[]\" value=\"$t_entry_array[0]\" $t_checked />";
			    $t_show_submit = true;
			} else {
			    if ( $t_set ) {
				    $t_value = '<img src="images/ok.gif" width="20" height="15" alt="X" title="X" />';
			    } else {
				    $t_value = '&nbsp;';
			    }
            }
			echo '<td class="center">' . $t_value . '</td>';
		}
		if ( $t_can_change ) {
			echo '<td><select name="access_' . $p_threshold . '">';
			print_enum_string_option_list( 'access_levels', config_get_access( $p_threshold ) );
			echo '</select> </td>';
		} else {
			echo '<td>' . get_enum_to_string( lang_get( 'access_levels_enum_string' ), config_get_access( $p_threshold ) ) . '&nbsp;</td>';
		}

		echo '</tr>' . "\n";
	}

	function get_capability_boolean( $p_caption, $p_threshold, $p_all_projects_only=false ) {
	    global $t_user, $t_project, $t_show_submit, $t_access_levels;

		$t_can_change = access_has_project_level( config_get_access( $p_threshold ), $t_project, $t_user )
		          && ( ( ALL_PROJECTS == $t_project ) || ! $p_all_projects_only );

		echo '<tr ' . helper_alternate_class() . '><td>' . string_display( $p_caption ) . '</td>';
		if ( $t_can_change ) {
		    $t_checked = ( ON == config_get( $p_threshold ) ) ? "CHECKED" : "";
		    $t_value = "<input type=\"checkbox\" name=\"flag_" . $p_threshold . "\" value=\"1\" $t_checked />";
		    $t_show_submit = true;
		} else {
		    if ( ON == config_get( $p_threshold ) ) {
			    $t_value = '<img src="images/ok.gif" width="20" height="15" title="X" alt="X" />';
		    } else {
			    $t_value = '&nbsp;';
		    }
        }
		echo '<td class="left" colspan="' . count( $t_access_levels ) . '">' . $t_value . '</td>';

		if ( $t_can_change ) {
			echo '<td><select name="access_' . $p_threshold . '">';
			print_enum_string_option_list( 'access_levels', config_get_access( $p_threshold ) );
			echo '</select> </td>';
		} else {
			echo '<td>' . get_enum_to_string( lang_get( 'access_levels_enum_string' ), config_get_access( $p_threshold ) ) . '&nbsp;</td>';
		}

		echo '</tr>' . "\n";
	}

	function get_capability_enum( $p_caption, $p_threshold, $p_enum, $p_all_projects_only=false ) {
	    global $t_user, $t_project, $t_show_submit, $t_access_levels;

		$t_can_change = access_has_project_level( config_get_access( $p_threshold ), $t_project, $t_user )
		          && ( ( ALL_PROJECTS == $t_project ) || ! $p_all_projects_only );

		echo '<tr ' . helper_alternate_class() . '><td>' . string_display( $p_caption ) . '</td>';
		if ( $t_can_change ) {
			echo '<td class="left" colspan="' . count( $t_access_levels ) . '"><select name="flag_' . $p_threshold . '">';
			print_enum_string_option_list( $p_enum, config_get( $p_threshold ) );
			echo '</select> </td>';
		    $t_show_submit = true;
		} else {
			$t_value = get_enum_to_string( lang_get( $p_enum . '_enum_string' ), config_get( $p_threshold ) ) . '&nbsp;';
		    echo '<td class="left" colspan="' . count( $t_access_levels ) . '">' . $t_value . '</td>';
        }

		if ( $t_can_change ) {
			echo '<td><select name="access_' . $p_threshold . '">';
			print_enum_string_option_list( 'access_levels', config_get_access( $p_threshold ) );
			echo '</select> </td>';
		} else {
			echo '<td>' . get_enum_to_string( lang_get( 'access_levels_enum_string' ), config_get_access( $p_threshold ) ) . '&nbsp;</td>';
		}

		echo '</tr>' . "\n";
	}

	function get_section_end() {
		echo '</table><br />' . "\n";
	}

    echo "<br /><br />\n";

	if ( ALL_PROJECTS == $t_project ) {
	    $t_project_title = lang_get( 'config_all_projects' );
	} else {
	    $t_project_title = sprintf( lang_get( 'config_project' ) , project_get_name( $t_project ) );
	}
	echo '<p class="bold">' . $t_project_title . '</p>' . "\n";

	echo "<form name=\"mail_config_action\" method=\"post\" action=\"manage_config_work_threshold_set.php\">\n";

	# Issues
	get_section_begin( lang_get( 'issues' ) );
	get_capability_row( lang_get( 'report_issue' ), 'report_bug_threshold' );
    get_capability_enum( lang_get( 'submit_status' ), 'bug_submit_status', 'status' );
	get_capability_row( lang_get( 'update_issue' ), 'update_bug_threshold' );
	get_capability_boolean( lang_get( 'allow_close_immediate' ), 'allow_close_immediately' );
    get_capability_boolean( lang_get( 'allow_reporter_close' ), 'allow_reporter_close' );
	get_capability_row( lang_get( 'monitor_issue' ), 'monitor_bug_threshold' );
	get_capability_row( lang_get( 'handle_issue' ), 'handle_bug_threshold' );
 	get_capability_row( lang_get( 'assign_issue' ), 'update_bug_assign_threshold' );
	get_capability_row( lang_get( 'move_issue' ), 'move_bug_threshold', true );
	get_capability_row( lang_get( 'delete_issue' ), 'delete_bug_threshold' );
	get_capability_row( lang_get( 'reopen_issue' ), 'reopen_bug_threshold' );
    get_capability_boolean( lang_get( 'allow_reporter_reopen' ), 'allow_reporter_reopen' );
    get_capability_enum( lang_get( 'reopen_status' ), 'bug_reopen_status', 'status' );
    get_capability_enum( lang_get( 'reopen_resolution' ), 'bug_reopen_resolution', 'resolution' );
    get_capability_enum( lang_get( 'resolved_status' ), 'bug_resolved_status_threshold', 'status' );
    get_capability_enum( lang_get( 'readonly_status' ), 'bug_readonly_status_threshold', 'status' );
	get_capability_row( lang_get( 'update_readonly_issues' ), 'update_readonly_bug_threshold' );
	get_capability_row( lang_get( 'update_issue_status' ), 'update_bug_status_threshold' );
	get_capability_row( lang_get( 'view_private_issues' ), 'private_bug_threshold' );
	get_capability_row( lang_get( 'set_view_status' ), 'set_view_status_threshold' );
	get_capability_row( lang_get( 'update_view_status' ), 'change_view_status_threshold' );
	get_capability_row( lang_get( 'show_list_of_users_monitoring_issue' ), 'show_monitor_list_threshold' );
    get_capability_boolean( lang_get( 'set_status_assigned' ), 'auto_set_status_to_assigned' );
    get_capability_enum( lang_get( 'assigned_status' ), 'bug_assigned_status', 'status' );
    get_capability_boolean( lang_get( 'limit_access' ), 'limit_reporters', true );
	get_section_end();

	# Notes
	get_section_begin( lang_get( 'notes' ) );
	get_capability_row( lang_get( 'add_notes' ), 'add_bugnote_threshold' );
	get_capability_row( lang_get( 'update_notes' ), 'update_bugnote_threshold' );
    get_capability_boolean( lang_get( 'allow_user_edit' ), 'bugnote_allow_user_edit_delete' );
	get_capability_row( lang_get( 'delete_note' ), 'delete_bugnote_threshold' );
	get_capability_row( lang_get( 'view_private_notes' ), 'private_bugnote_threshold' );
	get_section_end();

	# Others
	get_section_begin( lang_get('others' ) );
	get_capability_row( lang_get( 'view' ) . ' ' . lang_get( 'changelog_link' ), 'view_changelog_threshold' );
	get_capability_row( lang_get( 'view' ) . ' ' . lang_get( 'assigned_to' ), 'view_handler_threshold' );
	get_capability_row( lang_get( 'view' ) . ' ' . lang_get( 'bug_history' ), 'view_history_threshold' );
	get_capability_row( lang_get( 'send_reminders' ), 'bug_reminder_threshold' );
	get_section_end();


    if ( $t_show_submit ) {
        echo "<input type=\"submit\" class=\"button\" value=\"" . lang_get( 'change_configuration' ) . "\" />\n";
    }

	echo "</form>\n";

	html_page_bottom1( __FILE__ );
?>
