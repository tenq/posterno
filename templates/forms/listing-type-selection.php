<?php
/**
 * The template for displaying the listing type selector part.
 *
 * This template can be overridden by copying it to yourtheme/pno/forms/listing-type-selection.php
 *
 * HOWEVER, on occasion PNO will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @version 1.0.0
 */

// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

?>

<div id="pno-listing-type-selection">

	<?php if ( isset( $data->title ) && ! empty( $data->title ) ) : ?>
		<h2><i class="fas fa-info-circle"></i> <?php echo esc_html( $data->title ); ?></h2>
	<?php endif; ?>

	<div class="row pno-types-container">
		<?php

		foreach ( pno_get_listings_types() as $type_id => $type_name ) :

			$submission_url = add_query_arg(
				[
					'listing_type' => absint( $type_id ),
				],
				get_permalink()
			);

		?>

		<div class="col-sm-4">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title"><?php echo esc_html( $type_name ); ?></h5>
					<?php if ( $listing_type_description = carbon_get_term_meta( $type_id, 'submission_description' ) ) : ?>
						<p class="card-text"><?php echo wp_kses_post( $listing_type_description ); ?></p>
					<?php endif; ?>
					<a href="<?php echo esc_url( $submission_url ); ?>" class="btn btn-secondary"><?php echo esc_html( $data->submit_label ); ?></a>
				</div>
			</div>
		</div>

		<?php endforeach; ?>
	</div>

</div>
