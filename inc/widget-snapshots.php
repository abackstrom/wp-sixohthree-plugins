<?php

namespace Sixohthree\Widgets;

class Snapshots extends \WP_Widget {
    public function __construct() {
        parent::__construct( false, '(603) Masthead' );
    }

    public function widget( $args, $instance ) {
        if( is_home() ) {
            $header_images = array();

            $header_images[] = array_pop(get_posts('post_type=header-image&orderby=rand&numberposts=1&location=left'));
            $header_images[] = array_pop(get_posts('post_type=header-image&orderby=rand&numberposts=1&location=right&exclude=' . $header_images[0]->ID));

            foreach( $header_images as $idx => $post ) {
                setup_postdata($post);
                the_post_thumbnail('post-thumbnail', array('class' => 'snapshot snapshot' . ($idx+1) ) );
            }
        }

        ?>
        <h1><a href="<?php echo get_settings('home'); ?>">sixohthree.com</a></h1>
        <?php
    }

    public static function register() {
        return function() {
            register_widget( __CLASS__ );
        };
    }//end register
}//end class Sixohthree_Widgets_Masthead
add_action( 'widgets_init', Snapshots::register() );

class Snapshots_Taxonomies {
    public function __construct() {
    }

    /**
     * Custom column output when admin is view the header-image post list.
     */
    function custom_column( $column_name ) {
        global $post;

        if( $column_name == 'header-image' ) {
            echo "<a href='", get_edit_post_link( $post->ID ), "'>", get_the_post_thumbnail( $post->ID ) ?: '(none)', "</a>";
        } elseif( $column_name == 'location' ) {
            $terms = get_the_terms( $post->ID, 'location' );
            $out = array();
            foreach( $terms as $term ) {
                $out[] = $term->name;
            }
            echo join( ', ', $out);
        }
    }//end custom_column

    /**
     * Enable thumbnail ("featured image") support in the theme, and set the thumbnail size.
     */
    function after_setup() {
        add_theme_support( 'post-thumbnails' );
        set_post_thumbnail_size(150, 100, true);
    }//end after_setup

    public function filters() {
        add_action( 'init', array( $this, 'init' ) );
        add_filter( 'manage_header-image_posts_columns', array( $this, 'posts_columns' ) );
        add_action( 'manage_posts_custom_column', array( $this, 'custom_column' ) );
        add_action( 'add_meta_boxes_header-image', array( $this, 'metaboxes' ) );
        add_action( 'after_setup_theme', array( $this, 'after_setup' ) );
    }//end filters

    public function init() {
        $labels = array(
            'name' => 'Header Images',
            'singular_name' => 'Header Image',
            'add_new_item' => 'Add Header Image',
            'edit_item' => 'Edit Header Image',
            'new_item' => 'New Header Image',
            'view_item' => 'View Header Image',
            'search_items' => 'Search Header Images',
            'not_found' => 'No Header Images found',
            'not_found_in_trash' => 'No Header Images found in Trash'
        );

        $args = array(
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'supports' => array('thumbnail', 'location')
        );
        register_post_type( 'header-image', $args );

        $args = array(
            'label' => 'Locations',
            'rewrite' => false
        );
        register_taxonomy( 'location', 'header-image', $args );
    }//end init

    /**
     * Make the "Featured Image" metabox front and center when editing a header-image post.
     */
    function metaboxes( $post ) {
        global $wp_meta_boxes;

        remove_meta_box('postimagediv', 'header-image', 'side');
        add_meta_box('postimagediv', __('Featured Image'), 'post_thumbnail_meta_box', 'header-image', 'normal', 'high');
    }//end metaboxes

    /**
     * Modify which columns display when the admin views a list of header-image posts.
     */
    function posts_columns( $posts_columns ) {
        $tmp = array();

        foreach( $posts_columns as $key => $value ) {
            if( $key == 'title' ) {
                $tmp['header-image'] = 'Header Image';
                $tmp['location'] = 'Location';
            } else {
                $tmp[$key] = $value;
            }
        }

        return $tmp;
    }//end posts_columns
}//end class Sixohthree_Widgets_Snapshots_Taxonomies

