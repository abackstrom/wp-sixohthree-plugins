<?php

namespace Sixohthree\Posttypes;

class Code {
	public function init() {
		$args = array(
			'label' => 'Code',
			'show_ui' => true,
			'show_in_menu' => true,
			'supports' => array('title', 'editor', 'revisions'),
		);

		register_post_type( 'sotcode', $args );
	}

	public function flush_rewrite_rules() {
		$this->init();

		flush_rewrite_rules();
	}

	public function user_can_richedit( $value ) {
		global $post_type;

		if( $post_type == 'sotcode' ) {
			return false;
		}

		return $value;
	}

	public function shortcode( $atts ) {
		extract( shortcode_atts( array(
			'name' => null,
			'id' => null,
		), $atts ) );

		$args = array(
			'post_type' => 'sotcode',
			'posts_per_page' => 1,
		);

		if( isset( $atts['id'] ) && $atts['id'] ) {
			$post = get_post( $atts['id'] );
		} else {
			$post = get_page_by_title( $atts['name'], OBJECT, 'sotcode' );
		}

		if( $post ) {
			return '<pre>' . htmlentities( $post->post_content ) . '</pre>';
		}

		return '(Code sample not found.)';
	}

	public function hooks() {
		add_action( 'init', array($this, 'init') );
		add_filter( 'user_can_richedit', array($this, 'user_can_richedit') );
		add_shortcode( 'code', array($this, 'shortcode') );

		register_activation_hook( SOT_PLUGIN, array($this, 'flush_rewrite_rules') );
	}
}
