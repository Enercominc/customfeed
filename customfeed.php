<?php
/**
 * Plugin Name:       Enercom Custom RSS Feed
 * Plugin URI:        https://github.com/Enercominc/customfeed
 * Repo URI:          git@github.com:Enercominc/customfeed.git
 * Description:       Handle the additional code needed to offer custom RSS Feeds
 * Version:           2.2.1
 * Requires at least: 5.5
 * Requires PHP:      7.2
 * Author:            Bradford Knowlton
 * Author URI:        https://bradknowlton.com/
 * License:           BSD 3-clause
 * License URI:       https://opensource.org/licenses/BSD-3-Clause
 * Text Domain:       enercom
 * Domain Path:       /languages
 */
 
 
if ( !defined('CUSTOMFEED_PATH') ) {
    define( 'CUSTOMFEED_PATH', plugin_dir_path( __FILE__ ) );
}


if ( !defined('CUSTOMFEED_URL') ) {
    define( 'CUSTOMFEED_URL', plugin_dir_url( __FILE__ ) );
}

add_action('init', 'customRSS');

function customRSS(){
    add_feed('closing-bell-story-feed', 'customRSSFunc');
    add_action( 'pre_get_posts', 'feed_content' );
}

add_filter('pre_option_posts_per_rss', function() { return 15;});


/**
 * Customizes the query.
 * It will bail if $query is not an object, filters are suppressed and it's not
 * our feed query.
 *
 * @param  WP_Query $query The current query
 */
function feed_content( $query ) {
	// Bail if $query is not an object or of incorrect class
	if ( ! $query instanceof WP_Query )
		return;

	// Bail if filters are suppressed on this query
	if ( $query->get( 'suppress_filters' ) )
		return;

	// Bail if it's not our feed
	if ( ! $query->is_feed( 'closing-bell-story-feed' ) )
		return;

	// Change the feed content
	// Example: A feed for pages
	// $query->set( 'post_type', array( 'page' ) );
	
	$tax_query = array(
	    'relation' => 'AND',
	    array(
	        'taxonomy' => 'category',
	        'field' => 'id',
	        'terms' => '2750'
	    )
	);
	$query->set( 'tax_query', $tax_query );
	
	$query->set( 'posts_per_page', 15 );

	$query->set( 'date_query', array(
            array(
                'after' => '1 days ago'
            )
        ) );
}

function customRSSFunc(){
	header( 'Content-Type: application/rss+xml' );
    require_once(CUSTOMFEED_PATH.'/feed-rss2.php');
}

function mytheme_custom_excerpt_length( $length ) {
    return 40;
}
add_filter( 'excerpt_length', 'mytheme_custom_excerpt_length', 999 );