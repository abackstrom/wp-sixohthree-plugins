<?php
/*
Plugin Name: Dotdotdot Mod
Plugin URI: http://wiki.bwerp.net/Dotdotdot_Mod
Description: Several modifications for Dotdotdot.
Author: Adam Backstrom
Version: 0.1
Author URI: http://blogs.bwerp.net/
*/ 

$posts_per_archive_page = -1; // archives just show permalinks.
                              // make sure none are left out.

function meta_robots($index=true,$follow=true) {
    $meta = "\n\t<meta name=\"robots\" content=\"";

    $meta .= $index ? "index" : "noindex";
    $meta .= ",";
    $meta .= $follow ? "follow" : "nofollow";

    $meta .= "\" />\n";

    return $meta;
}

add_action("wp_head", "template_robots");
function template_robots() {
    global $paged;

    if(is_archive() || is_search() || (is_home() && intval($paged) > 1))
        $meta = meta_robots(false, true);
    else
        $meta = meta_robots(true, true);

    echo $meta;
}

add_filter('the_excerpt', 'excerpt_read_more', 6);
function excerpt_read_more($content='') {
    return $content . ' <span class="more"><a href="' . get_permalink() . '" title="Read the full post">#</a></span>';
}

function get_recent_comments($count) {
    global $wpdb;
    $wpdb->query("SELECT * FROM comments ORDER BY comment_date LIMIT 5");
}

// called from wp-includes/template-functions-general.php:
//   get_archives()
function spanify_post_count($text) {
    //return '<span class="pchide">'.$text.'</span>';
    return $text;
}

function wp_tagline() {
    return '&mdash; '. get_bloginfo('description');
}

add_action('init', 'ddd_widget_init');
function ddd_widget_init() {
    if( !function_exists('register_sidebar_widget') )
        return;

    function widget_monthlyshort($args) {
        extract($args);
        echo $before_widget;
        echo $before_title . 'Calendar' . $after_title;
        wp_get_archives('type=monthlyshort&format=custom');
        echo $after_widget;
    }
    wp_register_sidebar_widget('Compact Calendar', 'ddd-monthlyshort', 'widget_monthlyshort');

    function widget_timerss($args) {
        extract($args);

        echo $before_widget;
        echo $before_title . 'Time @ Bwerp' . $after_title;

        include_once(ABSPATH . WPINC . '/rss.php');
        $rss = fetch_rss('http://time.bwerp.net/feed/');
        $maxitems = 5;
        $items = array_slice($rss->items, 0, $maxitems);

        ?>

        <ul>
        <?php if (empty($items)) echo '<li>No items</li>';
        else
        foreach ( $items as $item ) :
        $item['display_date'] = get_time_since($item['pubdate']);
        ?>
        <li><?php echo trim($item['description']); ?><br/><a href="<?php echo $item['link']; ?>"><?php echo $item['display_date'] ?> ago</a></li>
        <?php endforeach; ?>
        </ul>
        <p><a href="http://time.bwerp.net/">More&hellip;</a></p>
        <?php
        echo $after_widget;
    }
    wp_register_sidebar_widget('Time RSS', 'ddd-time-rss', 'widget_timerss');
}
