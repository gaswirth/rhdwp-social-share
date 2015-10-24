<?php
/**
 * Plugin Name: PinIt Button
 * Description: Adds a Pinterest "Pin It" button when hovering over images inside posts.
 * Author: Roundhouse Designs
 * Author URI: http://roundhouse-designs.com
 * Version: 1.1
**/

define('RHD_PINIT_DIR', plugin_dir_url(__FILE__));


// Adapted from uxde.net's example
/**
 * rhd_pinit_enqueue_styles function.
 * Enqueue stylesheets
 *
 * @access public
 * @return void
 */
function rhd_pinit_enqueue_styles() {
	wp_register_style( 'rhd-pinit', RHD_PINIT_DIR . 'rhd-pinit.css', array(), '1.1', 'all' );

	wp_enqueue_style( 'rhd-pinit' );
}
add_action( 'wp_enqueue_scripts', 'rhd_pinit_enqueue_styles' );


/**
 * rhd_pinit function.
 *
 * @access public
 * @param mixed $content
 * @return void
 */
function rhd_pinit( $content ) {
	global $post;
	$posturl = urlencode( get_permalink() ); //Get the post URL
	$pattern = '/<img(.*?)src="(.*?).(bmp|gif|jpg|png)"(.*?)>/i';
	preg_match( $pattern, $content, $tag );

	/* Make sure image doesn't have class "no-pin" or "nopin" and process if true */
	if ( $tag ) {
		if ( !stripos($tag[1], "no-pin") && !stripos($tag[1], "nopin") ) {
			$title_attr = preg_match( '/title="(.*?)"/', $tag[1], $titlematch );

			//$desc = preg_replace('/\s/', '%20', $titlematch[1]);

			$pinspan = '<span class="pinterest-button">';
			$pinurl = '<a target="_blank" href="//pinterest.com/pin/create/button/?url=' . $posturl . '&media=';
			$pindescription = '&description=' . get_the_title() . '%20|%20' . get_bloginfo('name');
			$pinfinish = '" class="pin-it"></a>';
			$pinend = '</span>';
			$replacement = $pinspan . $pinurl . '$2.$3' . $pindescription . $pinfinish . '<img$1src="$2.$3" $4 />' . $pinend;
			$content = preg_replace( $pattern, $replacement, $content );

			//Fix the link problem
			$newpattern = '/<a(.*?)><span class="pinterest-button"><a(.*?)><\/a><img(.*?)\/><\/span><\/a>/i';
			$replacement = '<span class="pinterest-button"><a$2></a><a$1><img$3\/></a></span>';
			$content = preg_replace( $newpattern, $replacement, $content );
		}
	}

	return $content;
}
add_filter( 'the_content', 'rhd_pinit' );
