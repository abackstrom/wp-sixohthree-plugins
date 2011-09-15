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

	$js_f = 'http://gist.github.com/%d.js';
	$js_file_f = 'http://gist.github.com/%d.js?file=%s';

	$txt_f = 'http://gist.github.com/raw/%d';
	$txt_file_f = 'http://gist.github.com/raw/%d/%s';

	if( $atts['file'] ) {
		$js_url = sprintf($js_file_f, $id, urlencode($atts['file']));
		$txt_url = sprintf($txt_file_f, $id, urlencode($atts['file']));
	} else {
		$js_url = sprintf($js_f, $id);
		$txt_url = sprintf($txt_f, $id);
	}

	$key = sprintf("gist:%d:%s", $id, $atts['file']);

	if( false && $transient = get_transient($key) ) {
		$body = $transient;
	} else {
		$remote = wp_remote_get($txt_url);

		if( is_wp_error($remote) ) {
			$body = $remote->get_error_message();
		} elseif( $remote['response']['code'] == 200 ) {
			$body = $remote['body'];
			set_transient($key, $body, 900); // 15 min cache
		} else {
			$body = "Unable to fetch this gist.";
		}
	}

	//
	// echo the result
	//

	$url_js = esc_attr($url_js);
	$body = htmlentities($body);

	$return = <<<EOF
	<script src="{$js_url}"></script>
	<noscript><pre>{$body}</pre></noscript>
EOF;

	return $return;
}
add_shortcode('gist', 'sixohthree_gist');
