<?php
/*
Plugin Name: Sixohthree Mods
Plugin URI: http://
Description: 
Version: 1.0
Author: Adam Backstrom
Author URI: http://sixohthree.com/
License: GPL2
*/

include __DIR__ . '/inc/class-sixohthree-container.php';
include __DIR__ . '/inc/widget-snapshots.php';
include __DIR__ . '/inc/widget-recents.php';
include __DIR__ . '/inc/posttype-code.php';

define( 'SOT_PLUGIN', __FILE__ );

global $sixohthree;
$sixohthree = new \Sixohthree\Container;

$sixohthree->taxonomies->snapshots = new \Sixohthree\Widgets\Snapshots_Taxonomies;
$sixohthree->taxonomies->snapshots->filters();

$sixohthree->posttypes->code = new \Sixohthree\Posttypes\Code;
$sixohthree->posttypes->code->hooks();

// function sixohthree_widgets_init() {
// 	/*
// 	register_sidebar(array(
// 		'name' => 'Home Page Left',
// 		'before_widget' => '<div id="%1$s" class="widget %2$s">',
// 		'after_widget' => '</div>',
// 		'before_title' => '<h2>',
// 		'after_title' => '</h2>',
// 	));
// 
// 	register_sidebar(array(
// 		'name' => 'Home Page Right',
// 		'before_widget' => '<div id="%1$s" class="widget %2$s">',
// 		'after_widget' => '</div>',
// 		'before_title' => '<h2>',
// 		'after_title' => '</h2>',
// 	));
// 	*/
// 
// 	register_widget( '\Sixohthree\Widgets\Recents' );
// }
// add_action( 'widgets_init', 'sixohthree_widgets_init' );

function sixohthree_readability() {
	echo <<<EOF
<div class="rdbWrapper" data-show-read="1" data-show-send-to-kindle="0" data-show-print="1" data-show-email="1" data-orientation="0" data-version="1" data-bg-color="transparent"></div><script type="text/javascript">(function() {var s = document.getElementsByTagName("script")[0],rdb = document.createElement("script"); rdb.type = "text/javascript"; rdb.async = true; rdb.src = document.location.protocol + "//www.readability.com/embed.js"; s.parentNode.insertBefore(rdb, s); })();</script>
EOF;
}
