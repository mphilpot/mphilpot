<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: proj_doc_page.php,v 1.45 2005/02/27 21:25:36 prichards Exp $
	# --------------------------------------------------------

	require_once( 'core.php' );

	$t_core_path = config_get( 'core_path' );

	require_once( $t_core_path.'string_api.php' );

	# Check if project documentation feature is enabled.
	if ( OFF == config_get( 'enable_project_documentation' ) || !file_is_uploading_enabled() ) {
		access_denied();
	}

	$t_project_id = helper_get_current_project();
	$t_mantis_project_file_table = config_get( 'mantis_project_file_table' );

	if( $t_project_id != ALL_PROJECTS ) {
		# Select all the project files
		$query = "SELECT *
				FROM $t_mantis_project_file_table
				WHERE project_id='$t_project_id' or project_id='". ALL_PROJECTS . "'
				ORDER BY project_id, title ASC";
	}
	else {
		# Select the project files (specific project + all-projects docs)
		$query = "SELECT *
				FROM $t_mantis_project_file_table
				ORDER BY project_id, title ASC";
	}
	$result = db_query( $query );
	$num_files = db_num_rows( $result );

	html_page_top1( lang_get( 'docs_link' ) );
	html_page_top2();
?>
<br />
<div align="center">
<table class="width100" cellspacing="1">
<tr>
	<td class="form-title">
		<?php echo lang_get( 'project_documentation_title' ) ?>
	</td>
	<td class="right">
		<?php print_doc_menu( 'proj_doc_page.php' ) ?>
	</td>
</tr>
<?php
	for ($i=0;$i<$num_files;$i++) {
		$row = db_fetch_array( $result );
		extract( $row, EXTR_PREFIX_ALL, 'v' );
		$v_filesize = number_format( $v_filesize );
		$v_title = string_display( $v_title );
		$v_description = string_display_links( $v_description );
		$v_date_added = date( config_get( 'normal_date_format' ), db_unixtimestamp( $v_date_added ) );

		if( access_has_project_level( config_get( 'view_proj_doc_threshold' ), $v_project_id ) ) {
			# check access rights for each document
?>
<tr valign="top" <?php echo helper_alternate_class( $i ) ?>>
	<td>
<?php
			$t_href = '<a href="file_download.php?file_id='.$v_id.'&amp;type=doc">';
			echo $t_href;
			print_file_icon( $v_filename );
			echo '</a>&nbsp;' . $t_href . $v_title . '</a> ('.$v_filesize.' bytes)';
?>
		<br />
		<span class="small">
<?php
			if( $v_project_id == ALL_PROJECTS ) {
				echo lang_get( 'all_projects' ) . '<br/>';
			}
			elseif( $v_project_id != $t_project_id ) {
				$t_project_name = project_get_name( $v_project_id );
				echo $t_project_name . '<br/>';
			}
			echo '(' . $v_date_added . ')';
			if ( access_has_project_level( config_get( 'manage_project_threshold' ), $v_project_id ) ) {
				echo '&nbsp;';
				print_bracket_link( 'proj_doc_edit_page.php?file_id='.$v_id, lang_get( 'edit_link' ) );
				echo '&nbsp;';
				print_bracket_link( 'proj_doc_delete.php?file_id=' . $v_id . '&title=' . string_url( $v_title ), lang_get( 'delete_link' ) );
			}
?>
		</span>
	</td>
	<td>
		<?php echo $v_description ?>
	</td>
</tr>
<?php
		} # end access right check
	} # end for loop
?>
</table>
</div>

<?php html_page_bottom1( __FILE__ ) ?>
