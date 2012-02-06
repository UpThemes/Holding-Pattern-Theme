<?php if ( is_user_logged_in() ):?>

	<?php $upthemes =  get_bloginfo('stylesheet_directory').'/admin/';?>

	<?php get_scripts_styles(); ?>
	
	<script type="text/javascript">
	    var upThemes = "<?php bloginfo('template_directory'); ?>";
	</script>

	<div id="upthemes_framework">
	    
		<div id="up_header" class="polish">
		
            <h1 id="up_logo"><a href="<?php bloginfo('wpurl');?>/wp-admin/admin.php?page=upthemes">UpThemes Framework</a></h1>
			
			<ul id="up_topnav">
				<li class="support"><a href="http://upthemes.com/forum/">Support</a></li>
				<li class="documentation"><a href="<?php bloginfo('wpurl');?>/wp-admin/admin.php?page=upthemes-docs">Theme Documentation</a></li>
				<li class="buy-themes"><a href="<?php bloginfo('wpurl');?>/wp-admin/admin.php?page=upthemes-buy">Buy Themes</a></li>
			</ul><!-- /#up_topnav -->
		
			<div class="clear"></div>
		
		</div><!-- /#up_header -->
		
		<?php
		
		/* ----------------------- Form Security Check -------------------------- */
		if(isset($_POST['_wpnonce'])):
		    //Check if submitted from this domain
		    check_admin_referer();
		    //Verify Form Nonce
		    if (! wp_verify_nonce($_POST['_wpnonce'], md5('up_themes'.date('D'))) ) die('Security Exception');
		endif;
		
		
		/* ------------------Import/Export Functions ----------------------- */
		//Restore Previous Options
		global $export_message;
		if(isset($_POST['up_restore'])):
		    $current = get_option('up_themes_'.UPTHEMES_SHORT_NAME);
		    $backup = get_option('up_themes_'.UPTHEMES_SHORT_NAME.'_backup');
		    update_option('up_themes_'.UPTHEMES_SHORT_NAME.'_backup', $current);
		    update_option('up_themes_'.UPTHEMES_SHORT_NAME, $backup);
		    $export_message = "<p class='import'>Everything's back to normal now!</p>";
		endif;
		
		//Restore Defaults
		
		if(isset($_POST['up_defaults'])):
		    $current = get_option('up_themes_'.UPTHEMES_SHORT_NAME);
		    update_option('up_themes_'.UPTHEMES_SHORT_NAME.'_backup', $current);
		    delete_option('up_themes_'.UPTHEMES_SHORT_NAME);
		    $export_message = "<p class='import'>Default options restored!<span><form method='post' action=''><input class='up_restore' type='submit' name='up_restore' value='Click Here to Undo'></form></span></p>";
		endif;
		
		
		/* ------------------------- Import Options Code ------------------------------- */
		
		if(isset($_POST['up_import']) && isset($_POST['up_import_code'])):
		    $import = $_POST['up_import_code'];
		    $import = base64_decode($import);
		    $import = explode('~~', $import);
		    
		    //Check if code is legitimate
		    if(count($import) == 2):
			$option_name = $import[0];
			$options = explode('||', $import[1]);
		
			foreach ($options as $option):
			    $option = explode('|', $option);
			    global $new_options;
			    $new_options[$option[0]] = preg_replace('/"/', '\'', stripslashes($option[1]));
			endforeach;
			$current_option = get_option($option_name);
			update_option($option_name.'_backup', $current_option);
			update_option($option_name, $new_options);
			$export_message = "<p class='import'>Options Code Import Successful!<span><form method='post' action=''><input class='up_restore' type='submit' name='up_restore' value='Click Here to Undo'></form></span></p>";
		    else:
			$export_message = "<p class='import'>Oops! Something went wrong. <span>Try pasting your import code again.</span></p>";
		    endif;

		endif;
	    
		/* ------------------------- Save Theme Options ------------------------------- */
		if(isset($_POST['up_save'])):
			$posts = $_POST;
			foreach($posts as $k => $v):
			    //Check if option is array (mulitple selects)
			    if(is_array($v)):
				//Check if array is empty
				$check = 0;
				foreach($v as $key => $value):    
				    if($value != ''):
					$check++;
				    endif;
				endforeach;
				//If array is not empty
				if($check > 0  ):
				    //Remove empty array elements
				    $post[$k] = array_filter($v);
				else:
				    $post[$k] = '';
				endif;
				$check = 0;
			    else:
				//Remove slashes
				$post[$k] = preg_replace('/"/', '\'', stripslashes($v));
			    endif;
			endforeach;
			//Add options array to wp_options table
			update_option('up_themes_'.UPTHEMES_SHORT_NAME, $post);
		    endif;
		
		/* ---------------------- Default Options Functions  ----------------- */
		global $default_check;
		global $default_options;
		
		$option_check = get_option('up_themes_'.UPTHEMES_SHORT_NAME);	
		if($option_check):
		    $default_check = true;
		else:
		    $default_check = false;
		endif;
		

		//Get all options from the database
		$up_options_db = get_option('up_themes_'.UPTHEMES_SHORT_NAME);
		global $up_options;
		//Check if options are stored properly
		if(is_array($up_options_db)):
		    //Check array to an object
		    foreach ($up_options_db as $k => $v) {
			$up_options -> {$k} = $v;
		    }
		endif;
		?>
    
		<form method="post" enctype="multipart/form-data" action="" id="theme-options" name="theme-options">
		    <?php //Security Nonce For Cross Site Hacking
		    $nonce= wp_create_nonce(md5('up_themes'.date('D')));?>
		    <input type="hidden" name="_wpnonce" value="<?=$nonce?>" />
		    
		<div class="button-zone-wrapper">
		    <div id="button-zone" class="polish">
			<span class="top">
			    <span class="formState">Theme options have changed. Make sure to save.</span>
			    <button class="save" id="up_save" name="up_save" type="submit">Save Changes</button>
			</span>
		    </div><!-- #button-zone -->
		</div><!-- /.button-zone-wrapper -->
		
		<div id="up_main">

				<div id="up_sidebar">
				
					<div id="up_nav" class="box">
						
						<ul>
							<?php //Create dynamic tab links from array
							global $up_tabs;
							    foreach ($up_tabs as $tab):
								foreach($tab as $title => $shortname):?>
								    <li class="<?=$shortname?>"><a href="#<?=$shortname?>"><?=$title?></a></li>
								<?php endforeach;
							    endforeach;?>
							<li class="import-export"><a href="#import-export">Import/Export</a></li>
						</ul>
									
					</div><!-- /#up_nav -->
				
				</div><!-- /#up_sidebar -->
				
				<div id="up_content">
					<div id="tabber">
					    <?php //Create dynamic tabs from array
					    foreach ($up_tabs as $order => $tab):
						foreach($tab as $title => $shortname):?>
						    <div id="<?=$shortname?>">
							<h2><?=$title?></h2>
							<ul class="feature-set">
							    <?php require_once (STYLESHEETPATH . '/theme-options/'.$shortname.'_'.$order.'.php'); ?>
							</ul>										
						    </div><!-- /#<?=$shortname?> -->
						<?php endforeach;
					    endforeach;?>
					    </form>
					    <div id="import-export">
						<h2>Import/Export</h2>
						<ul class="feature-set">
						    <?php require_once (STYLESHEETPATH . '/admin/import-export.php'); ?>
						</ul>										
					    </div><!-- /#import-export -->
				
					</div><!-- /#tabber -->

				</div><!-- /#up_content -->
			
				<div class="clear"></div>

			
		
		</div><!-- /#up_container -->
		
		<div id="up_footer">
		
			<ul>
				<li><a href="#"><?=UPTHEMES_NAME?> Version <?=UPTHEMES_THEME_VER?></a></li>
				<li><a href="#">UpThemes Framework Version <?=UPTHEMES_VER?></a></li>
			</ul>
		
		</div><!-- /#up_footer -->
	
	</div><!-- /#upthemes_framework -->

<?php else: echo "You must be logged in to view this page"; endif;?>