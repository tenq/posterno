<?php
/**
 * The template for displaying pno's forms.
 *
 * This template can be overridden by copying it to yourtheme/posterno/form.php
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

<div class="pno-form-container">

	<?php if ( isset( $data->title ) && ! empty( $data->title ) ) : ?>
		<h2><?php echo esc_html( $data->title ); ?></h2>
	<?php endif; ?>

	<?php if ( isset( $data->message ) && ! empty( $data->message ) ) : ?>
		<p><?php echo wp_kses_post( $data->message ); ?></p>
	<?php endif; ?>

	<form action="<?php echo esc_url( $data->form->getAction() ); ?>" method="post" id="" enctype="multipart/form-data">

		<?php foreach ( $data->form->getFields() as $field ) : ?>

			<div <?php pno_form_field_class( $field ); ?>>

				<?php if ( ! empty( $field->getLabel() ) && $field->getType() !== 'checkbox' ) : ?>
					<label for="<?php echo esc_attr( $field->getName() ); ?>"><?php echo esc_html( $field->getLabel() ); ?></label>
				<?php endif; ?>

				<?php if ( ! $field->isRequired() && ! $field->isButton() && $field->getType() !== 'checkbox' ) : ?>
					<span class="pno-optional"><?php esc_html_e( '(optional)', 'posterno' ); ?></span>
				<?php endif; ?>

				<?php echo $field->render(); ?>

				<?php

				// We move the position of the label only for some fields.
				if ( ! empty( $field->getLabel() ) && $field->getType() === 'checkbox' ) :
					?>
					<label for="<?php echo esc_attr( $field->getName() ); ?>"><?php echo esc_html( $field->getLabel() ); ?></label>
				<?php endif; ?>

				<?php if ( ! empty( $field->getHint() ) ) : ?>
					<small class="form-text text-muted">
						<?php echo esc_html( $field->getHint() ); ?>
					</small>
				<?php endif; ?>

			</div>

		<?php endforeach; ?>

	</form>

</div>
