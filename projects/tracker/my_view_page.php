<?php
	# Mantis - a php based bugtracking system
	# Copyright (C) 2000 - 2002  Kenzaburo Ito - kenito@300baud.org
	# Copyright (C) 2002 - 2004  Mantis Team   - mantisbt-dev@lists.sourceforge.net
	# This program is distributed under the terms and conditions of the GPL
	# See the README and LICENSE files for details

	# --------------------------------------------------------
	# $Id: my_view_page.php,v 1.11 2005/04/22 22:27:50 prichards Exp $
	# --------------------------------------------------------
?>
<?php
	require_once( 'core.php' );

	require_once( $t_core_path . 'compress_api.php' );
	require_once( $t_core_path . 'filter_api.php' );

	auth_ensure_user_authenticated();

	$t_current_user_id = auth_get_current_user_id();

	compress_enable();

	html_page_top1( lang_get( 'my_view_link' ) );

	if ( current_user_get_pref( 'refresh_delay' ) > 0 ) {
		html_meta_redirect( 'my_view_page.php', current_user_get_pref( 'refresh_delay' )*60 );
	}

	html_page_top2();

	$f_page_number		= gpc_get_int( 'page_number', 1 );

	$t_per_page = config_get( 'my_view_bug_count' );
	$t_bug_count = null;
	$t_page_count = null;

	$t_boxes = config_get( 'my_view_boxes' );
	asort ($t_boxes);
	reset ($t_boxes);
	#print_r ($t_boxes);

	$t_project_id = helper_get_current_project();
?>

<div align="center">
<table class="hide" border="0" cellspacing="3" cellpadding="0">

<?php
	if ( STATUS_LEGEND_POSITION_TOP == config_get( 'status_legend_position' ) ) {
		echo '<tr>';
		echo '<td colspan="2">';
		html_status_legend();
		echo '</td>';
		echo '</tr>';
	}
	if ( ON == config_get( 'status_percentage_legend' ) ) {
		echo '<tr>';
		echo '<td colspan="2">';
		html_status_percentage_legend();
		echo '</td>';
		echo '</tr>';
	}
?>

<?php
	$t_number_of_boxes = count ( $t_boxes );
	$t_boxes_position = config_get( 'my_view_boxes_fixed_position' );
	$t_counter = 0;

	while (list ($t_box_title, $t_box_display) = each ($t_boxes)) {
		# don't display bugs that are set as 0
		if ($t_box_display == 0) {
			$t_number_of_boxes = $t_number_of_boxes - 1;
		}

		# don't display "Assigned to Me" bugs to users that bugs can't be assigned to
		else if ( $t_box_title == 'assigned' && ( current_user_is_anonymous() OR user_get_assigned_open_bug_count( $t_current_user_id, $t_project_id ) == 0 ) ) {
			$t_number_of_boxes = $t_number_of_boxes - 1;
		}

		# don't display "Monitored by Me" bugs to users that can't monitor bugs
		else if ( $t_box_title == 'monitored' && ( current_user_is_anonymous() OR !access_has_project_level( config_get( 'monitor_bug_threshold' ), $t_project_id, $t_current_user_id ) ) ) {
			$t_number_of_boxes = $t_number_of_boxes - 1;
		}

		# don't display "Reported by Me" bugs to users that can't report bugs
		else if ( $t_box_title == 'reported' && ( current_user_is_anonymous() OR !access_has_project_level( config_get( 'report_bug_threshold' ), $t_project_id, $t_current_user_id ) ) ) {
			$t_number_of_boxes = $t_number_of_boxes - 1;
		}

		# display the box
		else {
			$t_counter++;

			# check the style of displaying boxes - fixed (ie. each box in a separate table cell) or not
			if ( ON == $t_boxes_position ) {
				# for even box number start new row and column
				if ( 1 == $t_counter%2 ) {
					echo '<tr><td valign="top" width="50%">';
					include( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'my_view_inc.php' );
					echo '</td>';
				}

				# for odd box number only start new column
				elseif ( 0 == $t_counter%2 ) {
					echo '<td valign="top" width="50%">';
					include( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'my_view_inc.php' );
					echo '</td></tr>';
				}

				# for odd number of box display one empty table cell in second column
				if ( ( $t_counter == $t_number_of_boxes ) && 1 == $t_counter%2 ) {
					echo '<td valign="top" width="50%"></td></tr>';
				}
			}
			else if ( OFF == $t_boxes_position ) {
				# start new table row and column for first box
				if ( 1 == $t_counter ) {
					echo '<tr><td valign="top" width="50%">';
				}

				# start new table column for the second half of boxes
				if ( $t_counter == ceil ($t_number_of_boxes/2) + 1 ) {
					echo '<td valign="top" width="50%">';
				}

				# display the required box
				include( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'core' . DIRECTORY_SEPARATOR . 'my_view_inc.php' );
				echo '<br />';

				# close the first column for first half of boxes
				if ( $t_counter == ceil ($t_number_of_boxes/2) ) {
					echo '</td>';
				}

				# close the table row after all of the boxes
				if ( $t_counter == $t_number_of_boxes ) {
					echo '</td></tr>';
				}
			}
		}
	}

?>

<?php
	if ( STATUS_LEGEND_POSITION_BOTTOM == config_get( 'status_legend_position' ) ) {
		echo '<tr>';
		echo '<td colspan="2">';
		html_status_legend();
		echo '</td>';
		echo '</tr>';
	}
?>

</table>
</div>

<?php
	html_page_bottom1( __FILE__ );
?>
