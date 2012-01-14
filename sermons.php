<?php
/*
Plugin Name: Sermon Manager for WordPress
Plugin URI: http://wpforchurch.com
Description: Add audio and video sermons, manage speakers, series, and more. Visit <a href="http://wpforchurch.com" target="_blank">Wordpress for Church</a> for tutorials and support.
Version: 1.1.3
Author: Jack Lamb
Author URI: http://wpforchurch.com/
License: GPL2
*/

// Security check to see if someone is accessing this file directly
if(preg_match("#^sermons.php#", basename($_SERVER['PHP_SELF']))) exit();

// Define the plugin URL
define('WPFC_SERMONS', plugins_url() . '/sermon-manager-for-wordpress');

//Create sermon Custom Post Type
add_action('init', 'create_sermon_types');
function create_sermon_types() 
{
  $plugin = WPFC_SERMONS;
  $labels = array(
    'name' => _x('Sermons', 'post type general name'),
    'singular_name' => _x('Sermon', 'post type singular name'),
    'add_new' => _x('Add New', 'sermon'),
    'add_new_item' => __('Add New Sermon'),
    'edit_item' => __('Edit Sermon'),
    'new_item' => __('New Sermon'),
    'view_item' => __('View Sermon'),
    'search_items' => __('Search Sermons'),
    'not_found' =>  __('No sermons found'),
    'not_found_in_trash' => __('No sermons found in Trash'), 
    'parent_item_colon' => '',
    'menu_name' => 'Sermons',
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'menu_icon' => $plugin . '/img/book-open-bookmark.png',
	'capability_type' => 'post',
    'has_archive' => 'sermons', 
    'rewrite' => array('slug' => 'sermon'),
    'hierarchical' => false,
    'menu_position' => '19',
    'supports' => array('title','comments')
  ); 
  register_post_type('wpfc_sermon',$args);
  //flush_rewrite_rules();
}

//create new taxonomies: preachers, sermon series & topics
add_action( 'init', 'create_sermon_taxonomies', 0 );
function create_sermon_taxonomies()
{
//Preachers
$labels = array(	
	'name' => _x( 'Preachers', 'taxonomy general name' ),
	'singular_name' => _x( 'Preacher', 'taxonomy singular name' ),
	'menu_name' => __( 'Preachers' ),
	'search_items' => __( 'Search preachers' ), 
	'popular_items' => __( 'Most frequent preachers' ), 
	'all_items' => __( 'All preachers' ),
	'edit_item' => __( 'Edit preachers' ),
	'update_item' => __( 'Update preachers' ), 
	'add_new_item' => __( 'Add new preacher' ),
	'new_item_name' => __( 'New preacher name' ), 
	'separate_items_with_commas' => __( 'Separate multiple preachers with commas' ),
	'add_or_remove_items' => __( 'Add or remove preachers' ),
	'choose_from_most_used' => __( 'Choose from most frequent preachers' ),
	'parent_item' => null,
    'parent_item_colon' => null,
);

register_taxonomy('wpfc_preacher','wpfc_sermon', array(
	'hierarchical' => false, 
	'labels' => $labels, 
	'show_ui' => true,
	'query_var' => true,
    'rewrite' => array ( 'slug' => 'preacher' ),
));
//Sermon Series
$labels = array(	
	'name' => _x( 'Sermon Series', 'taxonomy general name' ),
	'graphic' => '',
	'singular_name' => _x( 'Sermon Series', 'taxonomy singular name' ),
	'menu_name' => __( 'Sermon Series' ),
	'search_items' => __( 'Search sermon series' ), 
	'popular_items' => __( 'Most frequent sermon series' ), 
	'all_items' => __( 'All sermon series' ),
	'edit_item' => __( 'Edit sermon series' ),
	'update_item' => __( 'Update sermon series' ), 
	'add_new_item' => __( 'Add new sermon series' ),
	'new_item_name' => __( 'New sermon series name' ), 
	'separate_items_with_commas' => __( 'Separate sermon series with commas' ),
	'add_or_remove_items' => __( 'Add or remove sermon series' ),
	'choose_from_most_used' => __( 'Choose from most used sermon series' ),
	'parent_item' => null,
    'parent_item_colon' => null,
);

register_taxonomy('wpfc_sermon_series','wpfc_sermon', array(
	'hierarchical' => false, 
	'labels' => $labels, 
	'show_ui' => true,
	'query_var' => true,
    'rewrite' => array ( 'slug' => 'sermon-series' ),
));
//Sermon Topics
$labels = array(	
	'name' => _x( 'Sermon Topics', 'taxonomy general name' ),
	'singular_name' => _x( 'Sermon Topics', 'taxonomy singular name' ),
	'menu_name' => __( 'Sermon Topics' ),
	'search_items' => __( 'Search sermon topics' ), 
	'popular_items' => __( 'Most popular sermon topics' ), 
	'all_items' => __( 'All sermon topics' ),
	'edit_item' => __( 'Edit sermon topic' ),
	'update_item' => __( 'Update sermon topic' ), 
	'add_new_item' => __( 'Add new sermon topic' ),
	'new_item_name' => __( 'New sermon topic' ), 
	'separate_items_with_commas' => __( 'Separate sermon topics with commas' ),
	'add_or_remove_items' => __( 'Add or remove sermon topics' ),
	'choose_from_most_used' => __( 'Choose from most used sermon topics' ),
	'parent_item' => null,
    'parent_item_colon' => null,
);

register_taxonomy('wpfc_sermon_topics','wpfc_sermon', array(
	'hierarchical' => false, 
	'labels' => $labels, 
	'show_ui' => true,
	'query_var' => true,
    'rewrite' => array ( 'slug' => 'topics' ),
));
}

//add filter to insure the text Sermon, or sermon, is displayed when user updates a sermon
add_filter('post_updated_messages', 'wpfc_sermon_updated_messages');
function wpfc_sermon_updated_messages( $messages ) {
  global $post, $post_ID;

  $messages['wpfc_sermon'] = array(
    0 => '', // Unused. Messages start at index 1.
    1 => sprintf( __('Sermon updated. <a href="%s">View sermon</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Sermon updated.'),
    /* translators: %s: date and time of the revision */
    5 => isset($_GET['revision']) ? sprintf( __('Sermon restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Sermon published. <a href="%s">View sermon</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Sermon saved.'),
    8 => sprintf( __('Sermon submitted. <a target="_blank" href="%s">Preview sermon</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Sermon scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview sermon</a>'),
      // translators: Publish box date format, see http://php.net/date
      date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Sermon draft updated. <a target="_blank" href="%s">Preview sermon</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );

  return $messages;
}

// TO DO: Add more help information
//display contextual help for Sermons
add_action( 'contextual_help', 'add_sermon_help_text', 10, 3 );

function add_sermon_help_text($contextual_help, $screen_id, $screen) { 
  //$contextual_help .= var_dump($screen); // use this to help determine $screen->id
  if ('wpfc_sermon' == $screen->id ) {
    $contextual_help =
      '<p>' . __('Things to remember when adding or editing a sermon:') . '</p>' .
      '<ul>' .
      '<li>' . __('Specify a sermon series if appropriate. This will help your site visitors while browsing sermons.') . '</li>' .
      '<li>' . __('Specify the correct preacher of the sermon.') . '</li>' .
      '</ul>' .
      '<p>' . __('If you want to schedule the sermon to be published in the future:') . '</p>' .
      '<ul>' .
      '<li>' . __('Under the Publish meta box, click on the Edit link next to Publish.') . '</li>' .
      '<li>' . __('Change the date to the date to actual publish this article, then click on Ok.') . '</li>' .
      '</ul>' .
      '<p><strong>' . __('For more help:') . '</strong></p>' .
      '<p>' . __('<a href="http://wpforchurch.com/" target="_blank">Wordpress for Church</a>') . '</p>' ;
  } elseif ( 'edit-sermon' == $screen->id ) {
    $contextual_help = 
      '<p>' . __('This is the help screen displaying on the sermons page.') . '</p>' ;
  }
  return $contextual_help;
}

// Add filter for custom search: includes bible_passage, sermon_description in WordPress search
add_filter('posts_where', 'customSearchWhere');
add_filter('posts_join', 'customSearchJoin');
add_filter('posts_request', 'request_filter');
add_filter('posts_groupby', 'customSearchGroup');

function request_filter($content)
{
  // var_dump($content);
  return $content;
}

function customSearchWhere($content)
{
  global $wpdb;

  if (is_search())
  {
  	$search = get_search_query();	
    $content .= " or ({$wpdb->prefix}postmeta.meta_key = 'bible_passage' and {$wpdb->prefix}postmeta.meta_value LIKE '%{$search}%') ";
    $content .= " or ({$wpdb->prefix}postmeta.meta_key = 'sermon_description' and {$wpdb->prefix}postmeta.meta_value LIKE '%{$search}%') ";
  }
  
  return $content;
}

function customSearchJoin($content)
{
  global $wpdb;

  if (is_search())
  {
    $content .= " left join {$wpdb->prefix}postmeta on {$wpdb->prefix}postmeta.post_id = {$wpdb->prefix}posts.id ";
  }
  return $content;
}

function customSearchGroup($content)
{
  global $wpdb;
  if (is_search())
  {
    $content .= " {$wpdb->prefix}posts.id ";
  }
  return $content;
}
// ==================================== End of custom search ===============

//enqueue needed js and styles on sermon edit screen
add_action('admin_enqueue_scripts', 'admin_script_post');

function admin_script_post() {
global $post_type;
	    if( 'wpfc_sermon' != $post_type )
	        return;
		wp_enqueue_script('jquery');
		wp_enqueue_script('thickbox');
		wp_enqueue_script('media-upload');
		wp_enqueue_style('thickbox');
		wp_enqueue_script('jquery-ui-datepicker', plugins_url('/js/jquery-ui-1.8.14.datepicker.min.js', __FILE__) );
		wp_enqueue_style('jquery-ui', plugins_url('/css/jquery.ui.datepicker.css', __FILE__) );  
		//backwards compatible wysiwyg editor for pre-3.3
		if(function_exists(wp_editor)) :
		else :
		wp_tiny_mce( TRUE, Array( "editor_selector" => "wysiwyg" ) );
		endif;
		//wp_enqueue_script('admin', $this->plugin_url . 'js/admin.js', array('jquery'), $this->version);
		//wp_enqueue_script('admin');
}

//Create custom fields and write panels for the Sermon post type
add_action("admin_init", "admin_init");

function admin_init() {
	add_meta_box("wpfc_sermon_details", "Sermon Details", "wpfc_sermon_details", "wpfc_sermon", "normal", "high");
	add_meta_box("wpfc_sermon_files", "Sermon Files", "wpfc_sermon_files", "wpfc_sermon", "normal", "high");
}
//top meta box - sermon details
function wpfc_sermon_details() {
	global $post;
	$custom = get_post_custom($post->ID);
	$bible_passage = $custom["bible_passage"] [0];
	$sermon_description = $custom["sermon_description"] [0];
    $sermon_date = $custom["sermon_date"] [0];
    $service_type = $custom["service_type"] [0];
	?>
	
<?php 
// Use nonce for verification  
wp_nonce_field( plugin_basename( __FILE__ ), 'sermons_nounce' );
?>
	<p><label>Date</label>
	<script>jQuery(document).ready(function(){jQuery( "input[name='sermon_date']" ).datepicker({ dateFormat: 'mm/dd/yy', numberOfMonths: 1 }); jQuery( "#ui-datepicker-div" ).hide();});</script>
	<?php 
	$dateMeta = get_post_meta($post->ID, 'sermon_date', true);
    if (get_post_meta($post->ID, 'sermon_date', true)) {
	$displayDate = date('F j, Y', $dateMeta);
	} else { $displayDate = '';
	}
	?>
	<input type="text" name="sermon_date" id="sermon_date" value="<?php echo $displayDate ?>" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<label>Service Type:</label> 
		<select id="service_type" name="service_type">
			<option value="Adult Bible Class"<?php if ((get_post_meta($post->ID, 'service_type', true)) == "Adult Bible Class") : ?> selected="true"<?php endif; ?>>Adult Bible Class</option>
			<option value="Sunday AM"<?php if ((get_post_meta($post->ID, 'service_type', true)) == "Sunday AM") : ?> selected="true"<?php endif; ?>>Sunday AM</option>
			<option value="Sunday PM"<?php if ((get_post_meta($post->ID, 'service_type', true)) == "Sunday PM") : ?> selected="true"<?php endif; ?>>Sunday PM</option>
			<option value="Midweek Service"<?php if ((get_post_meta($post->ID, 'service_type', true)) == "Midweek Service") : ?> selected="true"<?php endif; ?>>Midweek Service</option>
			<option value="Special Service"<?php if ((get_post_meta($post->ID, 'service_type', true)) == "Special Service") : ?> selected="true"<?php endif; ?>>Special Service</option>
			<option value="Radio Broadcast"<?php if ((get_post_meta($post->ID, 'service_type', true)) == "Radio Broadcast") : ?> selected="true"<?php endif; ?>>Radio Broadcast</option>
		</select>
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        <label>Main Bible Passage:</label> 
	<input type="text" size="40" name="bible_passage" value="<?php echo $bible_passage; ?>" /></p>
	<?php if(function_exists(wp_editor)) {
		$settings = array(
			'wpautop' => false,
			'media_buttons' => false,
		); 
		wp_editor($sermon_description, 'sermon_description', $settings ); }
	else { ?>
                </p><p><label>Sermon Description:</label></p>
		<textarea cols="100" rows="10" id="sermon_description" name="sermon_description" class="wysiwyg"><?php echo $sermon_description; ?></textarea>
    <?php } ?>
	<?php
}
//next meta box - sermon files
function wpfc_sermon_files() {
	global $post;
	$custom = get_post_custom($post->ID);
	$sermon_audio = $custom["sermon_audio"] [0];
	$sermon_video = $custom["sermon_video"] [0];
	?>
	<p><label>Location of MP3 file: <br />
	<input type="text" size="100" name="sermon_audio" value="<?php echo $sermon_audio; ?>" />  <a class="thickbox menu-top menu-top-first menu-top-last button" href="media-upload.php?post_id=<?php the_ID(); ?>&TB_iframe=1&width=640&height=324">Upload A New One</a></strong></label></p>
	<p><label>Paste your video embed code:</label><br />
	<textarea cols="70" rows="5" name="sermon_video"><?php echo $sermon_video; ?></textarea></p>
	<p>If you would like to add pdf, doc, ppt, or other file types:<br/></p>
    <p><a class="thickbox menu-top menu-top-first menu-top-last button" href="media-upload.php?post_id=<?php the_ID(); ?>&TB_iframe=1&width=640&height=324">Upload Additional Files</a></strong></label></p>
	<div id="wpfc-attachments">
    <?php
        $args = array(
          'post_type' => 'attachment',
          'numberposts' => -1,
          'post_status' => null,
          'post_parent' => $post->ID,
          );
        $attachments = get_posts($args);
        if ($attachments) {
		  echo '<p>Currently Attached Files: <ul>';
          foreach ($attachments as $attachment) {
            echo '<li>&nbsp;&nbsp;<a target="_blank" href="'.wp_get_attachment_url($attachment->ID).'">';
            echo $attachment->post_title;
            echo '</a></li>';
          }
		  echo '</ul></p>';
        }
    ?>
    </div>
	<?php
}
//make sure that we save all of our details!
add_action('save_post', 'save_details');

function save_details(){
  global $post;
  if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
	return $post_id;
  }

  if( defined('DOING_AJAX') && DOING_AJAX ) { //Prevents the metaboxes from being overwritten while quick editing.
	return $post_id;
  }

  if( ereg('/\edit\.php', $_SERVER['REQUEST_URI']) ) { //Detects if the save action is coming from a quick edit/batch edit.
	return $post_id;
  }
  // added nonce check
  wp_verify_nonce( $_POST['sermons_nounce'], plugin_basename( __FILE__ ) );
  // save all meta data
  update_post_meta($post->ID, "bible_passage", $_POST["bible_passage"]);
  update_post_meta($post->ID, "sermon_description", $_POST["sermon_description"]);
  update_post_meta($post->ID, "sermon_audio", $_POST["sermon_audio"]);
  update_post_meta($post->ID, "sermon_video", $_POST["sermon_video"]);
  update_post_meta($post->ID, "sermon_date", strtotime($_POST["sermon_date"]));
  update_post_meta($post->ID, "service_type", $_POST["service_type"]);
}

//create custom columns when listing sermon details in the Admin
add_action("manage_posts_custom_column", "wpfc_sermon_columns");
add_filter("manage_edit-wpfc_sermon_columns", "wpfc_sermon_edit_columns");

function wpfc_sermon_edit_columns($columns) {
	$columns = array(
		"cb" => "<input type=\"checkbox\" />",
		"title" => "Sermon Title",
		"preacher" => "Preacher",
		"series" => "Sermon Series",
		"topics" => "Topics",
		"views" => "Views",
	);
	return $columns;
}

function wpfc_sermon_columns($column){
	global $post;
	
	switch ($column){
		case "preacher":
			echo get_the_term_list($post->ID, 'wpfc_preacher', '', ', ','');
			break;
		case "series":
			echo get_the_term_list($post->ID, 'wpfc_sermon_series', '', ', ','');
			break;
		case "topics":
			echo get_the_term_list($post->ID, 'wpfc_sermon_topics', '', ', ','');
			break;
		case "views":
			echo getPostViews($post->ID);
			break;			
	}
}
/* Future update!
// Create a settings page: list service types, 
add_action('admin_menu', 'wpfc_settings_menu');

function wpfc_settings_menu() {
add_submenu_page('edit.php?post_type=wpfc_sermon', 'Options', 'Options', 'manage_options', 'wpfc_sermon-options', array(&$this, 'options_page') );
}
*/

/* Template selection */
// Include template for displaying sermons by Preacher
add_filter('template_include', 'sermon_template_include');
function sermon_template_include($template) {
		if(get_query_var('post_type') == 'wpfc_sermon') {
			if ( is_archive() || is_search() ) :
				if(file_exists(get_stylesheet_directory() . '/archive-wpfc_sermon.php'))
					return get_stylesheet_directory() . '/archive-wpfc_sermon.php';
				return plugin_dir_path( __FILE__ ) . '/views/archive-wpfc_sermon.php';
			else :
				if(file_exists(get_stylesheet_directory() . '/single-wpfc_sermon.php'))
					return get_stylesheet_directory() . '/single-wpfc_sermon.php';
				return plugin_dir_path( __FILE__ ) . '/views/single-wpfc_sermon.php';
			endif;
		}
		return $template;
}

// Include template for displaying sermons by Preacher
add_filter('template_include', 'preacher_template_include');
function preacher_template_include($template) {
		if(get_query_var('taxonomy') == 'wpfc_preacher') {
			if(file_exists(get_stylesheet_directory() . '/taxonomy-wpfc_preacher.php')) 
				return get_stylesheet_directory() . '/taxonomy-wpfc_preacher.php'; 
			return plugin_dir_path(__FILE__) . '/views/taxonomy-wpfc_preacher.php';	
		}
		return $template;
}

// Include template for displaying sermon series
add_filter('template_include', 'series_template_include');
function series_template_include($template) {
		if(get_query_var('taxonomy') == 'wpfc_sermon_series') {
			if(file_exists(get_stylesheet_directory() . '/taxonomy-wpfc_sermon_series.php'))
				return get_stylesheet_directory() . '/taxonomy-wpfc_sermon_series.php';
			return plugin_dir_path(__FILE__) . '/views/taxonomy-wpfc_sermon_series.php';
		}
		return $template;
}
/*
 * Theme developers can add support for sermon manager to their theme with 
 * add_theme_support( 'sermon-manager' );
 * in functions.php. For now, this will disable the loading of the jwplayer javascript
 */
 
// Add scripts only to single sermon pages
add_action('wp_head', 'add_wpfc_js');
function add_wpfc_js() {
	if (is_single() && 'wpfc_sermon' == get_post_type() ) {
		if ( ! current_theme_supports( 'sermon-manager' ) ) :
			echo '<script type="text/javascript" src="'.WPFC_SERMONS . '/js/jwplayer.js"></script>';		
		endif;
	}
	if (is_single() && 'wpfc_sermon' == get_post_type() ) { ?>
		<script src="http://code.bib.ly/bibly.min.js"></script>
		<link href="http://code.bib.ly/bibly.min.css" rel="stylesheet" />
		<script>
			// Bible version for all links. Leave blank to let user choose.
			bibly.linkVersion = 'KJV'; 
			// Turn off popups
			bibly.enablePopups = true;
			// ESV, NET, KJV, or LEB are the currently supported popups.
			bibly.popupVersion = 'KJV';
		</script>
	<?php
	}
}

// Add CSS to entire site. Looks for sermon.css in the main template directory first.
add_action('wp_head', 'add_wpfc_css');
function add_wpfc_css() {
	if(file_exists(get_stylesheet_directory() . '/sermon.css'))
		echo '<link rel="stylesheet" href="'.get_stylesheet_directory() . '/sermon.css'.'" type="text/css" >';
	echo '<link rel="stylesheet" href="'.WPFC_SERMONS . '/css/style.css'.'" type="text/css" >';
}

// Track post views - Added from http://wpsnipp.com/index.php/functions-php/track-post-views-without-a-plugin-using-post-meta/
function getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0 View";
    }
    return $count.' Views';
}
function setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

// Shortcodes to show speakers or series
add_shortcode('sermons_speaker', 'getSpeaker');
add_shortcode('sermons_series', 'getSeries');
//add_shortcode('sermons', 'getSermons');

/*function getSermons($atts, $content='')
{
  global $wpdb, $post;

  $index_query = new WP_Query('post_type=wpfc_sermon&posts_per_page=20&order=ASC');

	while ($index_query->have_posts()){
    $index_query->the_post();
    echo '<h3 style="line-height: 1.5em; margin: 10px 0 0 0;">';
    echo '<a href="'.get_permalink().'">';
    the_title();
    echo '</a></h3>';
    echo get_post_meta(get_the_ID(), 'bible_passage', true);
    echo ' | ';
    echo the_terms( $post->ID, 'wpfc_preacher', '', ', ', ' ' );
  }
  
}
*/
function getSeries($atts, $content='')
{
  global $wpdb, $post;
  
  $where = '';
  $orderby = '';
  
  if (isset($atts['name']))
  {
    // search by name
    $where = " and {$wpdb->prefix}terms.name = '".$atts['name']."' ";   
  }
  if (isset($atts['order']))
  {
    // sort order
    $orderby = " order by {$wpdb->prefix}posts.post_date ".$atts['order']." ";
  }
  
  $query = get_custom_query('wpfc_sermon_series', $where, $orderby);
  
  $myPosts = $wpdb->get_results($query);
   
  echo '<ul>';
  foreach ($myPosts as $value)
  {
    $post = get_post($value->object_id);
    echo '<li><a href="'.get_permalink($post->ID).'">' . $post->post_title . '</a></li>';
  }
  echo '</ul>';
    

}

function getSpeaker($atts, $content='')
{
  global $wpdb, $post;
  
  $where = '';
  $orderby = '';
  
  if (isset($atts['name']))
  {
    // search by name
    $where = " and {$wpdb->prefix}terms.name = '".$atts['name']."' ";   
  }
  if (isset($atts['order']))
  {
    // sort order
    $orderby = " order by {$wpdb->prefix}posts.post_date ".$atts['order']." ";
  }
  
  $query = get_custom_query('wpfc_preacher', $where, $orderby);
  
  $myPosts = $wpdb->get_results($query);
   
  echo '<ul>';
  foreach ($myPosts as $value)
  {
    // echo 'post-id: ' . $value->object_id . '<br />';
    $post = get_post($value->object_id);
    echo '<li><a href="'.get_permalink($post->ID).'">' . $post->post_title . '</a></li>';
  }
  echo '</ul>';
    
}

function get_custom_query($term, $where, $orderby)
{
  global $wpdb;

  // we need a custom query to get all data from the wpfc_preacher taxonomy
  
  $query = "select object_id 
    from {$wpdb->prefix}term_taxonomy, 
    {$wpdb->prefix}terms,
    {$wpdb->prefix}term_relationships,
    {$wpdb->prefix}posts
    where {$wpdb->prefix}term_taxonomy.term_id = {$wpdb->prefix}terms.term_id
    and {$wpdb->prefix}term_relationships.term_taxonomy_id = {$wpdb->prefix}term_taxonomy.term_taxonomy_id
    and {$wpdb->prefix}term_relationships.object_id = {$wpdb->prefix}posts.ID
    and taxonomy = '".$term."' " ;
    
  $query .= $where;
  
  $query .= $orderby;    
    
   // var_dump($query);
  
   return $query;
}

// Add the number of sermons to the Right Now on the Dashboard
add_action('right_now_content_table_end', 'wpfc_right_now');
function wpfc_right_now() {
    $num_posts = wp_count_posts('wpfc_sermon');
    $num = number_format_i18n($num_posts->publish);
    $text = _n('Sermon', 'Sermons', intval($num_posts->publish));
    if ( current_user_can('edit_posts') ) {
        $num = "<a href='edit.php?post_type=wpfc_sermon'>$num</a>";
        $text = "<a href='edit.php?post_type=wpfc_sermon'>$text</a>";
    }
    echo '<td class="first b b-sermon">' . $num . '</td>';
    echo '<td class="t sermons">' . $text . '</td>';
    echo '</tr>';
}

/**
 * Recent Sermons Widget
 */
class WP4C_Recent_Sermons extends WP_Widget {

	function WP4C_Recent_Sermons() {
		$widget_ops = array('classname' => 'widget_recent_sermons', 'description' => __( "The most recent sermons on your site") );
		parent::__construct('recent-sermons', __('Recent Sermons'), $widget_ops);
		$this->alt_option_name = 'widget_recent_entries';

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );
	}

	function widget($args, $instance) {
		$cache = wp_cache_get('widget_recent_sermons', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( isset($cache[$args['widget_id']]) ) {
			echo $cache[$args['widget_id']];
			return;
		}

		ob_start();
		extract($args);

		$title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Sermons') : $instance['title'], $instance, $this->id_base);
		if ( ! $number = absint( $instance['number'] ) )
 			$number = 10;

		$r = new WP_Query(array('post_type' => 'wpfc_sermon', 'posts_per_page' => $number, 'no_found_rows' => true, 'post_status' => 'publish', 'ignore_sticky_posts' => true));
		if ($r->have_posts()) :
?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<ul>
		<?php  while ($r->have_posts()) : $r->the_post(); ?>
		<li><a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>"><?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></li>
		<?php endwhile; ?>
		</ul>
		<?php echo $after_widget; ?>
<?php
		// Reset the global $the_post as this query will have stomped on it
		wp_reset_postdata();

		endif;

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_recent_sermons', $cache, 'widget');
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number'] = (int) $new_instance['number'];
		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['widget_recent_entries']) )
			delete_option('widget_recent_entries');

		return $instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_recent_sermons', 'widget');
	}

	function form( $instance ) {
		$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
		$number = isset($instance['number']) ? absint($instance['number']) : 5;
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>

		<p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of sermons to show:'); ?></label>
		<input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>
<?php
	}
}
add_action( 'widgets_init', create_function('', 'return register_widget("WP4C_Recent_Sermons");') );
?>