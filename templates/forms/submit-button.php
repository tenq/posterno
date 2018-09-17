<?php
/**
 * The template for displaying the forms submit button.
 *
 * This template can be overridden by copying it to yourtheme/pno/forms/submit-button.php
 *
 * HOWEVER, on occasion PNO will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version 1.0.0
 * @package posterno
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<div class="col-sm-12">
	<button
		type="submit"
		class="btn btn-primary"
		<?php echo pno_is_vue_form( $data->form ); //phpcs:ignore ?>
	>
		<?php echo esc_html( $data->submit_label ); ?>
	</button>
</div>
