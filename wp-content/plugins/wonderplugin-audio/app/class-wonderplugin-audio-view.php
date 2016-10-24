<?php 

require_once 'class-wonderplugin-audio-list-table.php';
require_once 'class-wonderplugin-audio-creator.php';

class WonderPlugin_Audio_View {

	private $controller;
	private $list_table;
	private $creator;
	
	function __construct($controller) {
		
		$this->controller = $controller;
	}
	
	function add_metaboxes() {
		add_meta_box('overview_features', __('WonderPlugin Audio Player Features', 'wonderplugin_audio'), array($this, 'show_features'), 'wonderplugin_audio_overview', 'features', '');
		add_meta_box('overview_upgrade', __('Upgrade to Commercial Version', 'wonderplugin_audio'), array($this, 'show_upgrade_to_commercial'), 'wonderplugin_audio_overview', 'upgrade', '');
		add_meta_box('overview_news', __('WonderPlugin News', 'wonderplugin_audio'), array($this, 'show_news'), 'wonderplugin_audio_overview', 'news', '');
		add_meta_box('overview_contact', __('Contact Us', 'wonderplugin_audio'), array($this, 'show_contact'), 'wonderplugin_audio_overview', 'contact', '');
	}
	
	function show_upgrade_to_commercial() {
		?>
		<ul class="wonderplugin-feature-list">
			<li>Use on commercial websites</li>
			<li>Remove the wonderplugin.com watermark</li>
			<li>Remove the limit of maximum three audios in one player</li>
			<li>Priority techincal support</li>
			<li><a href="http://www.wonderplugin.com/order/?product=audio" target="_blank">Upgrade to Commercial Version</a></li>
		</ul>
		<?php
	}
	
	function show_news() {
		
		include_once( ABSPATH . WPINC . '/feed.php' );
		
		$rss = fetch_feed( 'http://www.wonderplugin.com/feed/' );
		
		$maxitems = 0;
		if ( ! is_wp_error( $rss ) )
		{
			$maxitems = $rss->get_item_quantity( 5 );
			$rss_items = $rss->get_items( 0, $maxitems );
		}
		?>
		
		<ul class="wonderplugin-feature-list">
		    <?php if ( $maxitems > 0 ) {
		        foreach ( $rss_items as $item )
		        {
		        	?>
		        	<li>
		                <a href="<?php echo esc_url( $item->get_permalink() ); ?>" target="_blank" 
		                    title="<?php printf( __( 'Posted %s', 'wonderplugin_audio' ), $item->get_date('j F Y | g:i a') ); ?>">
		                    <?php echo esc_html( $item->get_title() ); ?>
		                </a>
		                <p><?php echo esc_html( $item->get_description() ); ?></p>
		            </li>
		        	<?php 
		        }
		    } ?>
		</ul>
		<?php
	}
	
	function show_features() {
		?>
		<ul class="wonderplugin-feature-list">
			<li>Works on mobile, tablets and all major web browsers, including iPhone, iPad, Android, Firefox, Safari, Chrome, Internet Explorer 7/8/9/10/11 and Opera</li>
			<li>Audio player with playlist</li>
			<li>Pre-defined professional skins</li>
			<li>Play mp3 and ogg files directly</li>
			<li>Loop playing, random playing, auto play and more</li>
			<li>Easy-to-use wizard style user interface</li>
			<li>Instantly preview</li>
			<li>Provide shortcode and PHP code to insert the audio to pages, posts or templates</li>
		</ul>
		<?php
	}
	
	function show_contact() {
		?>
		<p>Technical support is available for Commercial Version users at support@wonderplugin.com. Please include your license information, WordPress version, link to your audio, all related error messages in your email.</p> 
		<?php
	}
	
	function print_overview() {
		
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-audio" class="icon32"><br /></div>
			
		<h2><?php echo __( 'WonderPlugin Audio Player', 'wonderplugin_audio' ) . ( (WONDERPLUGIN_AUDIO_VERSION_TYPE == "C") ? " Commercial Version" : " Free Version") . " " . WONDERPLUGIN_AUDIO_VERSION; ?> </h2>
		 
		<div id="welcome-panel" class="welcome-panel">
			<div class="welcome-panel-content">
				<h3>WordPress Audio Player Plugin</h3>
				<div class="welcome-panel-column-container">
					<div class="welcome-panel-column">
						<h4>Get Started</h4>
						<a class="button button-primary button-hero" href="<?php echo admin_url('admin.php?page=wonderplugin_audio_add_new'); ?>">Create A New Audio Player</a>
					</div>
					<div class="welcome-panel-column welcome-panel-last">
						<h4>More Actions</h4>
						<ul>
							<li><a href="<?php echo admin_url('admin.php?page=wonderplugin_audio_show_items'); ?>" class="welcome-icon welcome-widgets-menus">Manage Existing Audio Players</a></li>
							<li><a href="http://www.wonderplugin.com/wordpress-audio-player/help/" target="_blank" class="welcome-icon welcome-learn-more">Help Document</a></li>
							<?php  if (WONDERPLUGIN_AUDIO_VERSION_TYPE !== "C") { ?>
							<li><a href="http://www.wonderplugin.com/order/?product=audio" target="_blank" class="welcome-icon welcome-view-site">Upgrade to Commercial Version</a></li>
							<?php } ?>
						</ul>
					</div>
				</div>
			</div>
		</div>
		
		<div id="dashboard-widgets-wrap">
			<div id="dashboard-widgets" class="metabox-holder columns-2">
	 
	                 <div class="postbox-container">
	                    <?php 
	                    do_meta_boxes( 'wonderplugin_audio_overview', 'features', '' ); 
	                    do_meta_boxes( 'wonderplugin_audio_overview', 'contact', '' ); 
	                    ?>
	                </div>
	 
	                <div class="postbox-container">
	                    <?php 
	                    if (WONDERPLUGIN_AUDIO_VERSION_TYPE != "C")
	                    	do_meta_boxes( 'wonderplugin_audio_overview', 'upgrade', ''); 
	                    do_meta_boxes( 'wonderplugin_audio_overview', 'news', ''); 
	                    ?>
	                </div>
	 
	        </div>
        </div>
            
		<?php
	}
	
	function print_edit_settings() {
	?>
		<div class="wrap">
		<div id="icon-wonderplugin-audio" class="icon32"><br /></div>
			
		<h2><?php _e( 'Settings', 'wonderplugin_audio' ); ?> </h2>
		<?php

		if ( isset($_POST['save-audio-options']) && check_admin_referer('wonderplugin-audio', 'wonderplugin-audio-settings') )
		{		
			unset($_POST['save-audio-options']);
			
			$this->controller->save_settings($_POST);
			
			echo '<div class="updated"><p>Settings saved.</p></div>';
		}
								
		$settings = $this->controller->get_settings();
		$userrole = $settings['userrole'];
		$keepdata = $settings['keepdata'];
		$disableupdate = $settings['disableupdate'];
		$supportwidget = $settings['supportwidget'];
		$addjstofooter = $settings['addjstofooter'];
		$jsonstripcslash = $settings['jsonstripcslash'];
		
		?>
		
		<h3>This page is only available for users of Administrator role.</h3>
		
        <form method="post">
        
        <?php wp_nonce_field('wonderplugin-audio', 'wonderplugin-audio-settings'); ?>
        
        <table class="form-table">
        
        <tr valign="top">
			<th scope="row">Set minimum user role</th>
			<td>
				<select name="userrole">
				  <option value="Administrator" <?php echo ($userrole == 'manage_options') ? 'selected="selected"' : ''; ?>>Administrator</option>
				  <option value="Editor" <?php echo ($userrole == 'moderate_comments') ? 'selected="selected"' : ''; ?>>Editor</option>
				  <option value="Author" <?php echo ($userrole == 'upload_files') ? 'selected="selected"' : ''; ?>>Author</option>
				</select>
			</td>
		</tr>
			
		<tr>
			<th>Data option</th>
			<td><label><input name='keepdata' type='checkbox' id='keepdata' <?php echo ($keepdata == 1) ? 'checked' : ''; ?> /> Keep data when deleting the plugin</label>
			</td>
		</tr>
		
		<tr>
			<th>Update option</th>
			<td><label><input name='disableupdate' type='checkbox' id='disableupdate' <?php echo ($disableupdate == 1) ? 'checked' : ''; ?> /> Disable plugin version check and update</label>
			</td>
		</tr>
		
		<tr>
			<th>Display audio player in widget</th>
			<td><label><input name='supportwidget' type='checkbox' id='supportwidget' <?php echo ($supportwidget == 1) ? 'checked' : ''; ?> /> Support shortcode in text widget</label>
			</td>
		</tr>
		
		<tr>
			<th>Scripts position</th>
			<td><label><input name='addjstofooter' type='checkbox' id='addjstofooter' <?php echo ($addjstofooter == 1) ? 'checked' : ''; ?> /> Add plugin js scripts to the footer (wp_footer hook must be implemented by the WordPress theme)</label>
			</td>
		</tr>
		
		<tr>
			<th>JSON options</th>
			<td><label><input name='jsonstripcslash' type='checkbox' id='jsonstripcslash' <?php echo ($jsonstripcslash == 1) ? 'checked' : ''; ?> /> Remove backslashes in JSON string</label>
			</td>
		</tr>
		
        </table>
        
        <p class="submit"><input type="submit" name="save-audio-options" id="save-audio-options" class="button button-primary" value="Save Changes"  /></p>
        
        </form>
        
		</div>
		<?php
	}
	
	function print_register() {
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-audio" class="icon32"><br /></div>
			
		<h2><?php _e( 'Register', 'wonderplugin_audio' ); ?></h2>
		<?php
				
		if (isset($_POST['save-audio-license']) && check_admin_referer('wonderplugin-audio', 'wonderplugin-audio-register') )
		{		
			unset($_POST['save-audio-license']);

			$ret = $this->controller->check_license($_POST);
			
			if ($ret['status'] == 'valid')
				echo '<div class="updated"><p>The key has been saved.</p></div>';
			else if ($ret['status'] == 'expired')
				echo '<div class="error"><p>Your free upgrade period has expired, please renew your license.</p></div>';
			else if ($ret['status'] == 'invalid')
				echo '<div class="error"><p>The key is invalid.</p></div>';
			else if ($ret['status'] == 'abnormal')
				echo '<div class="error"><p>You have reached the maximum website limit of your license key. Please log into the membership area and upgrade to a higher license.</p></div>';
			else if ($ret['status'] == 'misuse')
				echo '<div class="error"><p>There is a possible misuse of your license key, please contact support@wonderplugin.com for more information.</p></div>';
			else if ($ret['status'] == 'timeout')
				echo '<div class="error"><p>The license server can not be reached, please try again later.</p></div>';
			else if ($ret['status'] == 'empty')
				echo '<div class="error"><p>Please enter your license key.</p></div>';
			else if (isset($ret['message']))
				echo '<div class="error"><p>' . $ret['message'] . '</p></div>';
		}
		else if (isset($_POST['deregister-audio-license']) && check_admin_referer('wonderplugin-audio', 'wonderplugin-audio-register') )
		{	
			$ret = $this->controller->deregister_license($_POST);
			
			if ($ret['status'] == 'success')
				echo '<div class="updated"><p>The key has been deregistered.</p></div>';
			else if ($ret['status'] == 'timeout')
				echo '<div class="error"><p>The license server can not be reached, please try again later.</p></div>';
			else if ($ret['status'] == 'empty')
				echo '<div class="error"><p>The license key is empty.</p></div>';
		}
		
		$settings = $this->controller->get_settings();
		$disableupdate = $settings['disableupdate'];
		
		$key = '';
		$info = $this->controller->get_plugin_info();
		if (!empty($info->key) && ($info->key_status == 'valid' || $info->key_status == 'expired'))
			$key = $info->key;
		
		?>
		
		<?php 
		if ($disableupdate == 1)
		{
			echo "<h3 style='padding-left:10px;'>The plugin version check and update is currently disabled. You can enable it in the Settings menu.</h3>";
		}
		else
		{
		?> <div style="padding-left:10px;padding-top:12px;"> <?php
			if (empty($key)) { ?>
				<form method="post">
				<?php wp_nonce_field('wonderplugin-audio', 'wonderplugin-audio-register'); ?>
				<table class="form-table">
				<tr>
					<th>Enter Your License Key:</th>
					<td><input name="wonderplugin-audio-key" type="text" id="wonderplugin-audio-key" value="" class="regular-text" /> <input type="submit" name="save-audio-license" id="save-audio-license" class="button button-primary" value="Register License Key"  />
					</td>
				</tr>
				</table>
				</form>
			<?php } else { ?>
				<form method="post">
				<?php wp_nonce_field('wonderplugin-audio', 'wonderplugin-audio-register'); ?>
				<p>You have entered your license key and this domain has been successfully registered. &nbsp;&nbsp;<input name="wonderplugin-audio-key" type="hidden" id="wonderplugin-audio-key" value="<?php echo esc_html($key); ?>" class="regular-text" /><input type="submit" name="deregister-audio-license" id="deregister-audio-license" class="button button-primary" value="Deregister Your License Key"  /></p>
				</form>
				<?php if ($info->key_status == 'expired') { ?>
				<p><strong>Your free upgrade period has expired.</strong> To get upgrades, please <a href="https://www.wonderplugin.com/renew/" target="_blank">renew your license</a>.</p>
				<?php } ?>
			<?php } ?>
			</div>
		<?php } ?>
		
		<div style="padding-left:10px;padding-top:30px;">
		<a href="<?php echo admin_url('update-core.php?force-check=1'); ?>"><button class="button-primary">Force WordPress To Check For Plugin Updates</button></a>
		</div>
					
		<div style="padding-left:10px;padding-top:20px;">
        <ul style="list-style-type:square;font-size:16px;line-height:28px;margin-left:24px;">
		<li><a href="https://www.wonderplugin.com/how-to-upgrade-a-commercial-version-plugin-to-the-latest-version/" target="_blank">How to upgrade to the latest version</a></li>
	    <li><a href="https://www.wonderplugin.com/register-faq/" target="_blank">Where can I find my license key and other frequently asked questions</a></li>
	    </ul>
        </div>
        
		</div>
		
		<?php
	}
		
	function print_items() {
		
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-audio" class="icon32"><br /></div>
			
		<h2><?php _e( 'Manage Audio Players', 'wonderplugin_audio' ); ?> <a href="<?php echo admin_url('admin.php?page=wonderplugin_audio_add_new'); ?>" class="add-new-h2"> <?php _e( 'New Audio Player', 'wonderplugin_audio' ); ?></a> </h2>
		
		
		<form id="audio-list-table" method="post">
		<input type="hidden" name="page" value="<?php echo esc_html($_REQUEST['page']); ?>" />
		<?php 
		
		if ( !is_object($this->list_table) )
			$this->list_table = new WonderPlugin_Audio_List_Table($this);
		
		$this->process_actions();
		
		$this->list_table->list_data = $this->controller->get_list_data();
		$this->list_table->prepare_items();
		$this->list_table->views();
		$this->list_table->display();		
		?>								
        </form>
        
		</div>
		<?php
	}
	
	function print_item()
	{
		if ( !isset( $_REQUEST['itemid'] ) || !is_numeric( $_REQUEST['itemid'] ) )
			return;
		
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-audio" class="icon32"><br /></div>
					
		<h2><?php _e( 'View Audio Player', 'wonderplugin_audio' ); ?> <a href="<?php echo admin_url('admin.php?page=wonderplugin_audio_edit_item') . '&itemid=' . $_REQUEST['itemid']; ?>" class="add-new-h2"> <?php _e( 'Edit Audio Player', 'wonderplugin_audio' ); ?>  </a> </h2>
		
		<div class="updated"><p style="text-align:center;">  <?php _e( 'To embed the audio player into your page, use shortcode: ', 'wonderplugin_audio' ); ?> <?php echo esc_attr('[wonderplugin_audio id="' . $_REQUEST['itemid'] . '"]'); ?></p></div>

		<div class="updated"><p style="text-align:center;">  <?php _e( 'To embed the audio player into your template, use php code: ', 'wonderplugin_audio' ); ?> <?php echo esc_attr('<?php echo do_shortcode(\'[wonderplugin_audio id="' . $_REQUEST['itemid'] . '"]\'); ?>'); ?></p></div>
		
		<?php 
		if (WONDERPLUGIN_AUDIO_VERSION_TYPE !== "C")
			echo '<div class="updated"><p style="text-align:center;">To remove the Free Version watermark, please <a href="https://www.wonderplugin.com/order/?product=audio" target="_blank">Upgrade to Commercial Version</a>.</p></div>';

		echo $this->controller->generate_body_code( $_REQUEST['itemid'], null, true ); 
		?>	 
		
		</div>
		<?php
	}
	
	function process_actions()
	{
		if (!isset($_REQUEST['_wpnonce']) || (!wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-' . $this->list_table->_args['plural']) && !wp_verify_nonce($_REQUEST['_wpnonce'], 'wonderplugin-list-table-nonce')))
			return;
			
		if ( ((isset($_REQUEST['action']) && ($_REQUEST['action'] == 'trash')) || (isset($_REQUEST['action2']) && ($_REQUEST['action2'] == 'trash'))) && isset( $_REQUEST['itemid'] ) )
		{
			$trashed = 0;
				
			if ( is_array( $_REQUEST['itemid'] ) )
			{
				foreach( $_REQUEST['itemid'] as $id)
				{
					if ( is_numeric($id) )
					{
						$ret = $this->controller->trash_item($id);
						if ($ret > 0)
							$trashed += $ret;
					}
				}
			}
			else if ( is_numeric($_REQUEST['itemid']) )
			{
				$trashed = $this->controller->trash_item( $_REQUEST['itemid'] );
			}
				
			if ($trashed > 0)
			{
				echo '<div class="updated"><p>';
				printf( _n('%d audio player moved to the trash.', '%d audio players moved to the trash.', $trashed), $trashed );
				echo '</p></div>';
			}
		}
		
		if ( ((isset($_REQUEST['action']) && ($_REQUEST['action'] == 'restore')) || (isset($_REQUEST['action2']) && ($_REQUEST['action2'] == 'restore'))) && isset( $_REQUEST['itemid'] ) )
		{
			$restored = 0;
		
			if ( is_array( $_REQUEST['itemid'] ) )
			{
				foreach( $_REQUEST['itemid'] as $id)
				{
					if ( is_numeric($id) )
					{
						$ret = $this->controller->restore_item($id);
						if ($ret > 0)
							$restored += $ret;
					}
				}
			}
			else if ( is_numeric($_REQUEST['itemid']) )
			{
				$restored = $this->controller->restore_item( $_REQUEST['itemid'] );
			}
		
			if ($restored > 0)
			{
				echo '<div class="updated"><p>';
				printf( _n('%d audio player restored.', '%d audio players restored.', $restored), $restored );
				echo '</p></div>';
			}
		}
		
		if ( ((isset($_REQUEST['action']) && ($_REQUEST['action'] == 'delete')) || (isset($_REQUEST['action2']) && ($_REQUEST['action2'] == 'delete'))) && isset( $_REQUEST['itemid'] ) )
		{
			$deleted = 0;
			
			if ( is_array( $_REQUEST['itemid'] ) )
			{
				foreach( $_REQUEST['itemid'] as $id)
				{
					if ( is_numeric($id) )
					{
						$ret = $this->controller->delete_item($id);
						if ($ret > 0)
							$deleted += $ret;
					}
				}
			}
			else if ( is_numeric($_REQUEST['itemid']) )
			{
				$deleted = $this->controller->delete_item( $_REQUEST['itemid'] );
			}
			
			if ($deleted > 0)
			{
				echo '<div class="updated"><p>';
				printf( _n('%d audio player deleted.', '%d audio players deleted.', $deleted), $deleted );
				echo '</p></div>';
			}
		}
		
		if ( ((isset($_REQUEST['action']) && ($_REQUEST['action'] == 'clone')) || (isset($_REQUEST['action2']) && ($_REQUEST['action2'] == 'clone'))) && isset( $_REQUEST['itemid'] ) && is_numeric( $_REQUEST['itemid'] ))
		{
			$cloned_id = $this->controller->clone_item( $_REQUEST['itemid'] );
			if ($cloned_id > 0)
			{
				echo '<div class="updated"><p>';
				printf( 'New audio player created with ID: %d', $cloned_id );
				echo '</p></div>';
			}
			else
			{
				echo '<div class="error"><p>';
				printf( 'The audio player cannot be cloned.' );
				echo '</p></div>';
			}
		}
	}

	function print_add_new() {
		
		if ( !empty($_POST['wonderplugin-audio-save-item-post-value']) && !empty($_POST['wonderplugin-audio-save-item-post']) && check_admin_referer('wonderplugin-audio', 'wonderplugin-audio-saveform') )
		{
			$this->save_item_post($_POST['wonderplugin-audio-save-item-post-value']);
			return;
		}
		
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-audio" class="icon32"><br /></div>
		
		<h2><?php _e( 'New Audio Player', 'wonderplugin_audio' ); ?> <a href="<?php echo admin_url('admin.php?page=wonderplugin_audio_show_items'); ?>" class="add-new-h2"> <?php _e( 'Manage Audio Players', 'wonderplugin_audio' ); ?>  </a> </h2>
		
		<?php 
		$this->creator = new WonderPlugin_Audio_Creator($this);		
		echo $this->creator->render( -1, null);
	}
	
	function print_edit_item()
	{
		
		if ( !empty($_POST['wonderplugin-audio-save-item-post-value']) && !empty($_POST['wonderplugin-audio-save-item-post']) && check_admin_referer('wonderplugin-audio', 'wonderplugin-audio-saveform') )
		{
			$this->save_item_post($_POST['wonderplugin-audio-save-item-post-value']);
			return;
		}
		
		if ( !isset( $_REQUEST['itemid'] ) || !is_numeric( $_REQUEST['itemid'] ) )
			return;
	
		?>
		<div class="wrap">
		<div id="icon-wonderplugin-audio" class="icon32"><br /></div>
			
		<h2><?php _e( 'Edit Audio Player', 'wonderplugin_audio' ); ?> <a href="<?php echo admin_url('admin.php?page=wonderplugin_audio_show_item') . '&itemid=' . $_REQUEST['itemid']; ?>" class="add-new-h2"> <?php _e( 'View Audio', 'wonderplugin_audio' ); ?>  </a> </h2>
		
		<?php 
		$this->creator = new WonderPlugin_Audio_Creator($this);
		echo $this->creator->render( $_REQUEST['itemid'], $this->controller->get_item_data( $_REQUEST['itemid'] ) );
	}
	
	function save_item_post($item_post) {
			
		$jsonstripcslash = get_option( 'wonderplugin_audio_jsonstripcslash', 1 );
		if ($jsonstripcslash == 1)
			$json_post = trim(stripcslashes($item_post));
		else
			$json_post = trim($item_post);
		
		$items = json_decode($json_post, true);
		
		if ( empty($items) )
		{
			$json_error = "json_decode error";
			if ( function_exists('json_last_error_msg') )
				$json_error .= ' - ' . json_last_error_msg();
			else if ( function_exists('json_last_error') )
				$json_error .= 'code - ' . json_last_error();
		
			$ret = array(
					"success" => false,
					"id" => -1,
					"message" => $json_error . ". <b>To fix the problem, in the Plugin Settings menu, uncheck the option Remove backslashes in JSON string</b>",
					"errorcontent"	=> $json_post
			);
		}
		else
		{
			add_filter('safe_style_css', 'wonderplugin_audio_css_allow');
			
			foreach ($items as $key => &$value)
			{
				if ($value === true)
					$value = "true";
				else if ($value === false)
					$value = "false";
				else if ( is_string($value) )
					$value = wp_kses_post($value);
			}
			
			remove_filter('safe_style_css', 'wonderplugin_audio_css_allow');
			
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
		
			$ret = $this->controller->save_item($items);
		}
		?>
					
		<div class="wrap">
		<div id="icon-wonderplugin-audio" class="icon32"><br /></div>
		
		<?php 
		if (isset($ret['success']) && $ret['success'] && isset($ret['id']) && $ret['id'] >= 0) 
		{
			echo "<h2>Player Saved.";
			echo "<a href='" . admin_url('admin.php?page=wonderplugin_audio_edit_item') . '&itemid=' . $ret['id'] . "' class='add-new-h2'>Edit Player</a>";
			echo "<a href='" . admin_url('admin.php?page=wonderplugin_audio_show_item') . '&itemid=' . $ret['id'] . "' class='add-new-h2'>View Player</a>";
			echo "</h2>";
					
			echo "<div class='updated'><p>The player has been saved and published.</p></div>";
			echo "<div class='updated'><p>To embed the player into your page or post, use shortcode:  [wonderplugin_audio id=\"" . $ret['id'] . "\"]</p></div>";
			echo "<div class='updated'><p>To embed the player into your template, use php code:  &lt;?php echo do_shortcode('[wonderplugin_audio id=\"" . $ret['id'] . "\"]'); ?&gt;</p></div>"; 
		}
		else
		{
			echo "<h2>WonderPlugin Audio Player</h2>";
			echo "<div class='error'><p>The player can not be saved.</p></div>";
			echo "<div class='error'><p>Error Message: " . ((isset($ret['message'])) ? $ret['message'] : "") . "</p></div>";
			echo "<div class='error'><p>Error Content: " . ((isset($ret['errorcontent'])) ? $ret['errorcontent'] : "") . "</p></div>";
		}	
	}
}