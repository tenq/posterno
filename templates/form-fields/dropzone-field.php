<?php
/**
 * The template for displaying the file upload dropzone field.
 *
 * This template can be overridden by copying it to yourtheme/pno/form-fields/dropzone-field.php
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
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define the ajax url to which we're going to upload the file.
$upload_url = wp_nonce_url(
	add_query_arg(
		[
			'action'      => 'pno_dropzone_upload',
			'dropzone_id' => $data->get_id(),
		], admin_url( 'admin-ajax.php' )
	), 'pno_dropzone_upload', $data->get_id()
);

$max_files = pno_dropzone_get_max_files_amount( $data );

?>

<div class="pno-dropzone dropzone dropzone-single mb-3" data-toggle="dropzone" data-dropzone-url="<?php echo esc_url( $upload_url ); ?>" data-max-files="<?php echo esc_attr( absint( $max_files ) ); ?>" data-max-size="3" data-multiple="<?php echo esc_attr( $data->get_option( 'multiple' ) ); ?>" data-field-id="<?php echo esc_attr( $data->get_id() ); ?>">

	<div class="dz-preview dz-preview-single">
		<div class="dz-preview-cover">
			<img class="dz-preview-img" src="" alt="" data-dz-thumbnail>
			<a href="#" class="pno-dropzone-remove-file btn btn-secondary btn-sm" data-dz-remove><i class="fas fa-trash-alt mr-1"></i> <?php esc_html_e( 'Delete image' ); ?></a>
		</div>
	</div>

</div>

<div class="pno-dropzone-components">

	<!-- displays error messages related to the dropzone -->
	<div class="pno-dropzone-error d-none">
		<div class="alert alert-danger" role="alert">
			<?php esc_html_e( 'Something went wrong during the upload.' ); ?>
		</div>
	</div>

	<!-- displays progress of the related dropzone -->
	<div class="pno-dropzone-progress progress d-none">
		<div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">0%</div>
	</div>

	<!-- holds data for the database -->
	<input
		type="hidden"
		<?php pno_form_field_input_class( $data ); ?>
		id="<?php echo esc_attr( $data->get_id() ); ?>"
		aria-describedby="<?php echo esc_attr( $data->get_id() ); ?>"
		name="<?php echo esc_attr( $data->get_id() ); ?>"
		value=""
		<?php echo $data->get_attributes(); //phpcs:ignore ?>
	>

</div>
