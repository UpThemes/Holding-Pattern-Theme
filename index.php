<?php
global $up_options;
$lp_feedburner_url = "http://feeds2.feedburner.com/" . $up_options->feedburner;

get_header();

?>

<body class="home">

	<div id="wrapper">
	
		<div id="content">
	
			<a id="logo" href="<?php echo home_url(); ?>"><img src="
			<?php
					if( $up_options->logo ) :
						echo $up_options->logo;
					endif;
			?>" alt="<?php esc_attr( bloginfo( 'name' ) ); ?>" />
			</a>
	
			<div class="awesome_wrapper">
			
				<div class="awesome">
					
					<h1><?php echo $up_options->headline; ?></h1>
					
					<img src="<?php echo get_template_directory_uri(); ?>/images/divider.jpg" alt="" class="divider" />
					
					<?php if( $up_options->feedburner ) : ?>
					<form action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open('http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $up_options->feedburner; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520');return true">
						<label for="email">Enter your email address:</label>
						<p><input type="text" name="email"/></p>
					    <input type="hidden" value="<?php echo $up_options->feedburner; ?>" name="uri"/>
					    <input type="hidden" name="loc" value="en_US"/>
					    <input type="submit" value="Subscribe" />
					</form>
					<?php endif; ?>
					
					<p><a class="subscribe" href="<?php upfw_rss(); ?>" title="<?php echo esc_html(get_bloginfo('name'), 1) ?> <?php _e( 'RSS feed', 'holding-pattern' ); ?>" rel="alternate" type="application/rss+xml"><?php _e( 'Subscribe to our RSS feed', 'holding-pattern' ); ?></a> <?php _e( 'to stay updated on our progress.', 'holding-pattern' ); ?></p>
					
			    </div>
		    
		    </div>
		    
		    <?php if( $up_options->footer_text ){ ?>
		    <div class="footer"><?php echo $up_options->footer_text; ?></div>
		   	<?php } ?>
	    
	    </div>
    
	</div>


<?php wp_footer(); ?>
</body>
</html>
