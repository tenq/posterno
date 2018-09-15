<?php
/**
 * The template for displaying the listing submission page.
 *
 * This template can be overridden by copying it to yourtheme/pno/listing-submission.php
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

<div id="pno-listing-submission-page">

	<?php

	// Display submission steps.
	posterno()->templates
		->set_template_data( $data )
		->get_template_part( 'forms/steps' );

	// Display related form.
	posterno()->templates
		->set_template_data( $data )
		->get_template_part( 'forms/general', 'form' );

	?>

</div>
