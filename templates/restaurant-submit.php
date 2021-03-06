<?php
/**
 * Restaurant Submission Form
 */
if ( ! defined( 'ABSPATH' ) ) exit;

global $listings;
?>
<form action="<?php echo esc_url( $action ); ?>" method="post" id="submit-restaurant-form" class="listings-form" enctype="multipart/form-data">

	<?php do_action( 'submit_restaurant_form_start' ); ?>

	<?php if ( apply_filters( 'submit_restaurant_form_show_signin', true ) ) : ?>

		<?php listings_get_template( 'account-signin.php' ); ?>

	<?php endif; ?>

	<?php if ( listings_user_can_post_listing() || listings_user_can_edit_listing( $restaurant_id ) ) : ?>

		<!-- Restaurant Information Fields -->
		<?php do_action( 'submit_restaurant_form_restaurant_fields_start' ); ?>

		<?php foreach ( $restaurant_fields as $key => $field ) : ?>
			<fieldset class="fieldset-<?php esc_attr_e( $key ); ?>">
				<label for="<?php esc_attr_e( $key ); ?>"><?php echo $field['label'] . apply_filters( 'submit_restaurant_form_required_label', $field['required'] ? '' : ' <small>' . __( '(optional)', 'restaurants-listings' ) . '</small>', $field ); ?></label>
				<div class="field <?php echo $field['required'] ? 'required-field' : ''; ?>">
					<?php listings_get_template( 'form-fields/' . $field['type'] . '-field.php', array( 'key' => $key, 'field' => $field ) ); ?>
				</div>
			</fieldset>
		<?php endforeach; ?>

		<?php do_action( 'submit_restaurant_form_restaurant_fields_end' ); ?>

		<!-- Company Information Fields -->
		<?php if ( $company_fields ) : ?>
			<h2><?php _e( 'Company Details', 'restaurants-listings' ); ?></h2>

			<?php do_action( 'submit_restaurant_form_company_fields_start' ); ?>

			<?php foreach ( $company_fields as $key => $field ) : ?>
				<fieldset class="fieldset-<?php esc_attr_e( $key ); ?>">
					<label for="<?php esc_attr_e( $key ); ?>"><?php echo $field['label'] . apply_filters( 'submit_restaurant_form_required_label', $field['required'] ? '' : ' <small>' . __( '(optional)', 'restaurants-listings' ) . '</small>', $field ); ?></label>
					<div class="field <?php echo $field['required'] ? 'required-field' : ''; ?>">
						<?php listings_get_template( 'form-fields/' . $field['type'] . '-field.php', array( 'key' => $key, 'field' => $field ) ); ?>
					</div>
				</fieldset>
			<?php endforeach; ?>

			<?php do_action( 'submit_restaurant_form_company_fields_end' ); ?>
		<?php endif; ?>

		<?php do_action( 'submit_restaurant_form_end' ); ?>

		<p>
			<input type="hidden" name="listings_form" value="<?php echo $form; ?>" />
			<input type="hidden" name="restaurant_id" value="<?php echo esc_attr( $restaurant_id ); ?>" />
			<input type="hidden" name="step" value="<?php echo esc_attr( $step ); ?>" />
			<input type="submit" name="submit_restaurant" class="button" value="<?php esc_attr_e( $submit_button_text ); ?>" />
		</p>

	<?php else : ?>

		<?php do_action( 'submit_restaurant_form_disabled' ); ?>

	<?php endif; ?>
</form>
