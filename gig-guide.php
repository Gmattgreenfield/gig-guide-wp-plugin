<?php
/*
Plugin Name: Gig Guide
Plugin URI: https://github.com/Gmattgreenfield/
Description:
Version: 0.0.1
Author: Matt Greenfield
Author URI: mattgreenfield.co.uk
License:
*/




//
// CREATE PAGE IN SETTINGS PANEL
//

// This function create an settings page on the wordpress side menu settings > Gig Guide Settings
// function gig_guide(){
// 	add_options_page (
// 		'Gig Guide Plugin',
// 		'Gig Guide Settings',
// 		'manage_options',
// 		'gig_guide',
// 		'gig_guide_options_page'
// 	);
// }

// // Run the above function on the admin_menu hook
// add_action( 'admin_menu', 'gig_guide');

// // Populate the settings page
// function gig_guide_options_page () {
// 	// Check the user has permissions
// 	if( !current_user_can( 'manage_options')) {
// 		wp_die( 'you do not have sufficient permission to access this settings page');
// 	}
// 	// Output content
// 	echo 'Welcome to the plugin page. One day this will be full of settings! Not for now.';
// }





// CREATE BACKEND CUSTOM POST TYPE AND TAXONOMIES


// Create Custom post type - Products
function custom_post_gigs() {
  $labels = array(
	'name'               => _x( 'Gigs', 'post type general name' ),
	'singular_name'      => _x( 'gig', 'post type singular name' ),
	'add_new'            => _x( 'Add New', 'book' ),
	'add_new_item'       => __( 'Add New Gig' ),
	'edit_item'          => __( 'Edit Gig' ),
	'new_item'           => __( 'New Gig' ),
	'all_items'          => __( 'All Gigs' ),
	'view_item'          => __( 'View Gig' ),
	'search_items'       => __( 'Search Gigs' ),
	'not_found'          => __( 'No Gigs found' ),
	'not_found_in_trash' => __( 'No Gigs found in the Trash' ),
	'parent_item_colon'  => '',
	'menu_name'          => 'Gigs'
  );
  $args = array(
	'labels'        => $labels,
	'description'   => 'Gigs for the gigs page',
	'public'        => true,
	'menu_position' => 5,
	'menu_icon' 	=> 'dashicons-megaphone',
	'supports'      => array( 'title' ), // We dont want support for comments, content editor, thumbnails, authors - its all in the meta boxes
	'has_archive'   => true,
  );
  register_post_type( 'gigs', $args );
}
add_action( 'init', 'custom_post_gigs' );





//
// SHORTCODE - [gig-guide]
// the output is stored in assets/gig_guide_markup.php

// The content to output when the code is used.
function outputhtml_function() {

	// Credit to @AidanThreadgold
	// See https://github.com/Gmattgreenfield/Filtered-Catalogue-WP-Plugin/issues/2
	ob_start();
	include 'assets/gig-guide-output.php';
	$return_string = ob_get_clean();

	return $return_string;
}

// Register the output to a shortcode
function register_shortcodes(){
   add_shortcode('gig-guide', 'outputhtml_function');
}

// Add the shortcodes to the add_action hook
add_action( 'init', 'register_shortcodes');






//
// LOAD CSS AND JS
//

// Attach the CSS and JS files to the page where the plugin is being used.
function gig_guide_includes () {
	wp_enqueue_style( 'gig_guide_frontend_css', plugins_url( '/gig-guide/assets/css/gig_guide_styles.min.css' ));

}
add_action('wp_enqueue_scripts','gig_guide_includes');







//
// META BOX
//



// DATE

// Visual appearance of the box
add_action( 'add_meta_boxes', 'gig_date_box' );
function gig_date_box() {
	// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
	add_meta_box(
		'gig_date_box',
		__( 'Date', 'myplugin_textdomain' ),
		'gig_date_box_content',
		'gigs',
		'normal',
		'core'
	);
}


// The content of the box
function gig_date_box_content( $post ) {
  // Add an nonce field so we can check for it later.
  wp_nonce_field( plugin_basename( __FILE__ ), 'gig_date_box_content_nonce' );

  // Use get_post_meta() to retrieve an existing value
  $value = get_post_meta( $post->ID, 'gig_date', true );

  echo '<label for="gig_date"></label>';
  echo '<input type="date" id="gig_date" name="gig_date" value="' . esc_attr( $value ) . '"/>';
}

// What happens to the data input to the box
add_action( 'save_post', 'gig_date_box_save' );
function gig_date_box_save( $post_id ) {

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
  return;

  if ( !wp_verify_nonce( $_POST['gig_date_box_content_nonce'], plugin_basename( __FILE__ ) ) )
  return;

  if ( 'page' == $_POST['post_type'] ) {
	if ( !current_user_can( 'edit_page', $post_id ) )
	return;
  } else {
	if ( !current_user_can( 'edit_post', $post_id ) )
	return;
  }
  $gig_date = $_POST['gig_date'];
  update_post_meta( $post_id, 'gig_date', $gig_date );
}




// TIME

// Visual appearance of the box
add_action( 'add_meta_boxes', 'gig_time_box' );
function gig_time_box() {
	// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
	add_meta_box(
		'gig_time_box',
		__( 'Time', 'myplugin_textdomain' ),
		'gig_time_box_content',
		'gigs',
		'normal',
		'core'
	);
}


// The content of the box
function gig_time_box_content( $post ) {
  // Add an nonce field so we can check for it later.
  wp_nonce_field( plugin_basename( __FILE__ ), 'gig_time_box_content_nonce' );

  // Use get_post_meta() to retrieve an existing value
  $value = get_post_meta( $post->ID, 'gig_time', true );

  echo '<label for="gig_time--from">From</label>';
  echo '<input type="time" id="gig_time--from" name="gig_time" value="' . esc_attr( $value ) . '" />';
}

// What happens to the data input to the box
add_action( 'save_post', 'gig_time_box_save' );
function gig_time_box_save( $post_id ) {

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
  return;

  if ( !wp_verify_nonce( $_POST['gig_time_box_content_nonce'], plugin_basename( __FILE__ ) ) )
  return;

  if ( 'page' == $_POST['post_type'] ) {
	if ( !current_user_can( 'edit_page', $post_id ) )
	return;
  } else {
	if ( !current_user_can( 'edit_post', $post_id ) )
	return;
  }
  $gig_time = $_POST['gig_time'];
  update_post_meta( $post_id, 'gig_time', $gig_time );
}









// TOWN

// Visual appearance of the box
add_action( 'add_meta_boxes', 'gig_town_box' );
function gig_town_box() {
	// add_meta_box( $id, $title, $callback, $post_type, $context, $priority, $callback_args );
	add_meta_box(
		'gig_town_box',
		__( 'Town', 'myplugin_textdomain' ),
		'gig_town_box_content',
		'gigs',
		'normal',
		'high'
	);
}


// The content of the box
function gig_town_box_content( $post ) {
  // Add an nonce field so we can check for it later.
  wp_nonce_field( plugin_basename( __FILE__ ), 'gig_town_box_content_nonce' );

  // Use get_post_meta() to retrieve an existing value
  $value = get_post_meta( $post->ID, 'gig_town', true );

  echo '<label for="gig_town"></label>';
  echo '<input type="text" id="gig_town" name="gig_town" placeholder="Type the town" value="' . esc_attr( $value ) . '" size="25"/>';
}

// What happens to the data input to the box
add_action( 'save_post', 'gig_town_box_save' );
function gig_town_box_save( $post_id ) {

  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
  return;

  if ( !wp_verify_nonce( $_POST['gig_town_box_content_nonce'], plugin_basename( __FILE__ ) ) )
  return;

  if ( 'page' == $_POST['post_type'] ) {
	if ( !current_user_can( 'edit_page', $post_id ) )
	return;
  } else {
	if ( !current_user_can( 'edit_post', $post_id ) )
	return;
  }
  $gig_town = $_POST['gig_town'];
  update_post_meta( $post_id, 'gig_town', $gig_town );
}

