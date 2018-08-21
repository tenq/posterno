<?php
/**
 * The template for displaying the textarea field.
 *
 * This template can be overridden by copying it to yourtheme/pno/form-fields/textarea-field.php
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

<textarea
	cols="20"
	rows="3"
	class="input-text form-control"
	name="<?php echo esc_attr( isset( $data->name ) ? $data->name : $data->key ); ?>"
	id="<?php echo esc_attr( $data->key ); ?>"
	placeholder="<?php echo empty( $data->placeholder ) ? '' : esc_attr( $data->placeholder ); ?>"
	maxlength="<?php echo ! empty( $data->maxlength ) ? $data->maxlength : ''; ?>"
	<?php if ( ! empty( $data->required ) ) echo 'required'; ?>
	><?php echo isset( $data->value ) ? esc_textarea( html_entity_decode( $data->value ) ) : ''; ?>
</textarea>
<?php if ( ! empty( $data->description ) ) : ?><small class="form-text text-muted"><?php echo $data->description; ?></small><?php endif; ?>
