<?php

namespace Listings\Restaurants\Widgets;

use Listings\Widgets\Widget;

class RecentRestaurants extends Widget {

    /**
     * Constructor
     */
    public function __construct() {
        global $wp_post_types;

        $this->widget_cssclass    = 'listings_restaurants widget_recent_restaurants';
        $this->widget_description = __( 'Display a list of recent listings on your site, optionally matching a keyword and location.', 'restaurants-listings' );
        $this->widget_id          = 'widget_recent_restaurants';
        $this->widget_name        = sprintf( __( 'Recent %s', 'restaurants-listings' ), $wp_post_types['restaurant_listing']->labels->name );
        $this->settings           = array(
            'title' => array(
                'type'  => 'text',
                'std'   => sprintf( __( 'Recent %s', 'restaurants-listings' ), $wp_post_types['restaurant_listing']->labels->name ),
                'label' => __( 'Title', 'restaurants-listings' )
            ),
            'keyword' => array(
                'type'  => 'text',
                'std'   => '',
                'label' => __( 'Keyword', 'restaurants-listings' )
            ),
            'location' => array(
                'type'  => 'text',
                'std'   => '',
                'label' => __( 'Location', 'restaurants-listings' )
            ),
            'number' => array(
                'type'  => 'number',
                'step'  => 1,
                'min'   => 1,
                'max'   => '',
                'std'   => 10,
                'label' => __( 'Number of listings to show', 'restaurants-listings' )
            )
        );
        $this->register();
    }

    /**
     * widget function.
     *
     * @see WP_Widget
     * @access public
     * @param array $args
     * @param array $instance
     * @return void
     */
    public function widget( $args, $instance ) {
        if ( $this->get_cached_widget( $args ) ) {
            return;
        }

        ob_start();

        extract( $args );

        $title  = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
        $number = absint( $instance['number'] );
        $restaurants   = listings_restaurants_get_listings( array(
            'search_location'   => isset( $instance['location'] ) ? $instance['location'] : '',
            'search_keywords'   => isset( $instance['keyword'] ) ? $instance['keyword'] : '',
            'posts_per_page'    => $number,
            'orderby'           => 'date',
            'order'             => 'DESC',
        ) );

        if ( $restaurants->have_posts() ) : ?>

            <?php echo $before_widget; ?>

            <?php if ( $title ) echo $before_title . $title . $after_title; ?>

            <ul class="restaurant_listings">

                <?php while ( $restaurants->have_posts() ) : $restaurants->the_post(); ?>

                    <?php listings_get_template_part( 'content-widget', 'restaurant_listing' ); ?>

                <?php endwhile; ?>

            </ul>

            <?php echo $after_widget; ?>

        <?php else : ?>

            <?php listings_get_template_part( 'content-widget', 'no-restaurants-found' ); ?>

        <?php endif;

        wp_reset_postdata();

        $content = ob_get_clean();

        echo $content;

        $this->cache_widget( $args, $content );
    }
}