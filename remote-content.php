<?php
/*
Plugin Name: Gist
Plugin URI: http://
Description: 
Version: 1.0
Author: Adam Backstrom
Author URI: http://sixohthree.com/
License: GPL2
*/

function sixohthree_gist($atts = array(), $id = null) {
	$atts = shortcode_atts(array('file' => ''), $atts);

	ksort($atts);

	$key = sprintf("gist:%s:%s", $id, serialize($atts));

	if( $transient = get_transient($key) ) {
		return $transient;
	}

	$js_f = 'https://gist.github.com/%s.js';
	$js_file_f = 'https://gist.github.com/%d.js?file=%s';

	$txt_f = 'https://gist.github.com/raw/%s';
	$txt_file_f = 'https://gist.github.com/raw/%s/%s';

	if( $atts['file'] ) {
		$js_url = sprintf($js_file_f, $id, urlencode($atts['file']));
		$txt_url = sprintf($txt_file_f, $id, urlencode($atts['file']));
	} else {
		$js_url = sprintf($js_f, $id);
		$txt_url = sprintf($txt_f, $id);
	}

	$remote = wp_remote_get($txt_url);

	if( is_wp_error($remote) ) {
		$body = $remote->get_error_message();
	} elseif( $remote['response']['code'] == 200 ) {
		$body = $remote['body'];
	} else {
		$body = "Unable to fetch this gist.";
	}

	//
	// echo the result
	//

	$js_url = esc_attr($js_url);
	$body = htmlentities($body);

	$return = "<script src='{$js_url}'></script><noscript><pre>{$body}</pre></noscript>";

	set_transient($key, $return, 900); // 15 min cache

	return $return;
}
add_shortcode('gist', 'sixohthree_gist');
