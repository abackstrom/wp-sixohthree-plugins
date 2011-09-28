<?php

namespace Sixohthree\Widgets;

class Recents extends \WP_Widget {
	public function __construct() {
		parent::__construct(false, '(603) Recents');
	}//end __construct

	public function widget( $args, $instance ) {
		echo '<h1 class="title">Of Late</h1>';
		echo '<p>A smattering of recent posts.</p>';

		query_posts( 'posts_per_page=5' );
		if( have_posts() ) {
			while( have_posts() ) {
				the_post();
				?>

				<div <?php post_class(); ?>>
				<?php if( has_post_format( 'aside' ) ): ?>
					<?php the_content(); ?>
				<?php else: ?>
					<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
					<span class="date"><?php echo get_the_date('j F Y.'); ?></span>
					<?php the_excerpt(); ?>
				<?php endif; ?>
				</div>

				<?php
			}
		}
		wp_reset_query();

		echo '<p><a href="/archives">And older things</a>.</p>';
	}//end widget
}//end class \Sixohthree\Widgets\Recents;
