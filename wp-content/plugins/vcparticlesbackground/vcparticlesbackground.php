<?php  
/* 
Plugin Name: VC Particles Background
Version: 1.2
Author: Boom-Apps
Author URI: http://codecanyon.net/user/boom-apps
Description: particle.js backgrounds for Visual Composer
*/  

class VC_Particles_Background {
	function backendElement() {
		vc_map( array(
            "name" => __("VC Particles Background", 'vc_extend'),
            "description" => __("Creative backgrounds!", 'vc_extend'),
            "base" => "vc_particles_background",
            "class" => "",
            "controls" => "full",
            "params" => array(
              array("save_always" => true, "type" => "textarea", "holder" => "div", "class" => "", "heading" => "Id - unique per page", "param_name" => "theid", "value" => "vcparticlesbackground_".time()."_".rand(100000,999999999) ),
              array("save_always" => true, "type" => "textarea",  "class" => "", "heading" => "Particles number", "param_name" => "particles_number_value", "value" => "80", "description" => "Integer (0-1000)" ),
              array("save_always" => true, "type" => "dropdown",  "class" => "", "heading" => "Particle type", "param_name" => "particles_shape_type", "value" => array("Circle","Image","Polygon","Star"), "description" => "" ),
              array("save_always" => true, "dependency" => array("element" => "particles_shape_type", "value" => array("Image")), "type" => "attach_image",  "class" => "", "heading" => "Particle image", "param_name" => "particles_shape_image_src", "value" => "", "description" => "For image particle type only!" ),
              array("save_always" => true, "dependency" => array("element" => "particles_shape_type", "value" => array("Circle","Polygon","Star")), "type" => "colorpicker",  "class" => "", "heading" => "Particle color", "param_name" => "particles_color", "value" => "000000", "description" => "Not for image particle type!" ),
              array("save_always" => true, "dependency" => array("element" => "particles_shape_type", "value" => array("Circle","Polygon","Star")), "type" => "colorpicker",  "class" => "", "heading" => "Particle stroke color", "param_name" => "particles_shape_stroke_color", "value" => "000000", "description" => "Not for image particle type!" ),
              array("save_always" => true, "dependency" => array("element" => "particles_shape_type", "value" => array("Circle","Polygon","Star")), "type" => "textarea",  "class" => "", "heading" => "Particle stroke width", "param_name" => "particles_shape_stroke_width", "value" => "0", "description" => "Not for image particle type! Integer (0-20)" ),
              array("save_always" => true, "dependency" => array("element" => "particles_shape_type", "value" => array("Polygon")), "type" => "textarea",  "class" => "", "heading" => "Polygon particle sides", "param_name" => "particles_shape_polygon_nb_sides", "value" => "5", "description" => "For polygon particle type only! Integer (3-20)" )
            )
        ) );
		

		vc_add_param("vc_particles_background", array( "save_always" => true, "type" => "textarea",  "group" => "Sizing", "class" => "", "heading" => "Particle size", "param_name" => "particles_size_value", "value" => "5", "description" => "Integer (0-500)" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "type" => "dropdown",  "group" => "Sizing", "class" => "", "heading" => "Randomize particle size", "param_name" => "particles_size_random", "value" => array("Yes" => "true", "No" => "false")));
		vc_add_param("vc_particles_background", array( "save_always" => true, "type" => "dropdown",  "group" => "Sizing", "class" => "", "heading" => "Animate particle size", "param_name" => "particles_size_anim_enable", "value" =>  array("No" => "false", "Yes" => "true") ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_size_anim_enable", "value" => array("true")), "type" => "textarea",  "group" => "Sizing", "class" => "", "heading" => "Animation speed", "param_name" => "particles_size_anim_speed", "value" => "40", "description" => "Integer (0-300)" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_size_anim_enable", "value" => array("true")), "type" => "textarea",  "group" => "Sizing", "class" => "", "heading" => "Minimum particle size", "param_name" => "particles_size_anim_size_min", "value" => "0.1", "description" => "Float ( > 0.1)" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_size_anim_enable", "value" => array("true")), "type" => "dropdown",  "group" => "Sizing", "class" => "", "heading" => "Sync animation", "param_name" => "particles_size_anim_sync", "value" => array("No" => "false", "Yes" => "true") ));

		vc_add_param("vc_particles_background", array( "save_always" => true, "type" => "textarea",  "group" => "Opacity", "class" => "", "heading" => "Particle opacity", "param_name" => "particles_opacity_value", "value" => "0.5", "description" => "Float (0.00 - 1.00)" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "type" => "dropdown",  "group" => "Opacity", "class" => "", "heading" => "Randomize opacity", "param_name" => "particles_opacity_random", "value" => array("Yes" => "true", "No" => "false") ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "type" => "dropdown",  "group" => "Opacity", "class" => "", "heading" => "Animate particle opacity", "param_name" => "particles_opacity_anim_enable", "value" => array("No" => "false", "Yes" => "true") ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_opacity_anim_enable", "value" => array("true")), "type" => "textarea",  "group" => "Opacity", "class" => "", "heading" => "Animation speed", "param_name" => "particles_opacity_anim_speed", "value" => "1", "description" => "Integer (0-10)" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_opacity_anim_enable", "value" => array("true")), "type" => "textarea",  "group" => "Opacity", "class" => "", "heading" => "Minimum particle opacity", "param_name" => "particles_opacity_anim_opacity_min", "value" => "0.1", "description" => "Float (0.00 - 1.00)" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_opacity_anim_enable", "value" => array("true")), "type" => "dropdown",  "group" => "Opacity", "class" => "", "heading" => "Sync animation", "param_name" => "particles_opacity_anim_sync", "value" => array("No" => "false", "Yes" => "true") ));

		vc_add_param("vc_particles_background", array( "save_always" => true, "type" => "dropdown",  "group" => "Movement", "class" => "", "heading" => "Enable movement", "param_name" => "particles_move_enabled", "value" => array("Yes" => "true", "No" => "false"), "description" => "" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_move_enabled", "value" => array("true")), "type" => "dropdown",  "group" => "Movement", "class" => "", "heading" => "Direction", "param_name" => "particles_move_direction", "value" => array("none","top","top-right","right","bottom-right","bottom","bottom-left","left","top-left"), "description" => "" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_move_enabled", "value" => array("true")), "type" => "dropdown",  "group" => "Movement", "class" => "", "heading" => "Enable random particles", "param_name" => "particles_move_random", "value" => array("No" => "false", "Yes" => "true"), "description" => "" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_move_enabled", "value" => array("true")), "type" => "dropdown",  "group" => "Movement", "class" => "", "heading" => "Enable straight particles", "param_name" => "particles_move_straight", "value" => array("No" => "false", "Yes" => "true"), "description" => "" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_move_enabled", "value" => array("true")), "type" => "textarea",  "group" => "Movement", "class" => "", "heading" => "Speed", "param_name" => "particles_move_speed", "value" => '6', "description" => "integer (0-200)" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_move_enabled", "value" => array("true")), "type" => "dropdown",  "group" => "Movement", "class" => "", "heading" => "Boundary mode", "param_name" => "particles_move_out_mode", "value" => array("bounce","out"), "description" => "" ));
		
		vc_add_param("vc_particles_background", array( "save_always" => true, "type" => "dropdown",  "group" => "Line linking", "class" => "", "heading" => "Enable line linking", "param_name" => "particles_line_linked_enable_auto", "value" => array("Yes" => "true", "No" => "false"), "description" => "" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_line_linked_enable_auto", "value" => array("true")), "type" => "textarea",  "group" => "Line linking", "class" => "", "heading" => "Maximum distance", "param_name" => "particles_line_linked_distance", "value" => "150", "description" => "Integer (0-500)" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_line_linked_enable_auto", "value" => array("true")), "type" => "colorpicker",  "group" => "Line linking", "class" => "", "heading" => "Line color", "param_name" => "particles_line_linked_color", "value" => "000000", "description" => "" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_line_linked_enable_auto", "value" => array("true")), "type" => "textarea",  "group" => "Line linking", "class" => "", "heading" => "Line opacity", "param_name" => "particles_line_linked_opacity", "value" => "0.40", "description" => "Float (0.00 - 1.00)" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_line_linked_enable_auto", "value" => array("true")), "type" => "textarea",  "group" => "Line linking", "class" => "", "heading" => "Line width", "param_name" => "particles_line_linked_width", "value" => "1", "description" => "Integer (0-20)" ));
		
		
		vc_add_param("vc_particles_background", array( "save_always" => true, "type" => "dropdown",  "group" => "Interactivity", "class" => "", "heading" => "Enable?", "param_name" => "particles_interactivity_onhover_enable", "value" => array("No" => "false", "Yes" => "true"), "description" => "This will not work in IE/EDGE and will make rows content unclickable"));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_interactivity_onhover_enable", "value" => array("true")), "type" => "dropdown",  "group" => "Interactivity", "class" => "", "heading" => "Mode", "param_name" => "particles_interactivity_onhover_mode", "value" => array("grab","repulse"), "description" => "" ));
		
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_interactivity_onhover_mode", "value" => array("grab")), "type" => "textarea",  "group" => "Interactivity", "class" => "", "heading" => "Grab distance", "param_name" => "particles_interactivity_modes_grab_distance", "value" => "312", "description" => "Integer (0-1500)" ));
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_interactivity_onhover_mode", "value" => array("grab")), "type" => "textarea",  "group" => "Interactivity", "class" => "", "heading" => "Grab opacity", "param_name" => "particles_interactivity_modes_grab_line_linked_opacity", "value" => "0.7", "description" => "Float (0.00 - 1.00)" ));
		
		vc_add_param("vc_particles_background", array( "save_always" => true, "dependency" => array("element" => "particles_interactivity_onhover_mode", "value" => array("repulse")), "type" => "textarea",  "group" => "Interactivity", "class" => "", "heading" => "Repulse distance", "param_name" => "particles_interactivity_modes_repulse_distance", "value" => "312", "description" => "Integer (0-1500)" ));

	}
	function theShortcode($Atts, $Content = null) {
		wp_enqueue_script(  array( 'jquery','vc-particles-background','particles-js' ) );
		wp_enqueue_style(  array( 'vc-particles-background' ) );
		
		if (isset($Atts['particles_shape_image_src'])) {
			$image = wp_get_attachment_image_src($Atts['particles_shape_image_src'], 'full');
		
			$Atts['particles_shape_image_src'] = $image[0];
			$Atts['particles_shape_image_width'] = $image[1];
			$Atts['particles_shape_image_height'] = $image[2];

		}
		
		$Return = '<div id="'.$Atts['theid'].'" ';
		foreach ($Atts as $AttN => $AttV) {
			$Return .= 'data-'.str_replace("_","-",$AttN).'="'.$AttV.'" ';
		}
		$Return .= 'class="vc-particles-background" style="display:none;"></div>';
		
		return $Return;
	}
	
	
	function custom_css_classes_for_vc_row_and_vc_column( $class_string, $tag ) {
	  if ( $tag == 'vc_row' || $tag == 'vc_row_inner' ) {
		if (strpos($class_string,'boomapps_vcrow') == false) { $class_string = $class_string . " boomapps_vcrow"; } 
	  }
	  if ( $tag == 'vc_column' || $tag == 'vc_column_inner' ) {
		if (strpos($class_string,'boomapps_vccolumn') == false) { $class_string = $class_string . " boomapps_vccolumn"; }
	  }
	  return $class_string; 
	}
	
	function registerStuff() {
		add_shortcode( 'vc_particles_background', array($this,'theShortcode') );	
		$this->backendElement();		
	}
	function registerScripts() {
		 wp_register_script( 'vc-particles-background', plugins_url('vcparticlesbackground.js',__FILE__ ) );
		 wp_register_style( 'vc-particles-background', plugins_url('vcparticlesbackground.css',__FILE__ ) );
		 wp_register_script( 'particles-js', plugins_url('_3rdparty/particles.js/particles.min.js',__FILE__ ) );
		
	}
	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'registerScripts') );
		add_action( 'init', array( $this, 'registerStuff') );	
		add_filter( 'vc_shortcodes_css_class', array($this,'custom_css_classes_for_vc_row_and_vc_column'), 10, 2 );
	}
}


/* Let's go! */
if (function_exists('vc_map')) {
	new VC_Particles_Background;
}

