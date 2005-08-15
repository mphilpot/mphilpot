<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: print_bug_page.php,v 1.55 2005/03/26 18:27:17 thraxisp Exp $
	# --------------------------------------------------------
?>
<?php
	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'bug_api.php' );
	require_once( $t_core_path.'custom_field_api.php' );
	require_once( $t_core_path.'date_api.php' );
	require_once( $t_core_path.'string_api.php' );
?>
<?php
	$f_bug_id = gpc_get_int( 'bug_id' );

	if ( SIMPLE_ONLY == $g_show_view ) {
		print_header_redirect ( 'bug_view_page.php?bug_id='.$f_bug_id );
	}

	access_ensure_bug_level( VIEWER, $f_bug_id );

	$c_bug_id = (integer)$f_bug_id;

	$t_bug_table = config_get( 'mantis_bug_table' );
	$query = "SELECT *
			FROM $t_bug_table
			WHERE id='$c_bug_id'";
	$result = db_query( $query );
	$row = db_fetch_array( $result );
	extract( $row, EXTR_PREFIX_ALL, 'v' );

	$t_bug_text_table = config_get( 'mantis_bug_text_table' );
	$query = "SELECT *
			FROM $t_bug_text_table
			WHERE id='$v_bug_text_id'";
	$result = db_query( $query );
	$row = db_fetch_array( $result );
	extract( $row, EXTR_PREFIX_ALL, 'v2' );

	$v_os 						= string_display( $v_os );
	$v_os_build					= string_display( $v_os_build );
	$v_platform					= string_display( $v_platform );
	$v_version 					= string_display( $v_version );
	$v_summary 					= string_display_links( $v_summary );
	$v2_description 			= string_display_links( $v2_description );
	$v2_steps_to_reproduce 		= string_display_links( $v2_steps_to_reproduce );
	$v2_additional_information 	= string_display_links( $v2_additional_information );
?>
<?php html_page_top1( bug_format_summary( $f_bug_id, SUMMARY_CAPTION ) ) ?>
<?php
	html_head_end();
	html_body_begin();
?>

<br />
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title" colspan="6">
		<div class="center"><?php echo config_get( 'window_title' ) . ' - ' . project_get_name( $v_project_id ) ?></div>
	</td>
</tr>
<tr>
	<td class="form-title" colspan="6">
		<?php echo lang_get( 'viewing_bug_advanced_details_title' ) ?>
	</td>
</tr>
<tr>
	<td class="print-spacer" colspan="6">
		<hr size="1" />
	</td>
</tr>
<tr class="print-category">
	<td class="print" width="16%">
		<?php echo lang_get( 'id' ) ?>:
	</td>
	<td class="print" width="16%">
		<?php echo lang_get( 'category' ) ?>:
	</td>
	<td class="print" width="16%">
		<?php echo lang_get( 'severity' ) ?>:
	</td>
	<td class="print" width="16%">
		<?php echo lang_get( 'reproducibility' ) ?>:
	</td>
	<td class="print" width="16%">
		<?php echo lang_get( 'date_submitted' ) ?>:
	</td>
	<td class="print" width="16%">
		<?php echo lang_get( 'last_update' ) ?>:
	</td>
</tr>
<tr class="print">
	<td class="print">
		<?php echo $v_id ?>
	</td>
	<td class="print">
		<?php echo $v_category ?>
	</td>
	<td class="print">
		<?php echo get_enum_element( 'severity', $v_severity ) ?>
	</td>
	<td class="print">
		<?php echo get_enum_element( 'reproducibility', $v_reproducibility ) ?>
	</td>
	<td class="print">
		<?php print_date( config_get( 'normal_date_format' ), db_unixtimestamp( $v_date_submitted ) ) ?>
	</td>
	<td class="print">
		<?php print_date( config_get( 'normal_date_format' ), db_unixtimestamp( $v_last_updated ) ) ?>
	</td>
</tr>
<tr>
	<td class="print-spacer" colspan="6">
		<hr size="1" />
	</td>
</tr>
<tr class="print">
	<td class="print-category">
		<?php echo lang_get( 'reporter' ) ?>:
	</td>
	<td class="print">
		<?php print_user_with_subject( $v_reporter_id, $f_bug_id ) ?>
	</td>
	<td class="print-category">
		<?php echo lang_get( 'platform' ) ?>:
	</td>
	<td class="print">
		<?php echo $v_platform ?>
	</td>
	<td class="print" colspan="2">&nbsp;</td>
</tr>
<tr class="print">
	<td class="print-category">
		<?php echo lang_get( 'assigned_to' ) ?>:
	</td>
	<td class="print">
		<?php print_user_with_subject( $v_handler_id, $f_bug_id ) ?>
	</td>
	<td class="print-category">
		<?php echo lang_get( 'os' ) ?>:
	</td>
	<td class="print">
		<?php echo $v_os ?>
	</td>
	<td class="print" colspan="2">&nbsp;</td>
</tr>
<tr class="print">
	<td class="print-category">
		<?php echo lang_get( 'priority' ) ?>:
	</td>
	<td class="print">
		<?php echo get_enum_element( 'priority', $v_priority ) ?>
	</td>
	<td class="print-category">
		<?php echo lang_get( 'os_version' ) ?>:
	</td>
	<td class="print">
		<?php echo $v_os_build ?>
	</td>
	<td class="print" colspan="2">&nbsp;</td>
</tr>
<tr class="print">
	<td class="print-category">
		<?php echo lang_get( 'status' ) ?>:
	</td>
	<td class="print">
		<?php echo get_enum_element( 'status', $v_status ) ?>
	</td>
	<td class="print-category">
		<?php echo lang_get( 'product_version' ) ?>:
	</td>
	<td class="print">
		<?php echo $v_version ?>
	</td>
	<td class="print" colspan="2">&nbsp;</td>
</tr>
<tr class="print">
	<td class="print-category">
		<?php echo lang_get( 'product_build' ) ?>:
	</td>
	<td class="print">
		<?php echo $v_build?>
	</td>
	<td class="print-category">
		<?php echo lang_get( 'resolution' ) ?>:
	</td>
	<td class="print">
		<?php echo get_enum_element( 'resolution', $v_resolution ) ?>
	</td>
	<td class="print" colspan="2">&nbsp;</td>
</tr>
<tr class="print">
	<td class="print-category">
		<?php echo lang_get( 'projection' ) ?>:
	</td>
	<td class="print">
		<?php echo get_enum_element( 'projection', $v_projection ) ?>
	</td>
	<td class="print-category">
		<?php
			if ( !config_get( 'enable_relationship' ) ) {
				echo lang_get( 'duplicate_id' );
			} # MASC RELATIONSHIP
		?>&nbsp;
	</td>
	<td class="print">
		<?php
			if ( !config_get( 'enable_relationship' ) ) {
				print_duplicate_id( $v_duplicate_id );
			} # MASC RELATIONSHIP
		?>&nbsp;
	</td>
	<td class="print" colspan="2">&nbsp;</td>
</tr>
<tr class="print">
	<td class="print-category">
		<?php echo lang_get( 'eta' ) ?>:
	</td>
	<td class="print">
		<?php echo get_enum_element( 'eta', $v_eta ) ?>
	</td>
	<td class="print-category">
		<?php echo lang_get( 'fixed_in_version' ) ?>:
	</td>
	<td class="print">
		<?php echo $v_fixed_in_version ?>
	</td>
	<td class="print" colspan="2">&nbsp;</td>
</tr>

<?php
$t_related_custom_field_ids = custom_field_get_linked_ids( $v_project_id );
foreach( $t_related_custom_field_ids as $t_id ) {
	$t_def = custom_field_get_definition( $t_id );
?>
<tr class="print">
	<td class="print-category">
		<?php echo lang_get_defaulted( $t_def['name'] ) ?>:
	</td>
	<td class="print" colspan="4">
		<?php print_custom_field_value( $t_def, $t_id, $f_bug_id ); ?>
	</td>
</tr>
<?php
}       // foreach
?>

<tr>
	<td class="print-spacer" colspan="6">
		<hr size="1" />
	</td>
</tr>
<tr class="print">
	<td class="print-category">
		<?php echo lang_get( 'summary' ) ?>:
	</td>
	<td class="print" colspan="5">
		<?php echo bug_format_summary( $f_bug_id, SUMMARY_FIELD ) ?>
	</td>
</tr>
<tr class="print">
	<td class="print-category">
		<?php echo lang_get( 'description' ) ?>:
	</td>
	<td class="print" colspan="5">
		<?php echo $v2_description ?>
	</td>
</tr>
<tr class="print">
	<td class="print-category">
		<?php echo lang_get( 'steps_to_reproduce' ) ?>:
	</td>
	<td class="print" colspan="5">
		<?php echo $v2_steps_to_reproduce ?>
	</td>
</tr>
<tr class="print">
	<td class="print-category">
		<?php echo lang_get( 'additional_information' ) ?>:
	</td>
	<td class="print" colspan="5">
		<?php echo $v2_additional_information ?>
	</td>
</tr>
<?php
	# account profile description
	if ( $v_profile_id > 0 ) {
	    $t_user_prof_table = config_get( 'mantis_user_profile_table' );
		$query = "SELECT description
				FROM $t_user_prof_table
				WHERE id='$v_profile_id'";
		$result = db_query( $query );
		$t_profile_description = '';
		if ( db_num_rows( $result ) > 0 ) {
			$t_profile_description = db_result( $result, 0 );
		}
		$t_profile_description = string_display_links( $t_profile_description );

?>
<tr class="print">
	<td class="print-category">
		<?php echo lang_get( 'system_profile' ) ?>:
	</td>
	<td class="print" colspan="5">
		<?php echo $t_profile_description ?>
	</td>
</tr>
<?php
	}

	# MASC RELATIONSHIP
	if ( ON == config_get( 'enable_relationship' ) ) {
		echo "<tr class=\"print\">";
		echo "<td class=\"print-category\">" . lang_get( 'bug_relationships' ) . "</td>";
		echo "<td class=\"print\" colspan=\"5\">" . relationship_get_summary_html_preview( $c_bug_id ) . "</td></tr>";
	}
	# MASC RELATIONSHIP
?>
<tr class="print">
	<td class="print-category">
		<?php echo lang_get( 'attached_files' ) ?>:
	</td>
	<td class="print" colspan="5">
		<?php file_list_attachments ( $f_bug_id ); ?>
	</td>
</tr>
</table>

<?php include( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'print_bugnote_inc.php' ) ?>
