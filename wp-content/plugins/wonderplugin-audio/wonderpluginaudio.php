<?php
/*
Plugin Name: WonderPlugin Audio Player
Plugin URI: http://www.wonderplugin.com
Description: WordPress Audio Player Plugin
Version: 4.9
Author: Magic Hills Pty Ltd
Author URI: http://www.wonderplugin.com
License: Copyright 2015 Magic Hills Pty Ltd, All Rights Reserved
*/

define('WONDERPLUGIN_AUDIO_VERSION', '4.9');
define('WONDERPLUGIN_AUDIO_URL', plugin_dir_url( __FILE__ ));
define('WONDERPLUGIN_AUDIO_PATH', plugin_dir_path( __FILE__ ));
define('WONDERPLUGIN_AUDIO_PLUGIN', basename(dirname(__FILE__)) . '/' . basename(__FILE__));
define('WONDERPLUGIN_AUDIO_PLUGIN_VERSION', '4.9');

require_once 'app/wonderplugin-audio-functions.php';
require_once 'app/class-wonderplugin-audio-controller.php';

class WonderPlugin_Audio_Plugin {
	
	function __construct() {
	
		$this->init();
	}
	
	public function init() {
		
		// init controller
		$this->wonderplugin_audio_controller = new WonderPlugin_Audio_Controller();
		
		add_action( 'admin_menu', array($this, 'register_menu') );
		
		add_shortcode( 'wonderplugin_audio', array($this, 'shortcode_handler') );
		
		add_action( 'init', array($this, 'register_script') );
		add_action( 'wp_enqueue_scripts', array($this, 'enqueue_script') );
		
		if ( is_admin() )
		{
			add_action( 'wp_ajax_wonderplugin_audio_save_config', array($this, 'wp_ajax_save_item') );
			add_action( 'admin_init', array($this, 'admin_init_hook') );
		}
		
		$supportwidget = get_option( 'wonderplugin_audio_supportwidget', 1 );
		if ( $supportwidget == 1 )
		{
			add_filter('widget_text', 'do_shortcode');
		}
	}
	
	function register_menu()
	{
		$settings = $this->get_settings();
		$userrole = $settings['userrole'];
		
		$menu = add_menu_page(
				__('WonderPlugin Audio Player', 'wonderplugin_audio'),
				__('WonderPlugin Audio Player', 'wonderplugin_audio'),
				$userrole,
				'wonderplugin_audio_overview',
				array($this, 'show_overview'),
				WONDERPLUGIN_AUDIO_URL . 'images/logo-16.png' );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_audio_overview',
				__('Overview', 'wonderplugin_audio'),
				__('Overview', 'wonderplugin_audio'),
				$userrole,
				'wonderplugin_audio_overview',
				array($this, 'show_overview' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_audio_overview',
				__('New Audio Player', 'wonderplugin_audio'),
				__('New Audio Player', 'wonderplugin_audio'),
				$userrole,
				'wonderplugin_audio_add_new',
				array($this, 'add_new' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_audio_overview',
				__('Manage Audio Players', 'wonderplugin_audio'),
				__('Manage Audio Players', 'wonderplugin_audio'),
				$userrole,
				'wonderplugin_audio_show_items',
				array($this, 'show_items' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				'wonderplugin_audio_overview',
				__('Settings', 'wonderplugin_audio'),
				__('Settings', 'wonderplugin_audio'),
				'manage_options',
				'wonderplugin_audio_edit_settings',
				array($this, 'edit_settings' ) );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		
		$menu = add_submenu_page(
				null,
				__('View Audio Player', 'wonderplugin_audio'),
				__('View Audio Player', 'wonderplugin_audio'),	
				$userrole,	
				'wonderplugin_audio_show_item',	
				array($this, 'show_item' ));
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
		
		$menu = add_submenu_page(
				null,
				__('Edit Audio Player', 'wonderplugin_audio'),
				__('Edit Audio Player', 'wonderplugin_audio'),
				$userrole,
				'wonderplugin_audio_edit_item',
				array($this, 'edit_item' ) );
		add_action( 'admin_print_styles-' . $menu, array($this, 'enqueue_admin_script') );
	}
	
	function register_script()
	{		
		wp_register_script('wonderplugin-audio-template-script', WONDERPLUGIN_AUDIO_URL . 'app/wonderpluginaudiotemplate.js', array('jquery'), WONDERPLUGIN_AUDIO_VERSION, false);
		wp_register_script('wonderplugin-audio-skins-script', WONDERPLUGIN_AUDIO_URL . 'engine/wonderpluginaudioskins.js', array('jquery'), WONDERPLUGIN_AUDIO_VERSION, false);
		wp_register_script('wonderplugin-audio-script', WONDERPLUGIN_AUDIO_URL . 'engine/wonderpluginaudio.js', array('jquery'), WONDERPLUGIN_AUDIO_VERSION, false);
		wp_register_script('wonderplugin-audio-creator-script', WONDERPLUGIN_AUDIO_URL . 'app/wonderplugin-audio-creator.js', array('jquery'), WONDERPLUGIN_AUDIO_VERSION, false);
		wp_register_style('wonderplugin-audio-admin-style', WONDERPLUGIN_AUDIO_URL . 'wonderpluginaudio.css');
	}
	
	function enqueue_script()
	{
		$addjstofooter = get_option( 'wonderplugin_audio_addjstofooter', 0 );
		if ($addjstofooter == 1)
		{
			wp_enqueue_script('wonderplugin-audio-skins-script', false, array(), false, true);
			wp_enqueue_script('wonderplugin-audio-script', false, array(), false, true);
		}
		else
		{
			wp_enqueue_script('wonderplugin-audio-skins-script');
			wp_enqueue_script('wonderplugin-audio-script');
		}
	}
	
	function enqueue_admin_script($hook)
	{
		wp_enqueue_script('post');
		if (function_exists("wp_enqueue_media"))
		{
			wp_enqueue_media();
		}
		else
		{
			wp_enqueue_script('thickbox');
			wp_enqueue_style('thickbox');
			wp_enqueue_script('media-upload');
		}
		wp_enqueue_script('wonderplugin-audio-template-script');
		wp_enqueue_script('wonderplugin-audio-skins-script');
		wp_enqueue_script('wonderplugin-audio-script');
		wp_enqueue_script('wonderplugin-audio-creator-script');
		wp_enqueue_style('wonderplugin-audio-admin-style');
	}
	
	function admin_init_hook()
	{
		$settings = $this->get_settings();
		$userrole = $settings['userrole'];
		
		if ( !current_user_can($userrole) )
			return;
		
		// change text of history media uploader
		if (!function_exists("wp_enqueue_media"))
		{
			global $pagenow;
			
			if ( 'media-upload.php' == $pagenow || 'async-upload.php' == $pagenow ) {
				add_filter( 'gettext', array($this, 'replace_thickbox_text' ), 1, 3 );
			}
		}
		
		// add meta boxes
		$this->wonderplugin_audio_controller->add_metaboxes();
	}
	
	function replace_thickbox_text($translated_text, $text, $domain) {
		
		if ('Insert into Post' == $text) {
			$referer = strpos( wp_get_referer(), 'wonderplugin-audio' );
			if ( $referer != '' ) {
				return __('Insert into audio', 'wonderplugin_audio' );
			}
		}
		return $translated_text;
	}
	
	function show_overview() {
		
		$this->wonderplugin_audio_controller->show_overview();
	}
	
	function show_items() {
		
		$this->wonderplugin_audio_controller->show_items();
	}
	
	function add_new() {
		
		$this->wonderplugin_audio_controller->add_new();
	}
	
	function show_item() {
		
		$this->wonderplugin_audio_controller->show_item();
	}
	
	function edit_item() {
	
		$this->wonderplugin_audio_controller->edit_item();
	}
	
	function edit_settings() {
	
		$this->wonderplugin_audio_controller->edit_settings();
	}
	
	function register() {
	
		$this->wonderplugin_audio_controller->register();
	}
	
	function get_settings() {
	
		return $this->wonderplugin_audio_controller->get_settings();
	}
	
	function shortcode_handler($atts, $content = null) {
		
		if ( !isset($atts['id']) )
			return __('Please specify a audio id', 'wonderplugin_audio');
		
		$inline_content = (isset($atts['inline']) && ($atts['inline'] == '1')) ?  $content : null;
		
		return $this->wonderplugin_audio_controller->generate_body_code( $atts['id'], $inline_content, false);
	}
	
	function wp_ajax_save_item() {
		
		check_ajax_referer( 'wonderplugin-audio-ajaxnonce', 'nonce' );
		
		$settings = $this->get_settings();
		$userrole = $settings['userrole'];
		
		if ( !current_user_can($userrole) )
			return;
		
		$jsonstripcslash = get_option( 'wonderplugin_audio_jsonstripcslash', 1 );
		if ($jsonstripcslash == 1)
			$json_post = trim(stripcslashes($_POST["item"]));
		else
			$json_post = trim($_POST["item"]);
		
		$items = json_decode($json_post, true);
		
		if ( empty($items) )
		{
			$json_error = "json_decode error";
			if ( function_exists('json_last_error_msg') )
				$json_error .= ' - ' . json_last_error_msg();
			else if ( function_exists('json_last_error') )
				$json_error .= 'code - ' . json_last_error();
				
			header('Content-Type: application/json');
			echo json_encode(array(
					"success" => false,
					"id" => -1,
					"message" => $json_error
			));
			wp_die();
		}
		
		add_filter('safe_style_css', 'wonderplugin_audio_css_allow');
		add_filter('wp_kses_allowed_html', 'wonderplugin_audio_tags_allow', 'post');
		
		foreach ($items as $key => &$value)
		{
			if ($value === true)
				$value = "true";
			else if ($value === false)
				$value = "false";
			else if ( is_string($value) )
				$value = wp_kses_post($value);
		}
				
		if (isset($items["slides"]) && count($items["slides"]) > 0)
		{
			foreach ($items["slides"] as $key => &$slide)
			{
				foreach ($slide as $key => &$value)
				{
					if ($value === true)
						$value = "true";
					else if ($value === false)
						$value = "false";
					else if ( is_string($value) )
						$value = wp_kses_post($value);
				}
			}
		}
		
		remove_filter('wp_kses_allowed_html', 'wonderplugin_audio_tags_allow', 'post');
		remove_filter('safe_style_css', 'wonderplugin_audio_css_allow');
		
		header('Content-Type: application/json');
		echo json_encode($this->wonderplugin_audio_controller->save_item($items));
		wp_die();
		
	}
	
}

/**
 * Init the plugin
 */
$wonderplugin_audio_plugin = new WonderPlugin_Audio_Plugin();

/**
 * Uninstallation
 */
function wonderplugin_audio_uninstall() {
	
	if ( ! current_user_can( 'activate_plugins' ) )
		return;
	
	global $wpdb;
	
	$keepdata = get_option( 'wonderplugin_audio_keepdata', 1 );
	if ( $keepdata == 0 )
	{
		$table_name = $wpdb->prefix . "wonderplugin_audio";
		$wpdb->query("DROP TABLE IF EXISTS $table_name");
	}
}

if ( function_exists('register_uninstall_hook') )
{
	register_uninstall_hook( __FILE__, 'wonderplugin_audio_uninstall' );
}

define('WONDERPLUGIN_AUDIO_VERSION_TYPE', 'F');
