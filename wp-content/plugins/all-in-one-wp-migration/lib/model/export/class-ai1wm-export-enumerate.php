<?php
/**
 * Copyright (C) 2014-2016 ServMask Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ███████╗███████╗██████╗ ██╗   ██╗███╗   ███╗ █████╗ ███████╗██╗  ██╗
 * ██╔════╝██╔════╝██╔══██╗██║   ██║████╗ ████║██╔══██╗██╔════╝██║ ██╔╝
 * ███████╗█████╗  ██████╔╝██║   ██║██╔████╔██║███████║███████╗█████╔╝
 * ╚════██║██╔══╝  ██╔══██╗╚██╗ ██╔╝██║╚██╔╝██║██╔══██║╚════██║██╔═██╗
 * ███████║███████╗██║  ██║ ╚████╔╝ ██║ ╚═╝ ██║██║  ██║███████║██║  ██╗
 * ╚══════╝╚══════╝╚═╝  ╚═╝  ╚═══╝  ╚═╝     ╚═╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝
 */

class Ai1wm_Export_Enumerate {

	public static function execute( $params ) {

		// Set progress
		Ai1wm_Status::info( __( 'Retrieving a list of all WordPress files...', AI1WM_PLUGIN_NAME ) );

		// Set exclude filters
		$exclude_filters = ai1wm_content_filters();

		// Exclude cache
		if ( isset( $params['options']['no_cache'] ) ) {
			$exclude_filters[] = 'cache';
		}

		// Exclude themes
		if ( isset( $params['options']['no_themes'] ) ) {
			$exclude_filters[] = 'themes';
		} else {
			$inactive_themes = array();

			// Exclude inactive themes
			if ( isset( $params['options']['no_inactive_themes'] ) ) {
				foreach ( wp_get_themes() as $theme => $info ) {
					// Exclude current parent and child themes
					if ( ! in_array( $theme, array( get_template(), get_stylesheet() ) ) ) {
						$inactive_themes[] = 'themes' . DIRECTORY_SEPARATOR . $theme;
					}
				}
			}

			// Set exclude filters
			$exclude_filters = array_merge( $exclude_filters, $inactive_themes );
		}

		// Exclude must-use plugins
		if ( isset( $params['options']['no_muplugins'] ) ) {
			$exclude_filters = array_merge( $exclude_filters, array( 'mu-plugins' ) );
		}

		// Exclude plugins
		if ( isset( $params['options']['no_plugins'] ) ) {
			$exclude_filters = array_merge( $exclude_filters, array( 'plugins' ) );
		} else {
			$inactive_plugins = array();

			// Exclude inactive plugins
			if ( isset( $params['options']['no_inactive_plugins'] ) ) {
				foreach ( get_plugins() as $basename => $plugin ) {
					if ( is_plugin_inactive( $basename ) ) {
						if ( dirname( $basename ) === '.' ) {
							$inactive_plugins[] = 'plugins' . DIRECTORY_SEPARATOR . basename( $basename );
						} else {
							$inactive_plugins[] = 'plugins' . DIRECTORY_SEPARATOR . dirname( $basename );
						}
					}
				}
			}

			// Set exclude filters
			$exclude_filters = array_merge( $exclude_filters, ai1wm_plugin_filters( $inactive_plugins ) );
		}

		// Exclude media
		if ( isset( $params['options']['no_media'] ) ) {
			$exclude_filters = array_merge( $exclude_filters, array( 'uploads', 'blogs.dir' ) );
		}

		// Get total files
		if ( isset( $params['total_files'] ) ) {
			$total_files = (int) $params['total_files'];
		} else {
			$total_files = 0;
		}

		// Get total size
		if ( isset( $params['total_size'] ) ) {
			$total_size = (int) $params['total_size'];
		} else {
			$total_size = 0;
		}

		// Create map file
		$filemap = fopen( ai1wm_filemap_path( $params ) , 'a+' );

		try {

			// Iterate over content directory
			$iterator = new Ai1wm_Recursive_Directory_Iterator( WP_CONTENT_DIR );

			// Exclude uploads, plugins or themes
			$iterator = new Ai1wm_Recursive_Exclude_Filter( $iterator, apply_filters( 'ai1wm_exclude_content_from_export', $exclude_filters ) );

			// Recursively iterate over content directory
			$iterator = new RecursiveIteratorIterator( $iterator, RecursiveIteratorIterator::LEAVES_ONLY, RecursiveIteratorIterator::CATCH_GET_CHILD );

			// Write path line
			foreach ( $iterator as $item ) {
				if ( $item->isFile() ) {
					if ( fwrite( $filemap, $iterator->getSubPathName() . PHP_EOL ) ) {
						$total_files++;

						// Add current file size
						$total_size += filesize( $iterator->getPathname() );
					}
				}
			}
		} catch ( Exception $e ) {
			// Skip bad file permissions
		}

		// Set total files
		$params['total_files'] = $total_files;

		// Set total size
		$params['total_size'] = $total_size;

		// Close the filemap file
		fclose( $filemap );

		// Set progress
		Ai1wm_Status::info( __( 'Done retrieving a list of all WordPress files.', AI1WM_PLUGIN_NAME ) );

		return $params;
	}
}
