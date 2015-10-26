<?php
/**
 * Plugin Name: Post Primary Tag
 * Description: Allow Primary Tag Per Post
 * Version:     0.1
 * Author:      Hampton Paulk
 * Author URI:  http://hamptonpaulk.com
 * @package PostPrimaryTag
 * License:
 */

if ( ! defined( 'ABSPATH' ) ) {
	echo 'Direct Access Not Allowed';
	exit;
}

/**
 * The base plugin class used for all hooks
 */
require( 'classes/class-post-primary-tag.php' );

/**
 * Return an instance of the class through the factory
 */
function run_post_primary_tag() {
	return Post_Primary_Tag::factory();
}
add_action( 'plugins_loaded', 'run_post_primary_tag' );
