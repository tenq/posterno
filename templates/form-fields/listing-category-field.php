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
$max_parent_selectable = pno_get_selectable_categories_count();
$max_sub_selectable    = pno_get_selectable_subcategories_count();

$sub_categories_placeholder = esc_html__( 'Select one or more subcategories.' );

if ( $max_sub_selectable <= 1 ) {
	$sub_categories_placeholder = esc_html__( 'Select a sub category.' );
}

?>

<pno-listing-category-selector inline-template>
	<div>
		<pno-select2 inline-template v-model="selectedCategories" data-placeholder="<?php echo esc_html( $data->get_option( 'placeholder' ) ); ?>" :settings="{ maximumSelectionLength: <?php echo absint( $max_parent_selectable ); ?> }" data-emitterid="category-changed">
			<div class="pno-select2-wrapper">
				<select class="form-control" multiple>
					<?php foreach ( $listings_categories as $category_id => $category_name ) : ?>
						<option value="<?php echo absint( $category_id ); ?>"><?php echo esc_html( $category_name ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</pno-select2>
		<div class="mt-3" v-if="displaySubcategories">
			<div class="pno-loading" v-if="loading"></div>
			<pno-select2 v-else :options="availableSubcategories" inline-template v-model="selectedSubcategories" data-placeholder="<?php echo esc_html( $sub_categories_placeholder ); ?>" :settings="{ maximumSelectionLength: <?php echo absint( $max_sub_selectable ); ?> }">
				<div class="pno-select2-wrapper">
					<select class="form-control" multiple></select>
				</div>
			</pno-select2>
		</div>
	</div>
</pno-listing-category-selector>

<input
	type="hidden"
	<?php pno_form_field_input_class( $data ); ?>
	name="<?php echo esc_attr( $data->get_object_meta_key() ); ?>"
	id="pno-field-<?php echo esc_attr( $data->get_object_meta_key() ); ?>"
	value="<?php echo ! empty( $data->get_value() ) ? esc_attr( $data->get_value() ) : ''; ?>"
>

