<?php
/**
 * Plugin Name: Custom Post Static Index
 * Plugin URI: https://levidps.com
 * Description: A plugin to add static page selection for custom post types, visible on the 'reading' options page
 * Version: 1.0
 * Author: Levidps
 * Author URI: https://levidps.com/
 * License: GPL2
 */

/*  Copyright 2017 Levidps

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

if ( !defined( 'ABSPATH' ) ) exit;

if ( !function_exists('ldps_custom_posts_init') ) :

	function ldps_custom_posts_init() {

		/**
		 * Add index page options to the BE for events & sponsors
		 */

		/* Create Settings Section */
		add_settings_section(
			'ldps_custom_post_section',
			'<hr><br/>Index Pages for Custom Posts',
			'',
			'reading'
		);

		/* Create Settings Field */
		add_settings_field(
			'ldps_page_indexes',
			'Custom post index pages',
			'ldps_page_indexes_callback',
			'reading',
			'ldps_custom_post_section'
		);

		$_type_args = array('_builtin' => false );
		$_types = get_post_types($_type_args, 'names');

		if ($_types) :
			foreach ($_types as $_type) :
				if ( !strpos($_type, 'acf') ) :

					register_setting('reading', 'ldps_' . $_type . '_page');

				endif;
			endforeach;
		endif;

	}

	/** Init settings on Admin Init */
	add_action('admin_init', 'ldps_custom_posts_init');

	function ldps_page_indexes_callback() {
		$_type_args = array('_builtin' => false );
		$_types = get_post_types($_type_args, 'names');

		if ($_types) :

			// Wrapper Element
			echo '<ul>';

			// For each post type
			foreach ($_types as $_type) :
				if ( strpos($_type, 'acf') === false ) :

					// Args
					$args = array(
						'post_type' => 'page',
						'name' => 'ldps_' . $_type . '_page',
						'id' => 'ldps_' . $_type . '_page',
						'show_option_none' => 'Select Page',
						'echo' => 1,
						'selected' => get_option('ldps_' . $_type . '_page'),
					);

					// Return list items with dropdown
					echo '<li><label for="ldps_' . $_type . '_page">' . ucfirst($_type) . ' Page: ';
						wp_dropdown_pages($args);
					echo '</label></li>';

				endif;
			endforeach;

			echo '</ul>';

		endif;
	}

endif;