<?php global $post; ?>
<li <?php listings_restaurants_restaurant_listing_class(); ?> data-longitude="<?php echo esc_attr( $post->geolocation_lat ); ?>" data-latitude="<?php echo esc_attr( $post->geolocation_long ); ?>">
	<a href="<?php listings_restaurants_the_restaurant_permalink(); ?>">
		<?php listings_restaurants_the_company_logo(); ?>
		<div class="position">
			<h3><?php the_title(); ?></h3>
			<div class="company">
				<?php listings_restaurants_the_company_name( '<strong>', '</strong> ' ); ?>
				<?php listings_restaurants_the_company_tagline( '<span class="tagline">', '</span>' ); ?>
			</div>
		</div>
		<div class="location">
			<?php listings_restaurants_the_restaurant_location( false ); ?>
		</div>
		<ul class="meta">
			<?php do_action( 'restaurant_listing_meta_start' ); ?>

			<li class="job-type <?php echo listings_restaurants_the_restaurant_type() ? sanitize_title( listings_restaurants_the_restaurant_type()->slug ) : ''; ?>"><?php listings_restaurants_the_restaurant_type(); ?></li>
			<li class="date"><date><?php printf( __( '%s ago', 'restaurants-listings' ), human_time_diff( get_post_time( 'U' ), current_time( 'timestamp' ) ) ); ?></date></li>

			<?php do_action( 'restaurant_listing_meta_end' ); ?>
		</ul>
	</a>
</li>