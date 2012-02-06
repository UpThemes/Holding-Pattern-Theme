<?php

// Add UpThemes Framework
require_once('admin/admin.php');

function get_custom_styles(){

global $up_options;

?>

<style type="text/css">

body{
	background: <?php echo $up_options->background_color; ?> url(<?php echo $up_options->background_image; ?>) no-repeat top center;
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

?>