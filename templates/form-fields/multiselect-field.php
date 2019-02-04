<?php
/**
 * The template for displaying the multiselect field.
 *
 * This template can be overridden by copying it to yourtheme/posterno/form-fields/multiselect-field.php
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
if ( ! defined( 'ABSPATH' ) ) exit;

?>
<select
	multiple="multiple"
	<?php pno_form_field_input_class( $data ); ?>
	name="<?php echo esc_attr( $data->get_object_meta_key() ); ?>[]"
	id="pno-field-<?php echo esc_attr( $data->get_object_meta_key() ); ?>"
	<?php echo $data->get_attributes(); //phpcs:ignore ?>
	>
	<?php foreach ( $data->get_options() as $key => $value ) : ?>
		<option
			value="<?php echo esc_attr( $key ); ?>"
			<?php if ( ! empty( $data->get_value() ) && is_array( $data->get_value() ) ) selected( in_array( $key, $data->get_value() ), true ); ?>
		>
			<?php echo esc_html( $value ); ?>
		</option>
	<?php endforeach; ?>
</select>
