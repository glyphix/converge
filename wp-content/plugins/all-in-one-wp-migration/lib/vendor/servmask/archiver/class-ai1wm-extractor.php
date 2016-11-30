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

class Ai1wm_Extractor extends Ai1wm_Archiver {

	/**
	 * Overloaded constructor that opens the passed file for reading
	 *
	 * @param string $file File to use as archive
	 */
	public function __construct( $file ) {
		// call parent, to initialize variables
		parent::__construct( $file );
	}

	/**
	 * Extract files from archive to specified location
	 *
	 * @param string $location Location where the files should be extracted
	 * @param int    $seek     Location in the file to start exporting data from
	 */
	public function extract_files( $location, $seek = 0 ) {

	}

	/**
	 * Get the total files in an archive
	 *
	 * @return int Total files in the archive
	 * @throws \Ai1wm_Not_Accesible_Exception
	 * @throws \Ai1wm_Not_Readable_Exception
	 */
	public function get_total_files() {
		fseek( $this->file_handle, SEEK_SET, 0 );

		// total files
		$total_files = 0;

		while ( $block = $this->read_from_handle( $this->file_handle, 4377, $this->filename ) ) {
			// end block has been reached
			if ( $block === $this->eof ) {
				continue;
			}

			// get file data from the block
			$data = $this->get_data_from_block( $block );

			// we have a file, increment the counter
			$total_files++;

			// skip file content so we can move forward to the next file
			$this->set_file_pointer( $this->file_handle, $data['size'], $this->filename );
		}

		return $total_files;
	}

	/**
	 * Get the total size of files in an archive
	 *
	 * @return int Total size of files in the archive
	 * @throws \Ai1wm_Not_Accesible_Exception
	 * @throws \Ai1wm_Not_Readable_Exception
	 */
	public function get_total_size() {
		fseek( $this->file_handle, SEEK_SET, 0 );

		// total size
		$total_size = 0;

		while ( $block = $this->read_from_handle( $this->file_handle, 4377, $this->filename ) ) {
			// end block has been reached
			if ( $block === $this->eof ) {
				continue;
			}

			// get file data from the block
			$data = $this->get_data_from_block( $block );

			// we have a file, increment the counter
			$total_size += $data['size'];

			// skip file content so we can move forward to the next file
			$this->set_file_pointer( $this->file_handle, $data['size'], $this->filename );
		}

		return $total_size;
	}

	public function extract_one_file_to( $location, $exclude = array(), $old_paths = array(), $new_paths = array(), $offset = 0, $timeout = 0 ) {
		if ( false === is_dir( $location ) ) {
			throw new Ai1wm_Not_Readable_Exception( sprintf( __( '%s doesn\'t exist', AI1WM_PLUGIN_NAME ), $location ) );
		}

		$block = $this->read_from_handle( $this->file_handle, 4377, $this->filename );

		// we reached end of file, set the pointer to the end of the file so that feof returns true
		if ( $block === $this->eof ) {
			@fseek( $this->file_handle, 1, SEEK_END );
			@fgetc( $this->file_handle );
			return;
		}

		// get file data from header block
		$data = $this->get_data_from_block( $block );

		// set filename
		if ( $data['path'] === '.' ) {
			$filename = $data['filename'];
		} else {
			$filename = $data['path'] . '/' . $data['filename'];
		}

		// we need to build the path
		$path = str_replace( '/', DIRECTORY_SEPARATOR, $data['path'] );

		// we need to build the path for the file
		$filename = str_replace( '/', DIRECTORY_SEPARATOR, $filename );

		// should we skip this file?
		for ( $i = 0; $i < count( $exclude ); $i++ ) {
			if ( strpos( $filename . DIRECTORY_SEPARATOR, $exclude[$i] . DIRECTORY_SEPARATOR ) === 0 ) {
				$this->set_file_pointer( $this->file_handle, $data['size'], $this->filename );
				return;
			}
		}

		// replace extract paths
		for ( $i = 0; $i < count( $old_paths ); $i++ ) {
			if ( strpos( $path . DIRECTORY_SEPARATOR, $old_paths[$i] . DIRECTORY_SEPARATOR ) === 0 ) {
				$path = substr_replace( $path, $new_paths[$i], 0, strlen( $old_paths[$i] ) );
				break;
			}
		}

		// check if location doesn't exist, then create it
		if ( false === is_dir( $location . DIRECTORY_SEPARATOR . $path ) ) {
			mkdir( $location . DIRECTORY_SEPARATOR . $path, 0755, true );
		}

		try {
			// we have a match, let's extract the file
			if ( ( $offset = $this->extract_to( $location . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . basename( $filename ), $data, $offset, $timeout ) ) ) {
				return $offset;
			}
		} catch ( Exception $e ) {
			// we don't have file permissions, skip file content
			$this->set_file_pointer( $this->file_handle, $data['size'], $this->filename );
		}
	}

	/**
	 * Extract specific files from archive
	 *
	 * @param string $location Location where to extract files
	 * @param array  $files    Files to extract
	 * @param array  $offset   File offset
	 * @param int    $timeout  Process timeout
	 */
	public function extract_by_files_array( $location, $files = array(), $offset = 0, $timeout = 0 ) {
		if ( false === is_dir( $location ) ) {
			throw new Ai1wm_Not_Readable_Exception( sprintf( __( '%s doesn\'t exist', AI1WM_PLUGIN_NAME ), $location ) );
		}

		// we read until we reached the end of the file, or the files we were looking for were found
		while ( ( $block = $this->read_from_handle( $this->file_handle, 4377, $this->filename ) ) ) {
			// end block has been reached and we still have files to extract
			// that means the files don't exist in the archive
			if ( $block === $this->eof ) {
				// we reached end of file, set the pointer to the end of the file so that feof returns true
				@fseek( $this->file_handle, 1, SEEK_END );
				@fgetc( $this->file_handle );
				return;
			}

			$data = $this->get_data_from_block( $block );

			// set filename
			if ( $data['path'] === '.' ) {
				$filename = $data['filename'];
			} else {
				$filename = $data['path'] . '/' . $data['filename'];
			}

			// we need to build the path
			$path = str_replace( '/', DIRECTORY_SEPARATOR, $data['path'] );

			// we need to build the path for the file
			$filename = str_replace( '/', DIRECTORY_SEPARATOR, $filename );

			// set include flag
			$include = false;

			// files to extract
			for ( $i = 0; $i < count( $files ); $i++ ) {
				if ( strpos( $filename . DIRECTORY_SEPARATOR, $files[$i] . DIRECTORY_SEPARATOR ) === 0 ) {
					$include = true;
					break;
				}
			}

			// do we have a match?
			if ( $include ) {
				// check if location doesn't exist, then create it
				if ( false === is_dir( $location . DIRECTORY_SEPARATOR . $path ) ) {
					mkdir( $location . DIRECTORY_SEPARATOR . $path, 0755, true );
				}

				try {
					// we have a match, let's extract the file and remove it from the array
					if ( ( $offset = $this->extract_to( $location . DIRECTORY_SEPARATOR . $filename, $data, $offset, $timeout ) ) ) {
						return $offset;
					}
				} catch ( Exception $e ) {
					// we don't have file permissions, skip file content
					$this->set_file_pointer( $this->file_handle, $data['size'], $this->filename );
				}
			} else {
				// we don't have a match, skip file content
				$this->set_file_pointer( $this->file_handle, $data['size'], $this->filename );
			}
		}
	}

	public function set_file_pointer( $handle = null, $offset = 0, $file = '' ) {
		// if null is used, we use the archive handle
		if ( is_null( $handle ) ) {
			$handle = $this->file_handle;
		}

		// if filename is empty, we use archive filename
		if ( empty( $file ) ) {
			$file = $this->filename;
		}

		// do we have offset to apply?
		if ( $offset > 0 ) {
			// set position to current location plus offset
			$result = fseek( $handle, $offset, SEEK_CUR );

			if ( -1 === $result ) {
				throw new Ai1wm_Not_Accesible_Exception(
					sprintf(
						__( 'Unable to seek to offset %d on %s', AI1WM_PLUGIN_NAME ),
						$offset,
						$file
					)
				);
			}
		}
	}

	private function extract_to( $file, $data, $offset = 0, $timeout = 0 ) {
		// should the extract overwrite the file if it exists?
		if ( $offset ) {
			$handle = $this->open_file_for_writing( $file );
		} else {
			$handle = $this->open_file_for_overwriting( $file );
		}

		// get data file pointer
		$data_file_pointer = $this->get_file_pointer();

		// set data file pointer
		$this->set_file_pointer( $this->file_handle, $offset, $this->filename );

		// set file size
		$data['size'] -= $offset;

		// start time
		$start = microtime( true );

		// is the filesize more than 0 bytes?
		while ( $data['size'] > 0 ) {
			// read the file in chunks of 512KB
			$chunk_size = $data['size'] > 512000 ? 512000 : $data['size'];

			// read the file in chunks of 512KB from archiver
			$content = $this->read_from_handle( $this->file_handle, $chunk_size, $this->filename );

			// remove the amount of bytes we read
			$data['size'] -= $chunk_size;

			// write file contents
			$this->write_to_handle( $handle, $content, $file );

			// time elapsed
			if ( $timeout ) {
				if ( ( microtime( true ) - $start ) > $timeout ) {
					// set file offset
					$offset = $this->get_file_pointer() - $data_file_pointer;

					// close the handle
					fclose( $handle );

					// get file offset
					return $offset;
				}
			}
		}

		// close the handle
		fclose( $handle );

		// let's apply last modified date
		$this->set_mtime_of_file( $file, $data['mtime'] );

		// all files should chmoded to 755
		$this->set_file_mode( $file, 0644 );
	}

	private function set_mtime_of_file( $file, $mtime ) {
		return @touch( $file, $mtime );
	}

	private function set_file_mode( $file, $mode = 0644 ) {
		return @chmod( $file, $mode );
	}

	private function get_data_from_block( $block ) {
		// prepare our array keys to unpack
		$format = array(
			$this->block_format[0] . 'filename/',
			$this->block_format[1] . 'size/',
			$this->block_format[2] . 'mtime/',
			$this->block_format[3] . 'path',
		);
		$format = implode( '', $format );

		$data = unpack( $format, $block );

		$data['filename'] = trim( $data['filename'] );
		$data['size']     = trim( $data['size'] );
		$data['mtime']    = trim( $data['mtime'] );
		$data['path']     = trim( $data['path'] );

		// current file size
		$this->current_filesize = $data['size'];

		return $data;
	}

	/**
	 * Check if file has reached end of file
	 * Returns true if file has NOT reached eof, false otherwise
	 *
	 * @return bool
	 */
	public function has_not_reached_eof() {
		return ! feof( $this->file_handle );
	}

	/**
	 * Get current file pointer
	 *
	 * return int
	 */
	public function get_file_pointer() {
		$result = ftell( $this->file_handle );

		if ( false === $result ) {
			throw new Ai1wm_Not_Accesible_Exception(
				sprintf(
					__( 'Unable to get current pointer position of %s', AI1WM_PLUGIN_NAME ),
					$this->filename
				)
			);
		}

		return $result;
	}
}
