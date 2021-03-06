<?php

namespace Listings\Restaurants\Admin;

class Setup {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 12 );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'redirect' ) );
	}

	/**
	 * admin_menu function.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_menu() {
		add_dashboard_page( __( 'Setup', 'restaurants-listings' ), __( 'Setup', 'restaurants-listings' ), 'manage_options', 'listings-restaurants-setup', array( $this, 'output' ) );
	}

	/**
	 * Add styles just for this page, and remove dashboard page links.
	 *
	 * @access public
	 * @return void
	 */
	public function admin_head() {
		remove_submenu_page( 'index.php', 'listings-restaurants-setup' );
	}

	/**
	 * Sends user to the setup page on first activation
	 */
	public function redirect() {
		// Bail if no activation redirect transient is set
	    if ( ! get_transient( '_listings_restaurants_activation_redirect' ) ) {
			return;
	    }

	    if ( ! current_user_can( 'manage_options' ) ) {
	    	return;
	    }

		// Delete the redirect transient
		delete_transient( '_listings_restaurants_activation_redirect' );

		// Bail if activating from network, or bulk, or within an iFrame
		if ( is_network_admin() || isset( $_GET['activate-multi'] ) || defined( 'IFRAME_REQUEST' ) ) {
			return;
		}

		if ( ( isset( $_GET['action'] ) && 'upgrade-plugin' == $_GET['action'] ) && ( isset( $_GET['plugin'] ) && strstr( $_GET['plugin'], 'listings-restaurants.php' ) ) ) {
			return;
		}

		wp_redirect( admin_url( 'index.php?page=listings-restaurants-setup' ) );
		exit;
	}

	/**
	 * Create a page.
	 * @param  string $title
	 * @param  string $content
	 * @param  string $option
	 */
	public function create_page( $title, $content, $option ) {
		$page_data = array(
			'post_status'    => 'publish',
			'post_type'      => 'page',
			'post_author'    => 1,
			'post_name'      => sanitize_title( $title ),
			'post_title'     => $title,
			'post_content'   => $content,
			'post_parent'    => 0,
			'comment_status' => 'closed'
		);
		$page_id = wp_insert_post( $page_data );

		if ( $option ) {
			update_option( $option, $page_id );
		}
	}

	/**
	 * Output addons page
	 */
	public function output() {
		$step = ! empty( $_GET['step'] ) ? absint( $_GET['step'] ) : 1;

		if ( 3 === $step && ! empty( $_POST ) ) {
			$create_pages    = isset( $_POST['listings-restaurants-create-page'] ) ? $_POST['listings-restaurants-create-page'] : array();
			$page_titles     = $_POST['listings-restaurants-page-title'];
			$pages_to_create = array(
				'submit_restaurant_form' => '[submit_restaurant_form]',
				'restaurant_dashboard'   => '[restaurant_dashboard]',
				'restaurants'            => '[restaurants]'
			);

			foreach ( $pages_to_create as $page => $content ) {
				if ( ! isset( $create_pages[ $page ] ) || empty( $page_titles[ $page ] ) ) {
					continue;
				}
				$this->create_page( sanitize_text_field( $page_titles[ $page ] ), $content, 'listings_' . $page . '_page_id' );
			}
		}
		?>
		<div class="wrap listings_restaurants listings_restaurants_addons_wrap">
			<h2><?php _e( 'Listings Restaurants Setup', 'restaurants-listings' ); ?></h2>

			<ul class="listings-restaurants-setup-steps">
				<li class="<?php if ( $step === 1 ) echo 'listings-restaurants-setup-active-step'; ?>"><?php _e( '1. Introduction', 'restaurants-listings' ); ?></li>
				<li class="<?php if ( $step === 2 ) echo 'listings-restaurants-setup-active-step'; ?>"><?php _e( '2. Page Setup', 'restaurants-listings' ); ?></li>
				<li class="<?php if ( $step === 3 ) echo 'listings-restaurants-setup-active-step'; ?>"><?php _e( '3. Done', 'restaurants-listings' ); ?></li>
			</ul>

			<?php if ( 1 === $step ) : ?>

				<h3><?php _e( 'Setup Wizard Introduction', 'restaurants-listings' ); ?></h3>

				<p><?php _e( 'Welcome and thanks for installing <em>Listings Restaurants</em>!', 'restaurants-listings' ); ?></p>
				<p><?php _e( 'This setup wizard will help you get started by creating the pages for restaurant submission, restaurant management, and listing your restaurants.', 'restaurants-listings' ); ?></p>
				<p><?php printf( __( 'You can also skip the wizard and setup the pages and shortcodes yourself manually, the process is still relatively simple as Listings is easy to use. Refer to the %sdocumentation%s for help.', 'restaurants-listings' ), '<a href="https://wprestaurantmanager.com/documentation/">', '</a>' ); ?></p>

				<p class="submit">
					<a href="<?php echo esc_url( add_query_arg( 'step', 2 ) ); ?>" class="button button-primary"><?php _e( 'Continue to page setup', 'restaurants-listings' ); ?></a>
					<a href="<?php echo esc_url( add_query_arg( 'skip-listings-restaurants-setup', 1, admin_url( 'index.php?page=listings-restaurants-setup&step=3' ) ) ); ?>" class="button"><?php _e( 'Skip setup. I will setup the plugin manually', 'restaurants-listings' ); ?></a>
				</p>

			<?php endif; ?>
			<?php if ( 2 === $step ) : ?>

				<h3><?php _e( 'Page Setup', 'restaurants-listings' ); ?></h3>

				<p><?php printf( __( '<em>Listings Restaurants</em> includes %1$sshortcodes%2$s which can be used within your %3$spages%2$s to output content. These can be created for you below. For more information on the restaurant shortcodes view the %4$sshortcode documentation%2$s.', 'restaurants-listings' ), '<a href="http://codex.wordpress.org/Shortcode" title="What is a shortcode?" target="_blank" class="help-page-link">', '</a>', '<a href="http://codex.wordpress.org/Pages" target="_blank" class="help-page-link">', '<a href="https://wprestaurantmanager.com/document/shortcode-reference/" target="_blank" class="help-page-link">' ); ?></p>

				<form action="<?php echo esc_url( add_query_arg( 'step', 3 ) ); ?>" method="post">
					<table class="listings-restaurants-shortcodes widefat">
						<thead>
							<tr>
								<th>&nbsp;</th>
								<th><?php _e( 'Page Title', 'restaurants-listings' ); ?></th>
								<th><?php _e( 'Page Description', 'restaurants-listings' ); ?></th>
								<th><?php _e( 'Content Shortcode', 'restaurants-listings' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td><input type="checkbox" checked="checked" name="listings-restaurants-create-page[submit_restaurant_form]" /></td>
								<td><input type="text" value="<?php echo esc_attr( _x( 'Post a Restaurant', 'Default page title (wizard)', 'restaurants-listings' ) ); ?>" name="listings-restaurants-page-title[submit_restaurant_form]" /></td>
								<td>
									<p><?php _e( 'This page allows employers to post restaurants to your website from the front-end.', 'restaurants-listings' ); ?></p>

									<p><?php _e( 'If you do not want to accept submissions from users in this way (for example you just want to post restaurants from the admin dashboard) you can skip creating this page.', 'restaurants-listings' ); ?></p>
								</td>
								<td><code>[submit_restaurant_form]</code></td>
							</tr>
							<tr>
								<td><input type="checkbox" checked="checked" name="listings-restaurants-create-page[restaurant_dashboard]" /></td>
								<td><input type="text" value="<?php echo esc_attr( _x( 'Restaurant Dashboard', 'Default page title (wizard)', 'restaurants-listings' ) ); ?>" name="listings-restaurants-page-title[restaurant_dashboard]" /></td>
								<td>
									<p><?php _e( 'This page allows employers to manage and edit their own restaurants from the front-end.', 'restaurants-listings' ); ?></p>

									<p><?php _e( 'If you plan on managing all listings from the admin dashboard you can skip creating this page.', 'restaurants-listings' ); ?></p>
								</td>
								<td><code>[restaurant_dashboard]</code></td>
							</tr>
							<tr>
								<td><input type="checkbox" checked="checked" name="listings-restaurants-create-page[restaurants]" /></td>
								<td><input type="text" value="<?php echo esc_attr( _x( 'Restaurants', 'Default page title (wizard)', 'restaurants-listings' ) ); ?>" name="listings-restaurants-page-title[restaurants]" /></td>
								<td><?php _e( 'This page allows users to browse, search, and filter restaurant listings on the front-end of your site.', 'restaurants-listings' ); ?></td>
								<td><code>[restaurants]</code></td>
							</tr>
						</tbody>
						<tfoot>
							<tr>
								<th colspan="4">
									<input type="submit" class="button button-primary" value="Create selected pages" />
									<a href="<?php echo esc_url( add_query_arg( 'step', 3 ) ); ?>" class="button"><?php _e( 'Skip this step', 'restaurants-listings' ); ?></a>
								</th>
							</tr>
						</tfoot>
					</table>
				</form>

			<?php endif; ?>
			<?php if ( 3 === $step ) : ?>

				<h3><?php _e( 'All Done!', 'restaurants-listings' ); ?></h3>

				<p><?php _e( 'Looks like you\'re all set to start using the plugin. In case you\'re wondering where to go next:', 'restaurants-listings' ); ?></p>

				<ul class="listings-restaurants-next-steps">
					<li><a href="<?php echo admin_url( 'admin.php?page=listings-settings' ); ?>"><?php _e( 'Tweak the plugin settings', 'restaurants-listings' ); ?></a></li>
					<li><a href="<?php echo admin_url( 'post-new.php?post_type=listing' ); ?>"><?php _e( 'Add a restaurant via the back-end', 'restaurants-listings' ); ?></a></li>

					<?php if ( $permalink = listings_get_permalink( 'submit_restaurant_form' ) ) : ?>
						<li><a href="<?php echo esc_url( $permalink ); ?>"><?php _e( 'Add a restaurant via the front-end', 'restaurants-listings' ); ?></a></li>
					<?php else : ?>
						<li><a href="https://wprestaurantmanager.com/document/the-restaurant-submission-form/"><?php _e( 'Find out more about the front-end restaurant submission form', 'restaurants-listings' ); ?></a></li>
					<?php endif; ?>

					<?php if ( $permalink = listings_get_permalink( 'restaurants' ) ) : ?>
						<li><a href="<?php echo esc_url( $permalink ); ?>"><?php _e( 'View submitted restaurant listings-restaurants', 'restaurants-listings' ); ?></a></li>
					<?php else : ?>
						<li><a href="https://wprestaurantmanager.com/document/shortcode-reference/#section-1"><?php _e( 'Add the [restaurants] shortcode to a page to list restaurants', 'restaurants-listings' ); ?></a></li>
					<?php endif; ?>

					<?php if ( $permalink = listings_get_permalink( 'restaurant_dashboard' ) ) : ?>
						<li><a href="<?php echo esc_url( $permalink ); ?>"><?php _e( 'View the restaurant dashboard', 'restaurants-listings' ); ?></a></li>
					<?php else : ?>
						<li><a href="https://wprestaurantmanager.com/document/the-restaurant-dashboard/"><?php _e( 'Find out more about the front-end restaurant dashboard', 'restaurants-listings' ); ?></a></li>
					<?php endif; ?>
				</ul>

				<p><?php printf( __( 'And don\'t forget, if you need any more help using <em>Listings</em> you can consult the %1$sdocumentation%2$s or %3$spost on the forums%2$s!', 'restaurants-listings' ), '<a href="https://wprestaurantmanager.com/documentation/">', '</a>', '<a href="https://wordpress.org/support/plugin/wp-restaurant-manager">' ); ?></p>

				<div class="listings-restaurants-support-the-plugin">
					<h3><?php _e( 'Support the Ongoing Development of this Plugin', 'restaurants-listings' ); ?></h3>
					<p><?php _e( 'There are many ways to support open-source projects such as Listings, for example code contribution, translation, or even telling your friends how awesome the plugin (hopefully) is. Thanks in advance for your support - it is much appreciated!', 'restaurants-listings' ); ?></p>
					<ul>
						<li class="icon-review"><a href="https://wordpress.org/support/view/plugin-reviews/listings-restaurants#postform"><?php _e( 'Leave a positive review', 'restaurants-listings' ); ?></a></li>
						<li class="icon-localization"><a href="https://www.transifex.com/projects/p/listings-restaurants/"><?php _e( 'Contribute a localization', 'restaurants-listings' ); ?></a></li>
						<li class="icon-code"><a href="https://github.com/TheLookandFeel/listings-restaurants"><?php _e( 'Contribute code or report a bug', 'restaurants-listings' ); ?></a></li>
						<li class="icon-forum"><a href="https://wordpress.org/support/plugin/listings-restaurants"><?php _e( 'Help other users on the forums', 'restaurants-listings' ); ?></a></li>
					</ul>
				</div>

			<?php endif; ?>
		</div>
		<?php
	}
}