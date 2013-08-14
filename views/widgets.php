<?php

# widget for newest movies
class WPMovieLibraryWidgetNewests extends WP_Widget {

	public function __construct() {
		// widget actual processes
		parent::__construct('tmc_newest', __('The Newest Movies'), array('description' => __('The most recent movies.')));
	}

	public function form($instance) {
		if ( isset($instance['title']) ) {
			$title = $instance['title'];
			$number_of_movies = $instance['number_of_movies'];
		}
		else {
			$title = __('The Newest Movies');
			$number_of_movies = 5;
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>:</label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('number_of_movies'); ?>"><?php _e('Number of movies'); ?>:</label> 
		<input id="<?php echo $this->get_field_id('number_of_movies'); ?>" name="<?php echo $this->get_field_name('number_of_movies'); ?>" type="number" value="<?php echo esc_attr($number_of_movies); ?>" style="width: 60px; margin-left: 2px;" />
		</p>
		<?php 
	}

	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number_of_movies'] = strip_tags($new_instance['number_of_movies']);

		return $instance;
	}

	public function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);

		# get newest movie posts
		$args = array(
			'numberposts' => $instance['number_of_movies'],
			'post_type' => 'movie',
			'post_status' => 'publish',
			'orderby' => 'post_date'
		);

		$newest_movies = get_posts($args);

		echo $before_widget;
		if ( empty($title) ) {
			echo $before_title . __("The Newest Movies") . $after_title;
		} else {
			echo $before_title . $title . $after_title;
		} ?>

		<?php /* slider starts here */ ?>
		<div id="rslidesWrapper">
			<ul id="tmc_slider_newest" class="rslides">
				<?php foreach ( $newest_movies as $movie ) { ?>
				<li>
					<a href="<?php echo get_permalink($movie->ID); ?>">
						<?php echo get_post_meta($movie->ID, 'html_poster', true); ?>
						<?php echo get_post_meta($movie->ID, 'title', true); ?>
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>
		<script type="text/javascript">
		jQuery(function() {
		    jQuery("#tmc_slider_newest").responsiveSlides({
		    	nav: true,
		    	pause: true,
		    	timeout: 10000,
		    	prevText: "&larr;",
		    	nextText: "&rarr;"
		    });
		});
		</script>
		<?php /* slider ends here */ ?>

		<?php
		echo $after_widget;
	}
}

# widget for movies with highest ratings
class WPMovieLibraryWidgetBests extends WP_Widget {

	public function __construct() {
		// widget actual processes
		parent::__construct('tmc_best', __('The Best Movies'), array('description' => __('The movies with highest ratings.')));
	}

	public function form($instance) {
		if ( isset($instance['title']) ) {
			$title = $instance['title'];
			$number_of_movies = $instance['number_of_movies'];
		}
		else {
			$title = __('The Best Movies');
			$number_of_movies = 5;
		}
		?>
		<p>
		<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>:</label> 
		<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id('number_of_movies'); ?>"><?php _e('Number of movies'); ?>:</label> 
		<input id="<?php echo $this->get_field_id('number_of_movies'); ?>" name="<?php echo $this->get_field_name('number_of_movies'); ?>" type="number" value="<?php echo esc_attr($number_of_movies); ?>" style="width: 60px; margin-left: 2px;" />
		</p>
		<?php 
	}

	public function update($new_instance, $old_instance) {
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['number_of_movies'] = strip_tags($new_instance['number_of_movies']);

		return $instance;
	}

	public function widget($args, $instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);

		# get newest movie posts
		$args = array(
			'numberposts' => $instance['number_of_movies'],
			'post_type' => 'movie',
			'post_status' => 'publish',
			'meta_key' => 'rating',
			'orderby' => 'meta_value'
		);

		$newest_movies = get_posts($args);

		echo $before_widget;
		if ( empty($title) ) {
			echo $before_title . __("The Best Movies") . $after_title;
		} else {
			echo $before_title . $title . $after_title;
		} ?>

		<?php /* slider starts here */ ?>
		<div id="rslidesWrapper">
			<ul id="tmc_slider_best" class="rslides">
				<?php foreach ( $newest_movies as $movie ) { ?>
				<li>
					<a href="<?php echo get_permalink($movie->ID); ?>">
						<?php echo get_post_meta($movie->ID, 'html_poster', true); ?>
						<?php echo get_post_meta($movie->ID, 'title', true); ?>
					</a>
				</li>
				<?php } ?>
			</ul>
		</div>
		<script type="text/javascript">
		jQuery(function() {
		    jQuery("#tmc_slider_best").responsiveSlides({
		    	nav: true,
		    	pause: true,
		    	timeout: 10000,
		    	prevText: "&larr;",
		    	nextText: "&rarr;"
		    });
		});
		</script>
		<?php /* slider ends here */ ?>

		<?php
		echo $after_widget;
	}
}

?>