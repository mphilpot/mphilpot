<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: bug_report.php,v 1.40 2005/03/01 23:14:16 jlatour Exp $
	# --------------------------------------------------------

	# This page stores the reported bug

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'string_api.php' );
	require_once( $t_core_path.'file_api.php' );
	require_once( $t_core_path.'bug_api.php' );
	require_once( $t_core_path.'custom_field_api.php' );

	access_ensure_project_level( config_get('report_bug_threshold' ) );

	$t_bug_data = new BugData;
	$t_bug_data->build				= gpc_get_string( 'build', '' );
	$t_bug_data->platform				= gpc_get_string( 'platform', '' );
	$t_bug_data->os					= gpc_get_string( 'os', '' );
	$t_bug_data->os_build				= gpc_get_string( 'os_build', '' );
	$t_bug_data->version			= gpc_get_string( 'product_version', '' );
	$t_bug_data->profile_id			= gpc_get_int( 'profile_id', 0 );
	$t_bug_data->handler_id			= gpc_get_int( 'handler_id', 0 );
	$t_bug_data->view_state			= gpc_get_int( 'view_state', config_get( 'default_bug_view_status' ) );

	$t_bug_data->category				= gpc_get_string( 'category', '' );
	$t_bug_data->reproducibility		= gpc_get_int( 'reproducibility' );
	$t_bug_data->severity				= gpc_get_int( 'severity' );
	$t_bug_data->priority				= gpc_get_int( 'priority', NORMAL );
	$t_bug_data->summary				= gpc_get_string( 'summary' );
	$t_bug_data->description			= gpc_get_string( 'description' );
	$t_bug_data->steps_to_reproduce	= gpc_get_string( 'steps_to_reproduce', '' );
	$t_bug_data->additional_information	= gpc_get_string( 'additional_info', '' );

	$f_file					= gpc_get_file( 'file', null );
	$f_report_stay			= gpc_get_bool( 'report_stay' );
	$t_bug_data->project_id			= gpc_get_int( 'project_id' );

	$t_bug_data->reporter_id		= auth_get_current_user_id();
	$t_upload_method	= config_get( 'file_upload_method' );

	$t_bug_data->summary			= trim( $t_bug_data->summary );

	# If a file was uploaded, and we need to store it on disk, let's make
	#  sure that the file path for this project exists
	if ( is_uploaded_file( $f_file['tmp_name'] ) &&
		  file_allow_bug_upload() &&
		  ( DISK == $t_upload_method || FTP == $t_upload_method ) ) {
		$t_file_path = project_get_field( $t_bug_data->project_id, 'file_path' );

		if ( !file_exists( $t_file_path ) ) {
			trigger_error( ERROR_NO_DIRECTORY, ERROR );
		}
	}


	# if a profile was selected then let's use that information
	if ( 0 != $t_bug_data->profile_id ) {
		if ( profile_is_global( $t_bug_data->profile_id ) ) {
			$row = user_get_profile_row( ALL_USERS, $t_bug_data->profile_id );
		} else {
			$row = user_get_profile_row( $t_bug_data->reporter_id, $t_bug_data->profile_id );
		}

		if ( is_blank( $t_bug_data->platform ) ) {
			$t_bug_data->platform = $row['platform'];
		}
		if ( is_blank( $t_bug_data->os ) ) {
			$t_bug_data->os = $row['os'];
		}
		if ( is_blank( $t_bug_data->os_build ) ) {
			$t_bug_data->os_build = $row['os_build'];
		}
	}

	helper_call_custom_function( 'issue_create_validate', array( $t_bug_data ) );

	# Validate the custom fields before adding the bug.
	$t_related_custom_field_ids = custom_field_get_linked_ids( $t_bug_data->project_id );
	foreach( $t_related_custom_field_ids as $t_id ) {
		$t_def = custom_field_get_definition( $t_id );
		if ( $t_def['require_report'] && ( gpc_get_custom_field( "custom_field_$t_id", $t_def['type'], '' ) == '' ) ) {
			error_parameters( lang_get_defaulted( custom_field_get_field( $t_id, 'name' ) ) );
			trigger_error( ERROR_EMPTY_FIELD, ERROR );
		}
		if ( !custom_field_validate( $t_id, gpc_get_custom_field( "custom_field_$t_id", $t_def['type'], $t_def['default_value'] ) ) ) {
			error_parameters( lang_get_defaulted( custom_field_get_field( $t_id, 'name' ) ) );
			trigger_error( ERROR_CUSTOM_FIELD_INVALID_VALUE, ERROR );
		}
	}

	# Create the bug
	$t_bug_id = bug_create( $t_bug_data );

	# Handle the file upload
	if ( is_uploaded_file( $f_file['tmp_name'] ) &&
		  0 != $f_file['size'] &&
		  file_allow_bug_upload() ) {
		file_add( $t_bug_id, $f_file['tmp_name'], $f_file['name'], $f_file['type'] );
	}


	# Handle custom field submission
	foreach( $t_related_custom_field_ids as $t_id ) {
		# Do not set custom field value if user has no write access.
		if( !custom_field_has_write_access( $t_id, $t_bug_id ) ) {
			continue;
		}

		$t_def = custom_field_get_definition( $t_id );
		if( !custom_field_set_value( $t_id, $t_bug_id, gpc_get_custom_field( "custom_field_$t_id", $t_def['type'], $t_def['default_value'] ) ) ) {
			error_parameters( lang_get_defaulted( custom_field_get_field( $t_id, 'name' ) ) );
			trigger_error( ERROR_CUSTOM_FIELD_INVALID_VALUE, ERROR );
		}
	}

	$f_master_bug_id = gpc_get_int( 'm_id', 0 );
	$f_rel_type = gpc_get_int( 'rel_type', -1 );

	if( $f_master_bug_id > 0 && $f_rel_type >= 0 ) {
		# it's a child generation... let's create the relationship and add some lines in the history

		# update master bug last updated
		bug_update_date( $f_master_bug_id );

		# Add log line to record the cloning action
		history_log_event_special( $t_bug_id, BUG_CREATED_FROM, '', $f_master_bug_id );
		history_log_event_special( $f_master_bug_id, BUG_CLONED_TO, '', $t_bug_id );

		# Add the relationship
		relationship_add( $t_bug_id, $f_master_bug_id, $f_rel_type );

		# Add log line to the history (both issues)
		history_log_event_special( $f_master_bug_id, BUG_ADD_RELATIONSHIP, relationship_get_complementary_type( $f_rel_type ), $t_bug_id );
		history_log_event_special( $t_bug_id, BUG_ADD_RELATIONSHIP, $f_rel_type, $f_master_bug_id );

		# Send the email notification
		email_relationship_added( $f_master_bug_id, $t_bug_id, relationship_get_complementary_type( $f_rel_type ) );
	}

	email_new_bug( $t_bug_id );

	helper_call_custom_function( 'issue_create_notify', array( $t_bug_id ) );

	html_page_top1();

	if ( ! $f_report_stay ) {
		html_meta_redirect( 'view_all_bug_page.php' );
	}

	html_page_top2();
?>
<br />
<div align="center">
<?php
	echo lang_get( 'operation_successful' ) . '<br />';
	print_bracket_link( string_get_bug_view_url( $t_bug_id ), lang_get( 'view_submitted_bug_link' ) . " $t_bug_id", true );
	print_bracket_link( 'view_all_bug_page.php', lang_get( 'view_bugs_link' ) );

	if ( $f_report_stay ) {
?>
	<p>
	<form method="post" action="<?php echo string_get_bug_report_url() ?>">
		<input type="hidden" name="category" 		value="<?php echo $t_bug_data->category ?>" />
		<input type="hidden" name="severity" 		value="<?php echo $t_bug_data->severity ?>" />
		<input type="hidden" name="reproducibility" 	value="<?php echo $t_bug_data->reproducibility ?>" />
		<input type="hidden" name="profile_id" 		value="<?php echo $t_bug_data->profile_id ?>" />
		<input type="hidden" name="platform" 		value="<?php echo $t_bug_data->platform ?>" />
		<input type="hidden" name="os" 			value="<?php echo $t_bug_data->os ?>" />
		<input type="hidden" name="os_build" 		value="<?php echo $t_bug_data->os_build ?>" />
		<input type="hidden" name="product_version" 	value="<?php echo $t_bug_data->version ?>" />
		<input type="hidden" name="build" 		value="<?php echo $t_bug_data->build ?>" />
		<input type="hidden" name="report_stay" 	value="1" />
		<input type="hidden" name="view_state"		value="<?php echo $t_bug_data->view_state ?>" />
		<input type="submit" class="button" 		value="<?php echo lang_get( 'report_more_bugs' ) ?>" />
	</form>
	</p>
<?php
	}
?>
</div>

<?php html_page_bottom1( __FILE__ ) ?>
