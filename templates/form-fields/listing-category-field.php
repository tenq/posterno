<?php
/**
 * The template for displaying the category selection field.
 *
 * This is a Vuejs powered field.
 *
 * This template can be overridden by copying it to yourtheme/pno/form-fields/listing-category-field.php
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
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Retrieve categories.
$listing_type_id     = pno_get_submission_queried_listing_type_id();
$listings_categories = pno_get_listings_categories_for_submission_selection( $listing_type_id );

// Determine settings for the field.
$multiple          = pno_get_option( 'submission_categories_multiple' ) ? 'multiple' : false;
$placeholder_label = __( 'Select a category...' );
if ( $multiple ) {
	$placeholder_label = __( 'Select one or more categories...' );
}
$max_selection = pno_get_selectable_categories_count();

?>

<pno-listing-category-selector inline-template>
	<div>

		<pno-select2 inline-template v-model="selectedCategories" data-placeholder="<?php echo esc_html( $placeholder_label ); ?>" :settings="{ maximumSelectionLength: <?php echo absint( $max_selection ); ?> }" data-emitterid="category-changed">
			<div class="pno-select2-wrapper">
				<select class="form-control" <?php echo esc_attr( $multiple ); ?>>
					<?php if ( ! empty( $listings_categories ) && is_array( $listings_categories ) && pno_get_option( 'submission_categories_sublevel' ) ) : ?>

						<?php foreach ( $listings_categories as $listing_category ) : ?>
							<optgroup label="<?php echo esc_html( $listing_category['parent_name'] ); ?>">
								<?php foreach ( $listing_category['subcategories'] as $subcategory ) : ?>
									<option value="<?php echo absint( $subcategory['id'] ); ?>"><?php echo esc_html( $subcategory['name'] ); ?></option>
								<?php endforeach; ?>
							</optgroup>
						<?php endforeach; ?>

					<?php else : ?>

						<?php foreach ( $listings_categories as $category_id => $category_name ) : ?>
							<option value="<?php echo absint( $category_id ); ?>"><?php echo esc_html( $category_name ); ?></option>
						<?php endforeach; ?>

					<?php endif; ?>
				</select>
			</div>
		</pno-select2>

	</div>
</pno-listing-category-selector>

<input
	type="hidden"
	<?php pno_form_field_input_class( $data ); ?>
	name="<?php echo esc_attr( $data->get_name() ); ?>"
	id="<?php echo esc_attr( $data->get_id() ); ?>"
	value="<?php echo ! empty( $data->get_value() ) ? esc_attr( $data->get_value() ) : ''; ?>"
>
