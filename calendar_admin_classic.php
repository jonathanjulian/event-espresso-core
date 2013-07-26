<?php
//This file is used for Event Espresso 3.1.*



/**
 * 	load_pue_update - Update notifications
 *
 *  @return 	void
 */
function espresso_calendar_load_pue_update() {
	global $org_options, $espresso_check_for_updates;
	if ($espresso_check_for_updates == false)
		return;

	if (file_exists(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php')) { //include the file 
		require(EVENT_ESPRESSO_PLUGINFULLPATH . 'class/pue/pue-client.php' );
		$api_key = $org_options['site_license_key'];
		$host_server_url = 'http://eventespresso.com';
		$plugin_slug = 'espresso-calendar';
		$options = array(
				'apikey' => $api_key,
				'lang_domain' => 'event_espresso',
				'checkPeriod' => '24',
				'option_key' => 'site_license_key'
		);
		$check_for_updates = new PluginUpdateEngineChecker($host_server_url, $plugin_slug, $options); //initiate the class and start the plugin update engine!
	}
}




//Install the plugin
function espresso_calendar_install() {
	
	$calendar_default_settings = array(
	
			'espresso_page_post' => "R",
			
			'header_left' => 'prev , today',
			'header_center' => 'title',
			'header_right' =>'month, agendaWeek, agendaDay, next',
			
			'buttonText_prev' => '&lsaquo;',
			'buttonText_next' => '&rsaquo;',
			'buttonText_prevYear' => '&laquo;',
			'buttonText_nextYear' => '&raquo;',
			'buttonText_today' => 'today',
			'buttonText_month' => 'month',
			'buttonText_week' => 'week',
			'buttonText_day' => 'day',

			'firstDay' => '0',
			'weekends' => true,
			'espresso_calendar_height' => '650',
			'espresso_calendar_width' => '2',
			'enable_calendar_thumbs' => false,
			'show_tooltips' => true,
			'tooltips_pos' => array('my_1' => "bottom", 'my_2' => "center", 'at_1' => "center", 'at_2' => "center"),
			'tooltip_style' => 'qtip-light',
			'espresso_use_pickers' => false,
			'ee_event_background' => '007BAE',
			'ee_event_text_color' => 'FFFFFF',
			'enable_cat_classes' => false,
			'time_format' => get_option('time_format'),
			'show_time' => true,

			'disable_categories' => false,
			'show_attendee_limit' => false,
			
			'titleFormat_month' => 'MMMM yyyy', 
			'titleFormat_week' => 'MMM dS[ yyyy] - {[ MMM] dS yyyy}', 
			'titleFormat_day' => 'dddd, MMM dS, yyyy',
			
			'columnFormat_month' => 'ddd',
			'columnFormat_week' => 'ddd M/d',
			'columnFormat_day' => 'dddd M/d',
			
	);
	// get saved settings
	$calendar_settings = get_option( 'espresso_calendar_settings', array() );
	// override defaults
	$calendar_settings = array_merge( $calendar_default_settings, $calendar_settings );
	// resave
	update_option('espresso_calendar_settings', $calendar_settings);
}




/**
 * Add a settings link to the Plugins page, so people can go straight from the plugin page to the
 * settings page.
 */
function espresso_calendar_plugin_actions($links, $file) {
	// Static so we don't call plugin_basename on every plugin row.
	static $this_plugin;
	if (!$this_plugin)
		$this_plugin = plugin_basename(__FILE__);

	if ($file == $this_plugin) {
		$org_settings_link = '<a href="admin.php?page=espresso_calendar">' . __('Settings') . '</a>';
		array_unshift($links, $org_settings_link); // before other links
	}
	return $links;
}



function espresso_calendar_current_screen() {
	if (!is_admin())
		return;

	global $current_screen;
	if ($current_screen->base == 'widgets') {
		include_once( ESPRESSO_CALENDAR_PLUGINFULLPATH . 'calendar_help.php'); // include the calendar help file, since that's what we're freaking trying to load
		wp_enqueue_style('thickbox'); //load the freaking thickbox style
		wp_enqueue_script('thickbox'); // load the freaking thickbox script
	}
}



function espresso_calendar_add_to_featured_image_meta_box($event_meta) {
	$values = array(
			array('id' => true, 'text' => __('Yes', 'event_espresso')),
			array('id' => false, 'text' => __('No', 'event_espresso')));
	?>
	<p>
		<label>
			<?php _e('Add image to event calendar', 'event_espresso'); ?>
		</label>
		<?php echo select_input('show_on_calendar', $values, isset($event_meta['display_thumb_in_calendar']) ? $event_meta['display_thumb_in_calendar'] : '', 'id="show_on_calendar"'); ?>
	</p>
	<?php
}




function espresso_add_calendar_to_admin_menu($espresso_manager) {
	add_submenu_page('events', __('Event Espresso - Calendar Settings', 'event_espresso'), __('Calendar', 'event_espresso'), apply_filters('filter_hook_espresso_management_capability', 'administrator', $espresso_manager['espresso_manager_calendar']), 'espresso_calendar', 'espresso_calendar_config_mnu');
}



function espresso_calendar_config_mnu() {
	
	global $espresso_calendar, $notices;
	/* Calendar */
	function espresso_calendar_updated() {
		
	}

	if (isset($_POST['update_calendar']) && check_admin_referer('espresso_form_check', 'update_calendar')) {
		
		$espresso_calendar['espresso_page_post'] = $_POST['espresso_page_post'];
		
		$espresso_calendar['header_left'] = $_POST['espresso_calendar_header_left'];
		$espresso_calendar['header_center'] = $_POST['espresso_calendar_header_center'];
		$espresso_calendar['header_right'] = $_POST['espresso_calendar_header_right'];
		
		$espresso_calendar['buttonText_prev'] = $_POST['buttonText_prev'];
		$espresso_calendar['buttonText_next'] = $_POST['buttonText_next'];
		$espresso_calendar['buttonText_prevYear'] = $_POST['buttonText_prevYear'];
		$espresso_calendar['buttonText_nextYear'] = $_POST['buttonText_nextYear'];
		$espresso_calendar['buttonText_today'] = $_POST['buttonText_today'];
		$espresso_calendar['buttonText_month'] = $_POST['buttonText_month'];
		$espresso_calendar['buttonText_week'] = $_POST['buttonText_week'];
		$espresso_calendar['buttonText_day'] = $_POST['buttonText_day'];
		
		$espresso_calendar['firstDay'] = absint( $_POST['firstDay'] );
		$espresso_calendar['weekends'] = absint( $_POST['weekends'] );
		$espresso_calendar['espresso_calendar_height'] = $_POST['espresso_calendar_height'];
		$espresso_calendar['enable_calendar_thumbs'] = $_POST['enable_calendar_thumbs'];
		$espresso_calendar['show_tooltips'] = $_POST['show_tooltips'];
		$espresso_calendar['tooltips_pos']['my_1'] = $_POST['tooltips_pos_my_1'];
		$espresso_calendar['tooltips_pos']['my_2'] = $_POST['tooltips_pos_my_2'];
		$espresso_calendar['tooltips_pos']['at_1'] = $_POST['tooltips_pos_at_1'];
		$espresso_calendar['tooltips_pos']['at_2'] = $_POST['tooltips_pos_at_2'];
		$espresso_calendar['tooltip_style'] = $_POST['tooltip_style'];
		
		$espresso_calendar['show_time'] = $_POST['show_time'];

		$espresso_calendar['disable_categories'] = $_POST['disable_categories'];
		$espresso_calendar['show_attendee_limit'] = $_POST['show_attendee_limit'];
		$espresso_calendar['time_format'] = $_POST['time_format_custom'];
		$espresso_calendar['espresso_use_pickers'] = $_POST['espresso_use_pickers'];
		$espresso_calendar['ee_event_background'] = (!empty($_POST['ee_event_background']) ) ? $_POST['ee_event_background'] : '007BAE';
		$espresso_calendar['ee_event_text_color'] = (!empty($_POST['ee_event_text_color']) ) ? $_POST['ee_event_text_color'] : 'FFFFFF';
		$espresso_calendar['enable_cat_classes'] = $_POST['enable_cat_classes'];

		$espresso_calendar['titleFormat_month'] = $_POST['titleFormat_month'];
		$espresso_calendar['titleFormat_week'] = $_POST['titleFormat_week'];
		$espresso_calendar['titleFormat_day'] = $_POST['titleFormat_day'];
		
		$espresso_calendar['columnFormat_month'] = $_POST['columnFormat_month'];
		$espresso_calendar['columnFormat_week'] = $_POST['columnFormat_week'];
		$espresso_calendar['columnFormat_day'] = $_POST['columnFormat_day'];

		update_option('espresso_calendar_settings', $espresso_calendar);
		add_action('admin_notices', 'espresso_calendar_updated');
		$notices['updates'][] = __('The calendar settings were saved ', 'event_espresso');
	}
	if (!empty($_REQUEST['reset_calendar']) && check_admin_referer('espresso_form_check', 'reset_calendar_nonce')) {
		delete_option("espresso_calendar_settings");
		espresso_calendar_install();
		$notices['updates'][] = __('The calendar settings were reset ', 'event_espresso');
	}
	$espresso_calendar = get_option('espresso_calendar_settings');

	$values = array(
			array('id' => false, 'text' => __('No', 'event_espresso')),
			array('id' => true, 'text' => __('Yes', 'event_espresso'))
	);
################## Begin admin settings screen ###########################
	?>
<div id="ee-calendar-settings" class="wrap meta-box-sortables ui-sortable">
	<div id="icon-options-event" class="icon32"> </div>
	<h2>
	<?php _e('Event Espresso - Calendar Settings', 'event_espresso'); ?>
	</h2>
<?php ob_start(); ?>
	<form class="espresso_form" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
		<div class="metabox-holder">
			<div class="postbox">
				<div title="Click to toggle" class="handlediv"><br />
				</div>
				<h3 class="hndle">
<?php _e('Calendar Usage', 'event_espresso'); ?>
				</h3>
				<div class="inside">
					<div class="padding">
						<ul>
							<li> <strong>
								<?php _e('Directions:', 'event_espresso'); ?>
								</strong><br />
<?php _e(' Add [ESPRESSO_CALENDAR] to any page or post to display a calendar of Event Espresso events. Use [ESPRESSO_CALENDAR event_category_id="your_category_identifier"] to show events of a certain category (also creates a CSS using the category_identifier as the class name.) Use [ESPRESSO_CALENDAR show_expired="true"] to show expired events, can also be used inconjunction with the category ID.', 'event_espresso'); ?>
							</li>
							<li><strong>
<?php _e('Examples Shortcodes:', 'event_espresso'); ?>
								</strong><br />
								[ESPRESSO_CALENDAR]<br />
								[ESPRESSO_CALENDAR show_expired="true"]<br />
								[ESPRESSO_CALENDAR event_category_id="your_category_identifier"]<br />
								[ESPRESSO_CALENDAR event_category_id="your_category_identifier" show_expired="true"]<br />
								[ESPRESSO_CALENDAR cal_view="month"] (Available parameters: month, basicWeek, basicDay, agendaWeek, agendaDay) </li>
							<li><strong>
								<?php _e('Styles/Colors:', 'event_espresso'); ?>
								</strong><br />
<?php _e('To edit the calendar styles, copy the CSS file located in the plugin folder to your "wp-content/uploads/espresso/" directory. Then edit as needed. Refer to <a href="http://arshaw.com/fullcalendar/docs/event_rendering/Colors/" target="_blank">this page</a> for an example of styling the calendar and colors.', 'event_espresso'); ?>
							</li>
							<li><strong>
								<?php _e('Category Colors:', 'event_espresso'); ?>
								</strong><br />
<?php _e('Event Categories can have their own colors on the calendar. To use this feature, simply create a class in theme CSS file with the names of your event categories. For more inforamtion <a href="http://eventespresso.com/forums/?p=650" target="_blank">please visit the tutorial</a> for this topic.', 'event_espresso'); ?>
							</li>
						</ul>
					</div>
					<!-- / .padding -->
				</div>
				<!-- / .inside -->
			</div>
			<!-- / .postbox -->
		</div>
		<!-- / .metabox-holder -->

		<!-- Calendar basic settings metabox -->
		<div class="metabox-holder">
			<div class="postbox">
				<div title="Click to toggle" class="handlediv"><br />
				</div>
				<h3 class="hndle"><?php _e('Basic Settings', 'event_espresso'); ?></h3>
				<div class="inside">
					<div class="padding">
						<h4><?php _e('Time/Date Settings', 'event_espresso'); ?></h4>
						<table class="form-table">
							<tbody>
								<tr>
									<th>
										<label for="show_time"><?php _e('Show Event Time in Calendar', 'event_espresso'); ?></label>
									</th>
									<td>
										<?php echo select_input('show_time', $values, $espresso_calendar['show_time'], 'id="show_time"');?>											
									</td>
								</tr>
								<tr>
									<th><label for="time_format"><?php _e('Time Format') ?></label></th>
									<td>
<?php
$espresso_calendar['time_format'] = empty($espresso_calendar['time_format']) ? get_option('time_format') : $espresso_calendar['time_format'];
$time_formats = apply_filters('time_formats', array(
		__('g:i a'),
		'ga',
		'g:i A',
		'gA',
		'H:i',
				));

$custom = true;

foreach ($time_formats as $format) {
	echo "\t<label title='" . esc_attr($format) . "' style=\"display:inline-block; width:150px; margin-bottom:1em;\" >&nbsp;<input type='radio' name='time_format' value='" . esc_attr($format) . "'";
	if ($espresso_calendar['time_format'] === $format) { // checked() uses "==" rather than "==="
		echo " checked='checked'";
		$custom = false;
	}
	echo ' />&nbsp;<span>' . date_i18n($format) . "</span></label> \n";
}

echo '<div style="display:inline-block; width:230px;"><label style="display:inline-block;">&nbsp;<input type="radio" name="time_format" id="time_format_custom_radio" value="\c\u\s\t\o\m"';
checked($custom);
echo '/>&nbsp;' . __('Custom:') . '</label>&nbsp;<input type="text" name="time_format_custom" value="' . esc_attr($espresso_calendar['time_format']) . '" class="small-text" /> ';
echo '<span class="example"> ' . date_i18n($espresso_calendar['time_format']) . "</span></div>";
?><span class="description"><a href="http://codex.wordpress.org/Formatting_Date_and_Time"><?php _e('Documentation on date and time formatting', 'event_espresso'); ?></a></span>
<img class="ajax-loading" src="<?php echo esc_url(admin_url('images/wpspin_light.gif'));?>" alt="" />
									</td>
								</tr>

<?php
							$days_of_the_week = array(
									array('id' => 0, 'text' => __('Sunday', 'event_espresso')),
									array('id' => 1, 'text' => __('Monday', 'event_espresso')),
									array('id' => 2, 'text' => __('Tuesday', 'event_espresso')),
									array('id' => 3, 'text' => __('Wednesday', 'event_espresso')),
									array('id' => 4, 'text' => __('Thursday', 'event_espresso')),
									array('id' => 5, 'text' => __('Friday', 'event_espresso')),
									array('id' => 6, 'text' => __('Saturday', 'event_espresso'))
							);
?>								
								<tr>
									<th> <label for="firstDay">
<?php _e('First Day of the Week', 'event_espresso'); ?>
										</label>
									</th>
									<td>
										<?php echo select_input('firstDay', $days_of_the_week, $espresso_calendar['firstDay'], 'id="firstDay"'); ?><br />
										<span class="description">
<?php _e('Determines which day will be in the first column of the calendar', 'event_espresso'); ?>
										</span></td>
								</tr>
								<tr>
									<th> <label for="weekends">
<?php _e('Show Weekends', 'event_espresso'); ?>
										</label>
									</th>
									<td><?php echo select_input('weekends', $values, $espresso_calendar['weekends'], 'id="weekends"'); ?><br />
										<span class="description">
<?php _e('This setting allows you to remove the weekends from your calendar views. This may be useful if you don\'t have events on weekends.', 'event_espresso'); ?>
										</span></td>
								</tr>
							</tbody>
						</table>
						<h4>
<?php _e('Page Settings', 'event_espresso'); ?>
						</h4>
						<table class="form-table">
							<tbody>
								<tr>
									<th> <label for="espresso_calendar_height">
<?php _e('Height', 'event_espresso'); ?>
										</label>
									</th>
									<td><input id="espresso_calendar_height" type="text" name="espresso_calendar_height" size="100" maxlength="100" value="<?php echo $espresso_calendar['espresso_calendar_height']; ?>" />
										<br />
										<span class="description">
<?php _e('Will make the entire calendar (including header) a pixel height.', 'event_espresso'); ?>
										</span></td>
								</tr>
<!--								<tr>
									<th> <label for="calendar_pages">
<?php _e('Page(s) Displaying the Calendar', 'event_espresso'); ?>
										</label>
									</th>
									<td><input id="calendar_pages" type="text" name="calendar_pages" size="100" maxlength="100" value="<?php echo isset($espresso_calendar['calendar_pages']) && !empty($espresso_calendar['calendar_pages']) ? $espresso_calendar['calendar_pages'] : 0; ?>" />
										<br />
										<span class="description">
<?php _e('This tells the plugin to load the calendar CSS file on specific pages. This should be a comma separated list of page id\'s. If left to the default of 0, the calendar stylesheet will load on every page of the site. You can find Page ID\'s by going to the WordPress menu Pages > All Pages, and hovering your mouse over the Page title, at the bottom of your browser a small box will appear with some code in it. Where it says post= then a number (post=4), that number is the Page ID. You can improve site performance and reduce conflicts by specifying which page/s have calendars on them.', 'event_espresso'); ?>
										</span></td>
								</tr>-->
								<tr>
									<th> <label for="calendar_page_post">
<?php _e('Link to Post or Registration Page', 'event_espresso'); ?>
										</label>
									</th>
									<td><?php echo select_input('espresso_page_post', array(array('id' => 'R', 'text' => __('Registration Page', 'event_espresso')), array('id' => 'P', 'text' => __('Post', 'event_espresso'))), $espresso_calendar['espresso_page_post'], 'id="calendar_page_post"'); ?> <br />
										<span class="description">
<?php _e('If you are using the "Create a Post" feature. Use this option to link to the posts that are created by Event Espresso, or select the link to go to the standard registration page.', 'event_espresso'); ?>
										</span></td>
								</tr>
								<tr>
									<th> <label for="enable-calendar-thumbs">
<?php _e('Enable Images in Calendar', 'event_espresso'); ?>
										</label>
									</th>
									<td><?php echo select_input('enable_calendar_thumbs', $values, isset($espresso_calendar['enable_calendar_thumbs']) && !empty($espresso_calendar['enable_calendar_thumbs']) ?  $espresso_calendar['enable_calendar_thumbs']: 0, 'id="enable-calendar-thumbs"'); ?>
										<br />
										<span class="description">
<?php _e('The "Featured Image" box in the event editor handles the thumbnail image URLs for each event. After setting the "Enable Calendar images" option to "Yes" in the calendar settings, upload an event image in the built-in WordPress media uploader, then click the Insert into post button on the media uploader.', 'event_espresso'); ?>
										</span></td>
								</tr>
							</tbody>
						</table>
						<h4>
<?php _e('Theme Settings', 'event_espresso'); ?>
						</h4>

						<table class="form-table">
							<tbody>
							<tr>
									<th> <label for="enable-cat-classes">
<?php _e('Enable CSS for Categories', 'event_espresso'); ?>
										</label>
									</th>
									<td><?php echo select_input('enable_cat_classes', $values, $espresso_calendar['enable_cat_classes'], 'id="enable-cat-classes"'); ?><br />
										<span class="description">
<?php _e('This setting allows you to set each category to display a different color. Set each category color in Event Espresso > Categories.', 'event_espresso'); ?>
										</span></td>
								</tr>
								<tr>
									<th> <label for="espresso_use_pickers">
<?php _e('Use Color Pickers', 'event_espresso'); ?>
										</label>
									</th>
									<td><?php echo select_input('espresso_use_pickers', $values, $espresso_calendar['espresso_use_pickers'], 'id="espresso_use_pickers"'); ?><br />
										<span class="description">
<?php _e('This allows you to customize the event background color and text color.', 'event_espresso'); ?>
										</span></td>
								</tr>
								<tr class="color-picker-selections">
									<th class="color-picker-style"> <label for="background-color">
<?php _e('Event Background Color', 'event_espresso') ?>
										</label>
									</th>
									<td><input id="background-color"type="text" name="ee_event_background" <?php echo (isset($espresso_calendar['ee_event_background']) && !empty($espresso_calendar['ee_event_background'])) ? 'value="' . $espresso_calendar['ee_event_background'] . '"' : 'value="#007BAE"' ?> /><div id="colorpicker-1"></div></td>
								</tr>
								<tr class="color-picker-selections">
									<th class="color-picker-style"> <label for="text-color">
<?php _e('Event Text Color', 'event_espresso') ?>
										</label>
									</th>
									<td><input id="text-color" type="text" name="ee_event_text_color" <?php echo (isset($espresso_calendar['ee_event_text_color']) && !empty($espresso_calendar['ee_event_text_color'])) ? 'value="' . $espresso_calendar['ee_event_text_color'] . '"' : 'value="#FFFFFF"' ?> /><div id="colorpicker-2"></div></td>
								</tr>
								


								<tr>
									<th>
										<label for="show_tooltips">
										<?php _e('Show Tooltips', 'event_espresso'); ?>
										</label>
									</th>
									<td>
										<?php echo select_input('show_tooltips', $values, $espresso_calendar['show_tooltips'], 'id="show_tooltips"'); ?><br />
										<span class="description">
<?php _e('This allows you to display a short description of the event on hover. The "display short descriptions" feature set in Event Espresso>Template settings should be switched on when using this feature. Be sure to use the <code>&lt;!--more--&gt;</code> tag to separate the short description from the entire event description.', 'event_espresso'); ?>
										</span>
									</td>
								</tr>
						<?php 
							$values_1 = array(
									array('id' => 'top', 'text' => __('Top', 'event_espresso')),
									array('id' => 'center', 'text' => __('Center', 'event_espresso')),
									array('id' => 'bottom', 'text' => __('Bottom', 'event_espresso'))
							);
							$values_2 = array(
									array('id' => 'left', 'text' => __('Left', 'event_espresso')),
									array('id' => 'center', 'text' => __('Center', 'event_espresso')),
									array('id' => 'right', 'text' => __('Right', 'event_espresso'))
							);
						?>		
								<tr class="tooltip-position-selections">
									<th class="tooltip-positions">
										<label for="tooltips_pos_my_1"><?php _e('Tooltip Position', 'event_espresso'); ?></label>
									</th>
									<td>
										<?php _e('Place Tooltip ', 'event_espresso'); ?>
<?php echo select_input('tooltips_pos_my_1', $values_1, !empty($espresso_calendar['tooltips_pos']['my_1']) ? $espresso_calendar['tooltips_pos']['my_1'] : 'bottom', 'id="tooltips_pos_my_1"'); ?>
<?php echo select_input('tooltips_pos_my_2', $values_2, !empty($espresso_calendar['tooltips_pos']['my_2']) ? $espresso_calendar['tooltips_pos']['my_2'] : 'center', 'id="tooltips_pos_my_2"'); ?>
										<?php _e('at the Event\'s  ', 'event_espresso'); ?>
<?php echo select_input('tooltips_pos_at_1', $values_1, !empty($espresso_calendar['tooltips_pos']['at_1']) ? $espresso_calendar['tooltips_pos']['at_1'] : 'center', 'id="tooltips_pos_at_1"'); ?>
<?php echo select_input('tooltips_pos_at_2', $values_2, !empty($espresso_calendar['tooltips_pos']['at_2']) ? $espresso_calendar['tooltips_pos']['at_2'] : 'center', 'id="tooltips_pos_at_2"'); ?><br />
										<span class="description"><?php _e('Default: "Bottom Center" and "Center Center"', 'event_espresso'); ?>
										</span>
									</td>
								</tr>

						<?php 
							$tooltip_style = array(
									array('id' => 'qtip-plain', 'text' => __('plain', 'event_espresso')),
									array('id' => 'qtip-light', 'text' => __('light', 'event_espresso')),
									array('id' => 'qtip-dark', 'text' => __('dark', 'event_espresso')),
									array('id' => 'qtip-red', 'text' => __('red', 'event_espresso')),
									array('id' => 'qtip-green', 'text' => __('green', 'event_espresso')),
									array('id' => 'qtip-blue', 'text' => __('blue', 'event_espresso')),
									array('id' => 'qtip-bootstrap', 'text' => __('Twitter Bootstrap', 'event_espresso')),
									array('id' => 'qtip-tipsy', 'text' => __('Tipsy', 'event_espresso')),
									array('id' => 'qtip-youtube', 'text' => __('YouTube', 'event_espresso')),
									array('id' => 'qtip-jtools', 'text' => __('jTools', 'event_espresso')),
									array('id' => 'qtip-cluetip', 'text' => __('clueTip', 'event_espresso')),
									array('id' => 'qtip-tipped', 'text' => __('Tipped', 'event_espresso')),
							);
						?>		
								
								<tr class="tooltip_style-selections">
									<th class="tooltip_style">
										<label for="tooltip_style"><?php _e('Tooltip Style', 'event_espresso'); ?></label>
									</th>
									<td>
										<?php echo select_input('tooltip_style', $tooltip_style, !empty($espresso_calendar['tooltip_style']) ? $espresso_calendar['tooltip_style'] : 'qtip-light', 'id="tooltip_style"'); ?><br/>
										<span class="description"><?php _e('Adds styling to tooltips. Default: light', 'event_espresso'); ?>
										</span>
									</td>
								</tr>
								
							</tbody>
						</table>
						<p>
							<input class="button-primary" type="submit" name="save_calendar_settings" value="<?php _e('Save Calendar Options', 'event_espresso'); ?>" id="save_calendar_settings2" />
<?php wp_nonce_field('espresso_form_check', 'update_calendar') ?>
						</p>
					</div>
					<!-- / .padding -->
				</div>
				<!-- / .inside -->
			</div>
			<!-- / .postbox -->
		</div>
		<!-- / .metabox-holder -->

		<!-- Advanced settings metabox -->
		<div class="metabox-holder">
			<div class="postbox">
				<h3 class="hndle"><?php _e('Advanced Settings', 'event_espresso'); ?></h3>
				<div class="inside">
					<div class="padding">
						<table class="form-table">
							<tbody>
							
								<tr>
									<th><label><?php _e('Header Style Left', 'event_espresso'); ?></label></th>
									<td>
										<?php _e('Left', 'event_espresso'); ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="text" name="espresso_calendar_header_left" id="espresso_calendar_header_left" class="medium-text" value="<?php echo htmlentities(stripslashes($espresso_calendar['header_left'])) ?>"><br />
										<?php _e('Center', 'event_espresso'); ?>:&nbsp;
										<input type="text" name="espresso_calendar_header_center" id="espresso_calendar_header_center" class="medium-text" value="<?php echo htmlentities(stripslashes($espresso_calendar['header_center'])) ?>"><br />
										<?php _e('Right', 'event_espresso'); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="text" class="medium-text" name="espresso_calendar_header_right" id="espresso_calendar_header_right" value="<?php echo htmlentities(stripslashes($espresso_calendar['header_right'])) ?>"><br />
										<span class="description"><?php _e('Defines the buttons and title at the top of the calendar.', 'event_espresso'); ?></span>
									</td>
								</tr>
								
								<tr>
									<th><label><?php _e('Button Text', 'event_espresso'); ?></label></th>
									<td>
										
										<?php _e('Previous', 'event_espresso'); ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="text" class="medium-text" name="buttonText_prev" id="buttonText_prev" value="<?php echo htmlentities(stripslashes($espresso_calendar['buttonText_prev'])) ?>"><br />

										<?php _e('Next', 'event_espresso'); ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="text" class="medium-text" name="buttonText_next" id="buttonText_next" value="<?php echo htmlentities(stripslashes($espresso_calendar['buttonText_next'])) ?>"><br />

										<?php _e('Previous Year', 'event_espresso'); ?>:&nbsp;
										<input type="text" class="medium-text" name="buttonText_prevYear" id="buttonText_prevYear" value="<?php echo htmlentities(stripslashes($espresso_calendar['buttonText_prevYear'])) ?>"><br />

										<?php _e('Next Year', 'event_espresso'); ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="text" class="medium-text" name="buttonText_nextYear" id="buttonText_nextYear" value="<?php echo htmlentities(stripslashes($espresso_calendar['buttonText_nextYear'])) ?>"><br />

										<?php _e('Today', 'event_espresso'); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="text" class="medium-text" name="buttonText_today" id="buttonText_today" value="<?php echo htmlentities(stripslashes($espresso_calendar['buttonText_today'])) ?>"><br />

										<?php _e('Month', 'event_espresso'); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="text" class="medium-text" name="buttonText_month" id="buttonText_month" value="<?php echo htmlentities(stripslashes($espresso_calendar['buttonText_month'])) ?>"><br />

										<?php _e('Week', 'event_espresso'); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="text" class="medium-text" name="buttonText_week" id="buttonText_week" value="<?php echo htmlentities(stripslashes($espresso_calendar['buttonText_week'])) ?>"><br />

										<?php _e('Day', 'event_espresso'); ?>:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="text" class="medium-text" name="buttonText_day" id="buttonText_day" value="<?php echo htmlentities(stripslashes($espresso_calendar['buttonText_day'])) ?>"><br />

										<span class="description"><?php _e('Text that will be displayed on the buttons in the header.', 'event_espresso'); ?></span>
									</td>
								</tr>
								
								<tr>
									<th><label><?php _e('Title Format', 'event_espresso'); ?></label></th>
									<td>
										<?php _e('Month', 'event_espresso'); ?>:&nbsp;
										<input type="text" class="medium-text" name="titleFormat_month" id="titleFormat_month" value="<?php echo htmlentities(stripslashes($espresso_calendar['titleFormat_month'])) ?>"><br />
										<?php _e('Week', 'event_espresso'); ?>:&nbsp;
										<input type="text" class="medium-text" name="titleFormat_week" id="titleFormat_week" value="<?php echo htmlentities(stripslashes($espresso_calendar['titleFormat_week'])) ?>"><br />
										<?php _e('Day', 'event_espresso'); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="text" class="medium-text" name="titleFormat_day" id="titleFormat_day" value="<?php echo htmlentities(stripslashes($espresso_calendar['titleFormat_day'])) ?>"><br />
										<span class="description"><?php _e('Determines the text that will be displayed in the header\'s title.', 'event_espresso'); ?></span>
									</td>
								</tr>
								
								<tr>
									<th>
										<label><?php _e('Column Format', 'event_espresso'); ?></label>
									</th>
									<td>
										<?php _e('Month', 'event_espresso'); ?>:&nbsp;
										<input type="text" class="medium-text" name="columnFormat_month" id="columnFormat_month" value="<?php echo htmlentities(stripslashes($espresso_calendar['columnFormat_month'])) ?>"><br />
										<?php _e('Week', 'event_espresso'); ?>:&nbsp;
										<input type="text" class="medium-text" name="columnFormat_week" id="columnFormat_week" value="<?php echo htmlentities(stripslashes($espresso_calendar['columnFormat_week'])) ?>"><br />
										<?php _e('Day', 'event_espresso'); ?>:&nbsp;&nbsp;&nbsp;&nbsp;
										<input type="text" class="medium-text" name="columnFormat_day" id="columnFormat_day" value="<?php echo htmlentities(stripslashes($espresso_calendar['columnFormat_day'])) ?>"><br />
										<span class="description"><?php _e('Determines the text that will be displayed on the calendar\'s column headings.', 'event_espresso'); ?></span>
									</td>
								</tr>
								
								<!--<tr>
									<th><label><?php _e('Month Names', 'event_espresso'); ?></label></th>
									<td>
										<textarea name="espresso_calendar_monthNames" id="espresso_calendar_monthNames" cols="30" rows="5"><?php echo stripslashes($espresso_calendar['espresso_calendar_monthNames']) ?></textarea>
										<br />
										<span class="description">
<?php _e('Full names of months.', 'event_espresso'); ?>
										</span>
									</td>
								</tr>
								<tr>
									<th><label><?php _e('Month Names Short', 'event_espresso'); ?></label></th>
									<td><textarea name="espresso_calendar_monthNamesShort" id="espresso_calendar_monthNamesShort" cols="30" rows="5"><?php echo stripslashes($espresso_calendar['espresso_calendar_monthNamesShort']) ?></textarea>
										<br />
										<span class="description">
<?php _e('Abbreviated names of months.', 'event_espresso'); ?>
										</span></td>
								</tr>
								<tr>
									<th><label><?php _e('Day Names', 'event_espresso'); ?></label></th>
									<td><textarea name="espresso_calendar_dayNames" id="espresso_calendar_dayNames" cols="30" rows="5"><?php echo stripslashes($espresso_calendar['espresso_calendar_dayNames']) ?></textarea>
										<br />
										<span class="description">
<?php _e('Full names of days-of-week.', 'event_espresso'); ?>
										</span></td>
								</tr>
								<tr>
									<th><label><?php _e('Day Names Short', 'event_espresso'); ?></label></th>
									<td><textarea name="espresso_calendar_dayNamesShort" id="espresso_calendar_dayNamesShort" cols="30" rows="5"><?php echo stripslashes($espresso_calendar['espresso_calendar_dayNamesShort']) ?></textarea>
										<br />
										<span class="description">
<?php _e('Abbreviated names of days-of-week.', 'event_espresso'); ?>
										</span></td>
								</tr>-->
								</tbody>
						</table>
						<h4>
<?php _e('Memory Management', 'event_espresso'); ?>
						</h4>

						<table class="form-table">
							<tbody>
								<?php 
								//Throttle settings
//								$throttle_values = array(
//										array('id' => '50', 'text' => __('Really Low (50 records)', 'event_espresso')),
//										array('id' => '100', 'text' => __('Low (100 records)', 'event_espresso')),
//										array('id' => '250', 'text' => __('Low - Medium  (250 records)', 'event_espresso')),
//										array('id' => '500', 'text' => __('Medium (500 records)', 'event_espresso')),
//										array('id' => '750', 'text' => __('Medium - High (750 records)', 'event_espresso')),
//										array('id' => '1000', 'text' => __('High (1000 records)', 'event_espresso')),
//										array('id' => '1', 'text' => __('All the Way! (all records)', 'event_espresso')),
//								);
								?>
<!--								<tr>
									<th>
										<label for="throttle_enable">
										<?php _e('Enable Database Throttling', 'event_espresso'); ?>
										</label>
									</th>
									<td>
										<?php echo select_input('throttle_enable', $values, empty($espresso_calendar['throttle']['enable']) ? $espresso_calendar['throttle']['enable'] : false, 'id="throttle_enable"'); ?> <?php echo select_input('throttle_amount', $throttle_values, !empty($espresso_calendar['throttle']['amount']) ? $espresso_calendar['throttle']['amount'] : '250', 'id="throttle_amount"'); ?><br />
									<span class="description">
<?php _e('Enabling this setting allows you to limit the amount of records retrieved from the database .', 'event_espresso'); ?>
										</span>
									</td>
								</tr>-->
								
								<tr>
									<th> <label for="show_attendee_limit">
<?php _e('Display Attendee Limits', 'event_espresso'); ?>
										</label>
									</th>
									<td><?php echo select_input('show_attendee_limit', $values, !empty($espresso_calendar['show_attendee_limit']) ? $espresso_calendar['show_attendee_limit'] : false, 'id="show_attendee_limit"'); ?><br />
									<span class="description">
<?php _e('Enabling this setting increases the amount of database queries and may break the calendar on some servers.', 'event_espresso'); ?>
										</span>
									</td>
								</tr>
								
								<tr>
									<th> <label for="disable_categories">
<?php _e('Disable Categories?', 'event_espresso'); ?>
										</label>
									</th>
									<td><?php echo select_input('disable_categories', $values, !empty($espresso_calendar['disable_categories']) ? $espresso_calendar['disable_categories'] : false, 'id="disable_categories"'); ?><br />
									<span class="description">
<?php _e('Disabling categories in the calendar may potentially speed up the calendar and allow you to load more events, but you will not be able to use the category colors and css class options.', 'event_espresso'); ?>
										</span>
									</td>
								</tr>
								
							</tbody>
						</table>
						<input type="hidden" name="update_calendar" value="update" />
						<p>
							<input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Calendar Options', 'event_espresso'); ?>" id="save_calendar_settings_1" />
<?php wp_nonce_field('espresso_form_check', 'update_calendar') ?>
						</p>
						<p>
							<?php _e('Reset Calendar Settings?', 'event_espresso'); ?>
							<input name="reset_calendar" type="checkbox" />
<?php wp_nonce_field('espresso_form_check', 'reset_calendar_nonce') ?>
						</p>
					</div>
					<!-- / .padding -->
				</div>
				<!-- / .inside -->
			</div>
			<!-- / .postbox -->
		</div>
		<!-- / .metabox-holder -->
		<!--</li>
</ul>-->
	</form>
		<?php
		$main_post_content = ob_get_clean();
		espresso_choose_layout($main_post_content, event_espresso_display_right_column());
		include_once('calendar_help.php');
		?>

	</div>
	<!-- / #wrap -->
	<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function($){
			$("input[name='time_format']").click(function(){
				if ( "time_format_custom_radio" != $(this).attr("id") )
					$("input[name='time_format_custom']").val( $(this).val() ).siblings('.example').text( $(this).siblings('span').text() );
			});
			$("input[name='time_format_custom']").focus(function(){
				$("#time_format_custom_radio").attr("checked", "checked");
			});

			// disable color picker & thumb sizes inputs & fade if not use controls true
			window.scp = $('select#espresso_use_pickers option:selected').val();
			window.ect = $('select#enable-calendar-thumbs option:selected').val();
			window.ectt = $('select#show_tooltips option:selected').val();

			
			if(window.ect == 'false'){
				$('tr#thumbnail-sizes td input').attr('disabled', true);
				$('tr#thumbnail-sizes').attr('style', "opacity: .3");
			}
			$('select#enable-calendar-thumbs').change(function(){
				window.ect = $('select#enable-calendar-thumbs option:selected').val();
				if(window.ect == 'false'){
					$('tr#thumbnail-sizes td input').attr('disabled', true);
					$('tr#thumbnail-sizes').attr('style', "opacity: .3");
				}else{
					$('tr#thumbnail-sizes td input').removeAttr('disabled', true);
					$('tr#thumbnail-sizes').removeAttr('style', "opacity: .3");
				}
			});
			
			// color picker settings
			if(window.scp == ''){
				$('input#event-background, input#event-text').attr('disabled', true);
				$('.color-picker-style').attr('style', "opacity: .3");
				$('tr.color-picker-selections th, tr.color-picker-selections td').attr('style', "opacity: .3");
			}
			$('select#espresso_use_pickers').change(function(){
				window.scp = $('select#espresso_use_pickers option:selected').val();
				if(window.scp == ''){
					$('input#event-background, input#event-text').attr('disabled', true);
					$('tr.color-picker-selections th, tr.color-picker-selections td').attr('style', "opacity: .3");
				}else {
					$('input#event-background, input#event-text').removeAttr('disabled', true);
					$('tr.color-picker-selections th, tr.color-picker-selections td').removeAttr('style');
				}
			});
			$('#colorpicker-1').hide();
			$('#colorpicker-2').hide();
			$('#colorpicker-1').farbtastic("#background-color");
			$('#colorpicker-2').farbtastic("#text-color");
			$("#background-color").click(function(){$('#colorpicker-1').slideToggle()});
			$("#text-color").click(function(){$('#colorpicker-2').slideToggle()});
			
			
			// tooltip settings initialization
			if(window.ectt == ''){
				$('input#show_tooltips').attr('disabled', true);
				$('.tooltip-positions').attr('style', "opacity: .3");
				$('tr.tooltip-position-selections th, tr.tooltip-position-selections td').attr('style', "opacity: .3");
			}
			$('select#show_tooltips').change(function(){
				window.ectt = $('select#show_tooltips option:selected').val();
				if(window.ectt == ''){
					$('input#event-background, input#event-text').attr('disabled', true);
					$('tr.tooltip-position-selections th, tr.tooltip-position-selections td').attr('style', "opacity: .3");
				}else {
					$('input#tooltips_pos_my_1, input#tooltips_pos_my_2, input#tooltips_pos_at_1, input#tooltips_pos_at_2').removeAttr('disabled', true);
					$('tr.tooltip-position-selections th, tr.tooltip-position-selections td').removeAttr('style');
				}
			});
			
			
			// WP toggle function
			postboxes.add_postbox_toggles('espresso_calendar');

		});

		//]]>
	</script>
	<?php
}
