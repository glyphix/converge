<?php 

class WonderPlugin_Audio_Model {

	private $controller;
	
	function __construct($controller) {
		
		$this->controller = $controller;
	}
	
	function get_upload_path() {
		
		$uploads = wp_upload_dir();
		return $uploads['basedir'] . '/wonderplugin-audio/';
	}
	
	function get_upload_url() {
	
		$uploads = wp_upload_dir();
		return $uploads['baseurl'] . '/wonderplugin-audio/';
	}
	
	function generate_body_code($id, $content, $has_wrapper) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
		
		if ( !$this->is_db_table_exists() )
		{
			return '<p>The specified player does not exist.</p>';
		}
		
		$ret = "";
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$data = str_replace('\\\"', '"', $item_row->data);
			$data = str_replace("\\\'", "'", $data);
			
			$data = json_decode(trim($data));
						
			if ( isset($data->publish_status) && ($data->publish_status === 0) )
			{
				return '<p>The specified audio player is trashed.</p>';
			}
			
			add_filter('safe_style_css', 'wonderplugin_audio_css_allow');		
			add_filter('wp_kses_allowed_html', 'wonderplugin_audio_tags_allow', 'post');
			
			foreach($data as &$value)
			{
				if ( is_string($value) )
					$value = wp_kses_post($value);
			}
			
			remove_filter('wp_kses_allowed_html', 'wonderplugin_audio_tags_allow', 'post');
			remove_filter('safe_style_css', 'wonderplugin_audio_css_allow');
			
			if (isset($data->customcss) && strlen($data->customcss) > 0)
			{
				$customcss = str_replace("\r", " ", $data->customcss);
				$customcss = str_replace("\n", " ", $customcss);
				$customcss = str_replace("AUDIOPLAYERID", $id, $customcss);
				$ret .= '<style type="text/css">' . $customcss . '</style>';
			}
			
			if (isset($data->skincss) && strlen($data->skincss) > 0)
			{
				$skincss = str_replace("\r", " ", $data->skincss);
				$skincss = str_replace("\n", " ", $skincss);
				$skincss = str_replace('#amazingaudioplayer-AUDIOPLAYERID',  '#wonderpluginaudio-' . $id, $skincss);
				$ret .= '<style type="text/css">' . $skincss . '</style>';
			}
			
			// div data tag
			$ret .= '<div class="wonderpluginaudio" id="wonderpluginaudio-' . $id . '" data-audioplayerid="' . $id . '" data-width="' . $data->width . '" data-height="' . $data->height . '" data-skin="' . $data->skin . '"';
			
			if (isset($data->dataoptions) && strlen($data->dataoptions) > 0)
			{
				$ret .= ' ' . stripslashes($data->dataoptions);
			}
			
			$boolOptions = array('autoplay', 'random', 'forceflash', 'forcehtml5', 'autoresize', 'responsive', 'showtracklist', 'showprogress', 'showprevnext', 'showloop', 'showloading', 'enablega', 'titleinbarscroll', 'donotinit', 'addinitscript');
			foreach ( $boolOptions as $key )
			{
				if (isset($data->{$key}) )
					$ret .= ' data-' . $key . '="' . ((strtolower($data->{$key}) === 'true') ? 'true': 'false') .'"';
			}
			
			$valOptions = array('loop', 'tracklistitem', 'titleinbarwidth', 'gatrackingid');
			foreach ( $valOptions as $key )
			{
				if (isset($data->{$key}) )
					$ret .= ' data-' . $key . '="' . $data->{$key} . '"';
			}
				
			if ( isset($data->setdefaultvolume) && strtolower($data->setdefaultvolume) === 'true' && isset($data->defaultvolume) )
			{
				$ret .= ' data-defaultvolume="' . $data->defaultvolume . '"';
			}
			
			$ret .= ' data-jsfolder="' . WONDERPLUGIN_AUDIO_URL . 'engine/"'; 
			
			$ret .= ' style="display:block;position:relative;margin:0 auto;';
			
			if ( isset($data->responsive) && strtolower($data->responsive) === 'true' )
				$ret .= 'width:100%;';
			else if ( isset($data->autoresize) && strtolower($data->autoresize) === 'true' )
				$ret .= 'width:100%;max-width:' . $data->width . 'px;';
			else
				$ret .= 'width:' . $data->width . 'px;';
			
			if ($data->heightmode == 'auto')
				$ret .= 'height:auto;';
			else
				$ret .= 'height:' . $data->height . 'px;';
			$ret .= '"';
			
			$ret .= '>';
			
			if ( !empty($content) )
			{
				$ret .= $content;
			}
			else if (isset($data->slides) && count($data->slides) > 0)
			{
				$ret .= '<ul class="amazingaudioplayer-audios" style="display:none;">';
				
				foreach ($data->slides as $slide)
				{		
					foreach($slide as &$value)
					{
						if ( is_string($value) )
							$value = wp_kses_post($value);
					}
					
					$ret .= '<li';
					$ret .= ' data-artist="' . str_replace("\"", "&quot;", $slide->artist) . '"';
					$ret .= ' data-title="' . str_replace("\"", "&quot;", $slide->title) . '"';
					$ret .= ' data-album="' . str_replace("\"", "&quot;", $slide->album) . '"';
					$ret .= ' data-info="' . str_replace("\"", "&quot;", $slide->info) . '"';
					$ret .= ' data-image="' . $slide->image . '"';
					
					if ( isset($slide->live) && strtolower($slide->live) === 'true' )
					{
						$ret .= ' data-live="true"';
						if ( !empty($slide->radionomyradiouid) && strlen($slide->radionomyradiouid) > 0)
							$ret .= ' data-radionomyradiouid="' . $slide->radionomyradiouid . '"';
					}
					else
					{
						$ret .= ' data-duration="' . $slide->duration . '"';
					}
					$ret .= '>';
					
					if ($slide->mp3 && strlen($slide->mp3) > 0)
						$ret .= '<div class="amazingaudioplayer-source" data-src="' . $slide->mp3 . '" data-type="audio/mpeg" ></div>';
					if ($slide->ogg && strlen($slide->ogg) > 0)
						$ret .= '<div class="amazingaudioplayer-source" data-src="' . $slide->ogg . '" data-type="audio/ogg" ></div>';
				
					$ret .= '</li>';
					
				}
				$ret .= '</ul>';
				
			}
			if ('F' == 'F')
				$ret .= '<div class="wonderplugin-engine"><a href="http://www.wonderplugin.com/wordpress-audio-player/" title="'. get_option('wonderplugin-audio-engine')  .'">' . get_option('wonderplugin-audio-engine') . '</a></div>';
			$ret .= '</div>';
			
			if (isset($data->addinitscript) && strtolower($data->addinitscript) === 'true')
			{
				$ret .= '<script>jQuery(document).ready(function(){jQuery(".wonderplugin-engine").css({display:"none"});jQuery(".wonderpluginaudio").wonderpluginaudio({forceinit:true});});</script>';				
			}
		}
		else
		{
			$ret = '<p>The specified audio id does not exist.</p>';
		}
		return $ret;
	}
	
	function delete_item($id) {
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
		
		$ret = $wpdb->query( $wpdb->prepare(
				"
				DELETE FROM $table_name WHERE id=%s
				",
				$id
		) );
		
		return $ret;
	}
	
	function trash_item($id) {
		
		return $this->set_item_status($id, 0);
	}
	
	function restore_item($id) {
		
		return $this->set_item_status($id, 1);
	}
	
	function set_item_status($id, $status) {

		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
		
		$ret = false;
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$data = json_decode($item_row->data, true);
			$data['publish_status'] = $status;
			$data = json_encode($data);
				
			$update_ret = $wpdb->query( $wpdb->prepare( "UPDATE $table_name SET data=%s WHERE id=%d", $data, $id ) );
			if ( $update_ret )
				$ret = true;
		}
		
		return $ret;
	}
	
	function clone_item($id) {
	
		global $wpdb, $user_ID;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
		
		$cloned_id = -1;
		
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$time = current_time('mysql');
			$authorid = $user_ID;
			
			$ret = $wpdb->query( $wpdb->prepare(
					"
					INSERT INTO $table_name (name, data, time, authorid)
					VALUES (%s, %s, %s, %s)
					",
					$item_row->name . " Copy",
					$item_row->data,
					$time,
					$authorid
			) );
				
			if ($ret)
				$cloned_id = $wpdb->insert_id;
		}
	
		return $cloned_id;
	}
	
	function is_db_table_exists() {
	
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
	
		return ( strtolower($wpdb->get_var("SHOW TABLES LIKE '$table_name'")) == strtolower($table_name) );
	}
	
	function is_id_exist($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";

		$audio_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		return ($audio_row != null);
	}
	
	function create_db_table() {
	
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
		
		$charset = '';
		if ( !empty($wpdb -> charset) )
			$charset = "DEFAULT CHARACTER SET $wpdb->charset";
		if ( !empty($wpdb -> collate) )
			$charset .= " COLLATE $wpdb->collate";
	
		$sql = "CREATE TABLE $table_name (
		id INT(11) NOT NULL AUTO_INCREMENT,
		name tinytext DEFAULT '' NOT NULL,
		data MEDIUMTEXT DEFAULT '' NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		authorid tinytext NOT NULL,
		PRIMARY KEY  (id)
		) $charset;";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
	function save_item($item) {
		
		global $wpdb, $user_ID;
		
		if ( !$this->is_db_table_exists() )
		{
			$this->create_db_table();
		
			$create_error = "CREATE DB TABLE - ". $wpdb->last_error;
			if ( !$this->is_db_table_exists() )
			{
				return array(
						"success" => false,
						"id" => -1,
						"message" => $create_error
				);
			}
		}
		
		$table_name = $wpdb->prefix . "wonderplugin_audio";
		
		$id = $item["id"];
		$name = $item["name"];
		
		unset($item["id"]);
		$data = json_encode($item);
		
		if ( empty($data) )
		{
			$json_error = "json_encode error";
			if ( function_exists('json_last_error_msg') )
				$json_error .= ' - ' . json_last_error_msg();
			else if ( function_exists('json_last_error') )
				$json_error .= 'code - ' . json_last_error();
		
			return array(
					"success" => false,
					"id" => -1,
					"message" => $json_error
			);
		}
		
		$time = current_time('mysql');
		$authorid = $user_ID;
		
		if ( ($id > 0) && $this->is_id_exist($id) )
		{
			$ret = $wpdb->query( $wpdb->prepare(
					"
					UPDATE $table_name
					SET name=%s, data=%s, time=%s, authorid=%s
					WHERE id=%d
					",
					$name,
					$data,
					$time,
					$authorid,
					$id
			) );
			
			if (!$ret)
			{
				return array(
						"success" => false,
						"id" => $id, 
						"message" => "UPDATE - ". $wpdb->last_error
					);
			}
		}
		else
		{
			$ret = $wpdb->query( $wpdb->prepare(
					"
					INSERT INTO $table_name (name, data, time, authorid)
					VALUES (%s, %s, %s, %s)
					",
					$name,
					$data,
					$time,
					$authorid
			) );
			
			if (!$ret)
			{
				return array(
						"success" => false,
						"id" => -1,
						"message" => "INSERT - " . $wpdb->last_error
				);
			}
			
			$id = $wpdb->insert_id;
		}
		
		return array(
				"success" => true,
				"id" => intval($id),
				"message" => "Audio published!"
		);
	}
	
	function get_list_data() {
		
		if ( !$this->is_db_table_exists() )
			$this->create_db_table();
		
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
		
		$rows = $wpdb->get_results( "SELECT * FROM $table_name", ARRAY_A);
		
		$ret = array();
		
		if ( $rows )
		{
			foreach ( $rows as $row )
			{
				$ret[] = array(
							"id" => $row['id'],
							'name' => $row['name'],
							'data' => $row['data'],
							'time' => $row['time'],
							'author' => $row['authorid']
						);
			}
		}
	
		return $ret;
	}
	
	function get_item_data($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "wonderplugin_audio";
	
		$ret = "";
		$item_row = $wpdb->get_row( $wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id) );
		if ($item_row != null)
		{
			$ret = $item_row->data;
		}

		return $ret;
	}
	
	function get_settings() {
	
		$userrole = get_option( 'wonderplugin_audio_userrole' );
		if ( $userrole == false )
		{
			update_option( 'wonderplugin_audio_userrole', 'manage_options' );
			$userrole = 'manage_options';
		}
	
		$keepdata = get_option( 'wonderplugin_audio_keepdata', 1 );
		
		$disableupdate = get_option( 'wonderplugin_audio_disableupdate', 0 );
		
		$supportwidget = get_option( 'wonderplugin_audio_supportwidget', 1 );
		
		$addjstofooter = get_option( 'wonderplugin_audio_addjstofooter', 0 );
		
		$jsonstripcslash = get_option( 'wonderplugin_audio_jsonstripcslash', 1 );
		
		$settings = array(
				"userrole" => $userrole,
				"keepdata" => $keepdata,
				"disableupdate" => $disableupdate,
				"supportwidget" => $supportwidget,
				"addjstofooter" => $addjstofooter,
				"jsonstripcslash" => $jsonstripcslash
		);
	
		return $settings;
	}
	
	function save_settings($options) {
	
		if (!isset($options) || !isset($options['userrole']))
			$userrole = 'manage_options';
		else if ( $options['userrole'] == "Editor")
			$userrole = 'moderate_comments';
		else if ( $options['userrole'] == "Author")
			$userrole = 'upload_files';
		else
			$userrole = 'manage_options';
		update_option( 'wonderplugin_audio_userrole', $userrole );
	
		if (!isset($options) || !isset($options['keepdata']))
			$keepdata = 0;
		else
			$keepdata = 1;
		update_option( 'wonderplugin_audio_keepdata', $keepdata );
		
		if (!isset($options) || !isset($options['disableupdate']))
			$disableupdate = 0;
		else
			$disableupdate = 1;
		update_option( 'wonderplugin_audio_disableupdate', $disableupdate );
		
		if (!isset($options) || !isset($options['supportwidget']))
			$supportwidget = 0;
		else
			$supportwidget = 1;
		update_option( 'wonderplugin_audio_supportwidget', $supportwidget );
		
		if (!isset($options) || !isset($options['addjstofooter']))
			$addjstofooter = 0;
		else
			$addjstofooter = 1;
		update_option( 'wonderplugin_audio_addjstofooter', $addjstofooter );
		
		if (!isset($options) || !isset($options['jsonstripcslash']))
			$jsonstripcslash = 0;
		else
			$jsonstripcslash = 1;
		update_option( 'wonderplugin_audio_jsonstripcslash', $jsonstripcslash );
	}
	
	function get_plugin_info() {
	
		$info = get_option('wonderplugin_audio_information');
		if ($info === false)
			return false;
	
		return unserialize($info);
	}
	
	function save_plugin_info($info) {
	
		update_option( 'wonderplugin_audio_information', serialize($info) );
	}
	
	function check_license($options) {
	
		$ret = array(
				"status" => "empty"
		);
	
		if ( !isset($options) || empty($options['wonderplugin-audio-key']) )
		{
			return $ret;
		}
	
		$key = sanitize_text_field( $options['wonderplugin-audio-key'] );
		if ( empty($key) )
			return $ret;
	
		$update_data = $this->controller->get_update_data('register', $key);
		if( $update_data === false )
		{
			$ret['status'] = 'timeout';
			return $ret;
		}
	
		if ( isset($update_data->key_status) )
			$ret['status'] = $update_data->key_status;
	
		return $ret;
	}
	
	function deregister_license($options) {
	
		$ret = array(
				"status" => "empty"
		);
	
		if ( !isset($options) || empty($options['wonderplugin-audio-key']) )
			return $ret;
	
		$key = sanitize_text_field( $options['wonderplugin-audio-key'] );
		if ( empty($key) )
			return $ret;
	
		$info = $this->get_plugin_info();
		$info->key = '';
		$info->key_status = 'empty';
		$info->key_expire = 0;
		$this->save_plugin_info($info);
	
		$update_data = $this->controller->get_update_data('deregister', $key);
		if ($update_data === false)
		{
			$ret['status'] = 'timeout';
			return $ret;
		}
	
		$ret['status'] = 'success';
	
		return $ret;
	}
}
