<?php

// Add UpThemes Framework
require_once('admin/admin.php');

/**
 * Theme setup functions
 */
function holdingpattern_setup_theme() {
	/*
	 * Enable translation
	 * 
	 * Declare Theme textdomain and define
	 * location for translation files.
	 * 
	 * Translations can be added to the /languages
	 * directory.
	 *
	 * @since	Holding Pattern 1.2
	 */
	load_theme_textdomain( 'holding-pattern', get_template_directory() . '/languages' );

	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) ) {
		require_once( $locale_file );
	}
}
add_action( 'after_setup_theme', 'holdingpattern_setup_theme' );


/**
 * Print custom Theme styles
 */
function holdingpattern_print_custom_styles(){

	global $up_options;
	if ( ! is_admin() ) {
	?>

<style type="text/css">

body{
	background: <?php echo $up_options->background_color; ?> url(<?php echo esc_url( $up_options->background_image ); ?>) no-repeat top center;
	<?php if ($up_options->background_position): ?> background-position: <?php echo $up_options->background_position; ?>; <?php endif; ?>
	<?php if ($up_options->background_repeat): ?> background-position: <?php echo $up_options->background_repeat; ?>; <?php endif; ?>
}

.awesome_wrapper,
.footer{
	width: <?php echo $up_options->page_width; ?>;
}

</style>

	<?php
	}
}
add_action( 'wp_print_styles', 'holdingpattern_print_custom_styles' );
?>