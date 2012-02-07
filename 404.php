<?php 

$suspects = array();

$suspects[1]['name'] = "Mr. Green";
$suspects[1]['img'] = "mr_green.jpg";
$suspects[2]['name'] = "Miss Scarlet";
$suspects[2]['img'] = "miss_scarlet.jpg";
$suspects[3]['name'] = "Colonel Mustard";
$suspects[3]['img'] = "colonel_mustard.jpg";
$suspects[4]['name'] = "Mrs. Peacock";
$suspects[4]['img'] = "mrs_peacock.jpg";
$suspects[5]['name'] = "Mrs. White";
$suspects[5]['img'] = "mrs_white.jpg";
$suspects[6]['name'] = "Professor Plum";
$suspects[6]['img'] = "professor_plum.jpg";

$weapons = array();

$weapons[1]['name'] = "knife";
$weapons[1]['img'] = "knife.jpg";
$weapons[2]['name'] = "candlestick";
$weapons[2]['img'] = "candlestick.jpg";
$weapons[3]['name'] = "lead pipe";
$weapons[3]['img'] = "lead_pipe.jpg";
$weapons[4]['name'] = "revolver";
$weapons[4]['img'] = "revolver.jpg";
$weapons[5]['name'] = "rope";
$weapons[5]['img'] = "rope.jpg";
$weapons[6]['name'] = "wrench";
$weapons[6]['img'] = "wrench.jpg";

$locations = array();

$locations[1]['name'] = "ballroom";
$locations[1]['img'] = "ballroom.jpg";
$locations[2]['name'] = "billiard room";
$locations[2]['img'] = "billiard_room.jpg";
$locations[3]['name'] = "conservatory";
$locations[3]['img'] = "conservatory.jpg";
$locations[4]['name'] = "dining room";
$locations[4]['img'] = "dining_room.jpg";
$locations[5]['name'] = "hall";
$locations[5]['img'] = "hall.jpg";
$locations[6]['name'] = "kitchen";
$locations[6]['img'] = "kitchen.jpg";
$locations[7]['name'] = "library";
$locations[7]['img'] = "library.jpg";
$locations[8]['name'] = "lounge";
$locations[8]['img'] = "lounge.jpg";
$locations[9]['name'] = "study";
$locations[9]['img'] = "study.jpg";

get_header();

?>

<body class="four04">

	<div id="wrapper">
	
		<div id="content">
	
			<a id="logo" href="<?php echo home_url(); ?>"><img src="
			<?php
					if($up_options->logo):
						echo $up_options->logo;
					endif;
			?>" alt="<?php esc_attr( bloginfo( 'name' ) ); ?>" />
			</a>
				
			<div class="awesome_wrapper">
			
				<div class="awesome">
					
					<h1><?php _e( 'Page not found. <span>We think it may have been <strong>murdered</strong>', 'holding-pattern' ); ?>.</span></h1>
					
					<p>
						<img src="<?php echo get_template_directory_uri(); ?>/clue/suspect/<?php echo $suspects[rand(1,6)]['img'] ?>" alt="<?php echo $suspects[rand(1,6)]['name'] ?>" width="150" height="240"> &nbsp; 
						<img src="<?php echo get_template_directory_uri(); ?>/clue/location/<?php echo $locations[rand(1,9)]['img'] ?>" alt="<?php echo $locations[rand(1,9)]['name'] ?>" width="150" height="240"> &nbsp;     
						<img src="<?php echo get_template_directory_uri(); ?>/clue/weapon/<?php echo $weapons[rand(1,6)]['img'] ?>" alt="<?php echo $weapons[rand(1,6)]['name'] ?>" width="150" height="240">
					</p>
					
					<p>
						<a class="button" href="<?php echo home_url(); ?>"><?php _e('&laquo; Return to Homepage', 'holding-pattern' ); ?></a>
					</p>
					
			    </div><!-- .awesome -->
		    
		    </div><!-- .awesome_wrapper -->
		    
		    <?php if( $up_options->footer_text ){ ?>
		    <div class="footer"><?php echo $up_options->footer_text; ?></div>
		   	<?php } ?>
	    
	    </div><!-- #content -->
    
	</div><!-- #wrapper -->

<?php wp_footer(); ?>
</body>
</html>