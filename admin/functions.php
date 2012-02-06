<?php
/*
    UpThemes Framework
*/

// Framework Version Number
define('UPTHEMES_VER', '0.3.2');

// ---------------  Theme Constants from style.css -------------------- //

$get_up_theme = get_theme_data(STYLESHEETPATH .'/style.css');
$theme_title = $get_up_theme['Title'];
$theme_shortname = strtolower(preg_replace('/ /', '_', $theme_title));
$theme_version = $get_up_theme['Version'];
define('UPTHEMES_NAME', $theme_title);
define('UPTHEMES_SHORT_NAME', $theme_shortname);
define('UPTHEMES_THEME_VER', $theme_version);

// -------- CSS and JavaScript Includes for UpThemes Framework --------- //

//Check if on framework pages
if(preg_match('/page=upthemes/', $_SERVER['REQUEST_URI'])):
	function get_scripts_styles(){	
		$upthemes =  get_bloginfo('stylesheet_directory').'/admin/';
		wp_enqueue_style('up_framework',$upthemes."css/up_framework.css");
		//Check if theme-options/style.css exists and load it
		if(file_exists(STYLESHEETPATH ."/theme-options/style.css")):
			wp_enqueue_style('theme_options',get_bloginfo('stylesheet_directory')."/theme-options/style.css");
		endif;
		wp_enqueue_style('farbtastic');
		wp_deregister_script('jquery');
		wp_register_script('jquery','http://ajax.googleapis.com/ajax/libs/jquery/1.4/jquery.min.js');
		wp_enqueue_script('jquery.history',
			$upthemes."js/jquery.history.js",
			array('jquery'));
		wp_enqueue_script('jquery.color',
			$upthemes."js/jquery.color.js",
			array('jquery'));
		wp_enqueue_script('jquery.copy',
			$upthemes."js/jquery.copy.js",
			array('jquery'));
		wp_enqueue_script('ajaxupload',
			$upthemes."js/ajaxupload.js",
			array('jquery'));
	}
	add_action('admin_init','get_scripts_styles');

	function extra_scripts(){

		$upthemes =  get_bloginfo('stylesheet_directory').'/admin/'; ?>
		<script type="text/javascript" src="<?php echo $upthemes."js/up_framework.js"; ?>"></script>
		<script type="text/javascript" src="<?php echo get_bloginfo('wpurl')."/wp-admin/js/farbtastic.js"; ?>"></script>

	<?php }

	add_action('admin_head','extra_scripts');

endif;

// ---------------  Create Options Tabs -------------------- //

// Discover Options Files and Create Tabs Array
if(is_admin()):
    $path = STYLESHEETPATH."/theme-options/";
    $directory = @opendir($path) or die("Cannot open theme-options folder in the ".UPTHEMES_NAME." folder");
    while (false !== ($file = readdir($directory))) {
		if(!preg_match('/_/', $file)) continue;
        
        //Take the extension off
        $file = explode('.php', $file);
        
        //Separate the ordinal
        $file = explode('_', $file[0]);
        $order = $file[1];
        //Define the shortname
        $shortname = $file[0];
        
        //Define the title
        $file = explode('-', $shortname);
        foreach ($file as $part):
            $title .= $part." ";
        endforeach;
        $title = ucwords($title);
        
        //Add tab to array
        global $up_tabs;
        $up_tabs[$order] =  array(trim($title) => $shortname);
        $title = '';
    }
    closedir($directory);
    
    //Sort tab order
    global $up_tabs;
    ksort($up_tabs);
endif;

// -------------- Create Default Options --------------------//

function up_defaults(){
    if(!get_option('up_themes_'.UPTHEMES_SHORT_NAME)):
        //Redirect to options page where defaults will automatically be set
        header('Location: '.get_bloginfo('wpurl').'/wp-admin/admin.php?page=upthemes');
    endif;
}
add_action('admin_init', 'up_defaults');

// ---------------  Global Theme Options -------------------- //

$up_options_db = get_option('up_themes_'.UPTHEMES_SHORT_NAME);
global $up_options;
//Check if options are stored properly
if(is_array($up_options_db)):
    //Check array to an object
    foreach ($up_options_db as $k => $v) {
	$up_options -> {$k} = $v;
    }
endif;

up_multiple($update_option->theme1);

// ---------------  UpThemes Admin Options ---------------------- //

if(is_admin()):
    add_action('admin_menu', 'upthemes_admin');
    
    function upthemes_admin() {
      add_menu_page(UPTHEMES_NAME. ' Options', UPTHEMES_NAME, '10', 'upthemes', 'upthemes_admin_home', get_bloginfo('stylesheet_directory').'/admin/images/upfw_ico_up_16x16.png', 36);
      
        //Create tabbed pages from array
        global $up_tabs;
        foreach ($up_tabs as $tab):
            foreach($tab as $title => $shortname):
                add_submenu_page('upthemes', $title, $title, '10', 'upthemes#/'.$shortname, 'upthemes_admin_'.$shortname);
            endforeach;
        endforeach;
    
      //Static subpages
    add_submenu_page('upthemes', 'Import/Export', 'Import/Export', '10', 'upthemes#/import-export', 'upthemes_admin_import_export');
      add_submenu_page('upthemes', 'Documentation', 'Documentation', '10', 'upthemes-docs', 'upthemes_admin_docs');
      add_submenu_page('upthemes', 'Buy Themes', 'Buy Themes', '10', 'upthemes-buy', 'upthemes_admin_buy');
    }
    
    function upthemes_admin_home() {require_once('home.php');}
    function upthemes_admin_docs(){require_once('docs.php');}
    function upthemes_admin_buy(){require_once('buy.php');}
    function upthemes_admin_import_export(){require_once('import-export.php');}
endif;

// ---------------  Render Individual Options -------------------- //

if(is_admin()):
    function find_defaults($options){
        global $up_defaults;
        print_r($options);
    }
endif;

// ---------------  Render Individual Options -------------------- //

if(is_admin()):

    function render_options($options){
        global $up_options;
        global $wpdb;
        foreach ($options as $value) {
            //Check if there are additional attributes
            if(is_array($value['attr'])):
                $i = $value['attr'];
                global $attr;
                //Convert array into a string
                foreach($i as $k => $v):
                    $attr .= $k.'="'.$v.'" ';
                endforeach;
            endif;
            
            //Determine the type of input field
            switch ( $value['type'] ) {
                
                //Render Text Input
                case 'text':?>
                    <li>
                        <fieldset class="title">
                            <div class="inner">
                                <label for="<?=$value['id']?>"><?=$value['name']?></label>
                                <?php if($value['desc']): ?><kbd><?=$value['desc']?></kbd><?php endif;?>
                            </div>
                        </fieldset>
                        
                        <fieldset class="data">
                            <div class="inner">
                                <input type="text" name="<?=$value['id']?>" value="<?php if($up_options->$value['id']): echo $up_options->$value['id']; else: echo $value['value']; endif;?>" id="<?=$value['id']?>" <?=$attr?> />
                            </div>
                        </fieldset>
                    </li>
                    
                    <?php $attr = '';
                break;
                
                //Render Custom User Text Inputs
                case 'text_list':?>
                    <li>
                        <fieldset class="title">
                            <div class="inner">
                                <label for="<?=$value['id']?>"><?=$value['name']?></label>
                                <?php if($value['desc']): ?><kbd><?=$value['desc']?></kbd><?php endif;?>
                            </div>
                        </fieldset>
                        <script type="text/javascript">
                            $(function(){
                                $('p.add_text_list a').live('click', function(){
                                    $('div.text_list').append('<div class="entry"><input class="text_list" type="text" name="<?=$value['id']?>[]" /><p class="delete_text_list"><a href="#"><img src="<?php bloginfo("stylesheet_directory");?>/admin/images/text_list_delete.jpg" alt="Delete Text Field" /></a></p></div><div class="clear"></div>');
                                    $('input.hiddentext_list').remove();
                                    return false;
                                });
                                $('p.delete_text_list a').live('click', function(){
                                    var parent = $(this).parent();
                                    $(parent).parent().fadeOut(function(){
                                        $(this).remove();
                                    });
                                    var textList = $('div.text_list .entry').length;
                                    if(textList == 1){$('div.text_list').append('<input class="hiddentext_list" type="hidden" name="<?=$value['id']?>" id="<?=$value['id']?>[]" />');}
                                    return false;
                                });
                            });
                        </script>
                        <fieldset class="data">
                            <div class="inner">
                                <div class="text_list">
                                    <p class="add_text_list"><a href="#">Add New Field</a></p>
                                    <?php
                                    if($up_options->$value['id']):
                                        if(is_array($up_options->$value['id'])):
                                            foreach($up_options->$value['id'] as $text):?>
                                                <div class="entry">
                                                    <input class="text_list" type="text" name="<?=$value['id']?>[]" id="<?=$value['id']?>" value="<?=$text?>" <?=$attr?> />
                                                    <p class="delete_text_list"><a href="#"><img src="<?php bloginfo("stylesheet_directory");?>/admin/images/text_list_delete.jpg" alt="Delete Text Field" /></a></p>
                                                    <div class="clear"></div>
                                                </div>
                                            <? endforeach;
                                        endif;
                                    else:
                                        if($value['value']):
                                            if(preg_match('/,/', $value['value'])):
                                                $list = explode(', ', $value['value']);
                                                foreach($list as $text):?>
                                                        <div class="entry">
                                                            <input class="text_list" type="text" name="<?=$value['id']?>[]" id="<?=$value['id']?>" value="<?=$text?>" <?=$attr?> />
                                                            <p class="delete_text_list"><a href="#"><img src="<?php bloginfo("stylesheet_directory");?>/admin/images/text_list_delete.jpg" alt="Delete Text Field" /></a></p>
                                                            <div class="clear"></div>
                                                        </div>
                                                <?php endforeach;
                                            else:
                                                if($value['value'] == $v ):
                                                    $selected = ' selected = "selected"';
                                                endif;
                                            endif;
                                        endif;
                                    endif;?>
                                    
                                </div>
                            </div>
                        </fieldset>
                    </li>
                    
                    <?php $attr = '';
                break;
                
                //Render textarea options
                case 'textarea':?>
                    <li>
                        <fieldset class="title">
                            <div class="inner">
                                <label for="<?=$value['id']?>"><?=$value['name']?></label>
                                <?php if($value['desc']): ?><kbd><?=$value['desc']?></kbd><?php endif;?>
                            </div>
                        </fieldset>
                        
                        <fieldset class="data">
                            <div class="inner">
                                <textarea name="<?=$value['id']?>" id="<?=$value['id']?>" <?=$attr?>><?php if($up_options->$value['id']): echo $up_options->$value['id']; else: echo $value['value']; endif;?></textarea>
                            </div>
                        </fieldset>
                    </li>
                    
                    <?php $attr = '';
                break;
                
                //Render select dropdowns
                case 'select':?>
                    <li>
                        <fieldset class="title">
                            <div class="inner">
                                <label for="<?=$value['id']?>"><?=$value['name']?></label>
                                <?php if($value['desc']): ?><kbd><?=$value['desc']?></kbd><?php endif;?>
                            </div>
                        </fieldset>
                        
                        <fieldset class="data">
                            <div class="inner">
                                <select name="<?=$value['id']?>" id="<?=$value['id']?>" <?=$attr?>>
                                    <option value=""><?php if($value['default_text']): echo $value['default_text']; else: echo "None"; endif;?></option>
                                    <?php
                                    if(is_array($value['options'])):
                                        $i = $value['options'];
                                        foreach($i as $k => $v):
                                            if($up_options->$value['id']):
                                                if($up_options->$value['id'] == $v):
                                                    $selected = ' selected = "selected"';
                                                endif;
                                            else:
                                                if($value['value'] == $v):
                                                    $selected = ' selected = "selected"';
                                                endif;
                                            endif;?>
                                            <option value="<?=$v?>"<?=$selected?>><?=$k?></option>
                                            <?php $selected = '';?>
                                        <?php endforeach;
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </fieldset>
                    </li>
                    
                    <?php $attr = '';
                break;
                
                //Render multple selects
                case 'multiple':?>
                    <li>
                        <fieldset class="title">
                            <div class="inner">
                                <label for="<?=$value['id']?>"><?=$value['name']?></label>
                                <?php if($value['desc']): ?><kbd><?=$value['desc']?></kbd><?php endif;?>
                            </div>
                        </fieldset>
                        
                        <fieldset class="data">
                            <div class="inner">
                                <select MULTIPLE name="<?=$value['id']?>[]" id="<?=$value['id']?>" <?=$attr?>>
                                    <option value=""><?php if($value['default_text']): echo $value['default_text']; else: echo "None"; endif;?></option>
                                    <?php
                                    if(is_array($value['options'])):
                                        $i = $value['options'];
                                        foreach($i as $k => $v):
                                            if($up_options->$value['id']):
                                                if(is_array($up_options->$value['id'])):
                                                    foreach($up_options->$value['id'] as $std):
                                                        if($v == $std):
                                                            $selected = ' selected = "selected"';
                                                        endif;
                                                    endforeach;
                                                endif;
                                            else:
                                                
                                                if($value['value']):
                                                    if(preg_match('/,/', $value['value'])):
                                                        $cats = explode(', ', $value['value']);
                                                        foreach($cats as $cat):
                                                            if(preg_match('/\b'.$v.'\b/', $cat)):
                                                                $selected = ' selected = "selected"';
                                                            endif;
                                                        endforeach;
                                                    else:
                                                        if($value['value'] == $v ):
                                                            $selected = ' selected = "selected"';
                                                        endif;
                                                    endif;
                                                else:
                                                    if($value['value'] == $v ):
                                                        $selected = ' selected = "selected"';
                                                    endif;
                                                endif;
                                                
                                            endif;?>
                                            <option value="<?=$v?>"<?=$selected?>><?=$k?></option>
                                            <?php $selected = '';?>
                                        <?php endforeach;
                                    endif;
                                    ?>
                                </select>
                            </div>
                        </fieldset>
                    </li>
                    
                    <?php $attr = '';
                break;
                
                //Render color picker
                case 'color':?>
                    <li>
                        <fieldset class="title">
                            <div class="inner">
                                <label for="awesome3"><?=$value['name']?></label>
                                <?php if($value['desc']): ?><kbd><?=$value['desc']?></kbd><?php endif;?>
                            </div>
                        </fieldset>
                        <fieldset class="data">
                            <div class="inner">
                                <span class="colorPickerWrapper">
                                    <input type="text" class="popup-colorpicker" id="<?=$value['id']?>" name="<?=$value['id']?>" value="<?php if($up_options->$value['id']): echo $up_options->$value['id']; else: echo $value['value']; endif;?>" <?=$attr?> />
                                    <div class="popup-guy">
                                        <div class="popup-guy-inside">
                                            <div id="<?=$value['id']?>picker" class="color-picker"></div>
                                        </div>
                                    </div>
                                </span>
                            </div>
                        </fieldset>
                    </li>
                    
                    <?php
                break;
                
                //Render upload image
                case 'image':?>
                    <script type="text/javascript">
                        $(function(){
                            
                            //View UpThemes Gallery
                            $('a#<?=$value['id']?>viewgallery').toggle(
                                function(){
                                    $(this).text('Hide Gallery');
                                    $('#<?=$value['id']?>allimages').slideDown();
                                    return false;
                                },
                                function(){
                                    $(this).text('Select from the UpThemes Gallery');
                                    $('#<?=$value['id']?>allimages').slideUp();
                                    return false;
                                }
                            );
                            
                            //Select and image from the gallery
                            $('#<?=$value['id']?>allimages a').live('click', function(){
                                //Add image source to hidden input
                                $('input#<?=$value['id']?>').attr('value', $(this).attr('href'));
                                //Send image to preview
                                $('#<?=$value['id']?>preview').html('<img src="'+$(this).attr('href')+'" alt="<?=$value['id']?> Image" />');
                                //Save Me Fool
                                $('#button-zone').animate({ 
                                    backgroundColor: '#555',
                                    borderLeftColor: '#555',
                                    borderRightColor: '#555'
                                });
                                $('#button-zone button').addClass('save-me-fool');
                                $('.formState').fadeIn( 400 );
                                return false;
                            });
                            <?php //Upload Security
			    $upload_security = md5($_SERVER['SERVER_ADDR']);?>
                            //Upload an Image
                            var <?=$value['id']?>=$('div.uploadify button#<?=$value['id']?>');
                            var status=$('#<?=$value['id']?>status');
                            new AjaxUpload(<?=$value['id']?>, {
                                action: '<?php bloginfo('template_directory'); ?>/admin/upload-file.php',
                                name: '<?=$upload_security?>',
                                onSubmit: function(file, ext){
                                    //Check if file is an image
                                    if (! (ext && /^(JPG|PNG|GIF|jpg|png|jpeg|gif)$/.test(ext))){ 
                                       // extension is not allowed 
                                       status.text('Only JPG, PNG or GIF files are allowed');
                                       return false;
                                    }
                                    status.text('Uploading...');
                                },
                                onComplete: function(file, response){
                                    //On completion clear the status
                                    status.text('');
                                    //Successful upload
                                    if(response==="success"){
                                        //Preview uploaded file
					$('#<?=$value['id']?>preview').removeClass('uploaderror');
                                        $('#<?=$value['id']?>preview').html('<img src="<?php bloginfo('template_directory'); ?>/uploads/'+file+'" alt="<?=$value['id']?> Image" />').addClass('success');
                                        //Add image source to hidden input
                                        $('input#<?=$value['id']?>').attr('value', '<?php bloginfo('template_directory'); ?>/uploads/'+file);
                                        //Append thumbnail to gallery
                                        $('.thumbs').append('<a href="<?php bloginfo('stylesheet_directory')?>/uploads/'+file+'"><img src="<?php bloginfo('stylesheet_directory')?>/timthumb/timthumb.php?src=<?php bloginfo('stylesheet_directory')?>/uploads/'+file+'&amp;w=54&amp;h=54&amp;zc=1&amp;q=65" /></a>')
                                        //Save Me Fool
                                        $('#button-zone').animate({ 
                                            backgroundColor: '#555',
                                            borderLeftColor: '#555',
                                            borderRightColor: '#555'
                                        });
                                        $('#button-zone button').addClass('save-me-fool');
                                        $('.formState').fadeIn( 400 );
                                    } else{
					alert(response);
                                        //Something went wrong
                                        $('#<?=$value['id']?>preview').text(file+' did not upload. Please try again.').addClass('uploaderror');
                                    }
                                }
                            });
                        });
                    </script>
                    <li>
                        <fieldset class="title">
                            <div class="inner">
                                <label for="<?=$value['id']?>"><?=$value['name']?></label>
                                <?php if($value['desc']): ?><kbd><?=$value['desc']?></kbd><?php endif;?>
                            </div>
                            <!-- Image Preview Input -->
                            <div class="preview" id="<?=$value['id']?>preview"><?php
                            
                            if($up_options->$value['id']):
                                echo "<img src='".$up_options->$value['id']."' alt='Preview Image' />";
                            elseif($value['url']):
                                echo "<img src='".$value['url']."' alt='Preview Image' />";
                            else:
                            	echo "<img src='".get_bloginfo('template_url')."/admin/images/upfw_noimage.gif' alt='No Image Available' />";
                            endif;?></div>	
                            
                        </fieldset>
    
                        <fieldset class="data">
                                <div class="inner">
                                    <div class="uploadify">
                                        <button type="button" id="<?=$value['id']?>" class="secondary" <?=$attr?>><?=$value['value']?></button>
                                    </div>
                                    <!-- Upload Status Input -->
                                    <div class="status" id="<?=$value['id']?>status"></div>
                                    <!-- Hidden Input -->
                                    <input type="hidden" name="<?=$value['id']?>" id="<?=$value['id']?>" name="<?=$value['id']?>" value="<?php if($up_options->$value['id']): echo $up_options->$value['id']; else: echo $value['url']; endif;?>" />
                                    <!-- Divider -->
                                    <div class="divider">
                                        <span>OR</span>
                                    </div>
                                    <!-- View Gallery -->
                                    <div class="viewgallery">
                                        <a id="<?=$value['id']?>viewgallery" href="<?php bloginfo('stylesheet_directory')?>/admin/uploads/list.php">Select from the UpThemes Gallery</a>
                                    </div>
                                    <!-- All Images -->
                                    <div id="<?=$value['id']?>allimages" class="allimages">
                                        <div class="thumbs">
                                            <?php $path = STYLESHEETPATH ."/uploads/";
                                            $directory = @opendir($path) or die("Unable to open folder");
                                            while (false !== ($file = readdir($directory))) {
                                                if($file == "index.php") continue;
                                                if($file == ".") continue;
                                                if($file == "..") continue;
                                                if($file == "list.php") continue;
                                                if($file == "Thumbs.db") continue;?>
                                                <a href="<?php bloginfo('stylesheet_directory')?>/uploads/<?=$file?>"><img src="<?php bloginfo('stylesheet_directory')?>/timthumb/timthumb.php?src=<?php bloginfo('stylesheet_directory')?>/uploads/<?=$file?>&amp;w=54&amp;h=54&amp;zc=1&amp;q=65" /></a>
                                            <?php }
                                            closedir($directory);?>
                                        </div>
                                        <?php if($value['url']):?>
                                            <div class="default">
                                                <p><em>Default Image</em></p>
                                                <a href="<?=$value['url']?>"><img src="<?=$value['url']?>" /></a>
                                            </div>
                                        <?php endif;?>
                                    </div>
    
                                </div>
                        </fieldset>
                    </li>
                <?php
                break;
                
                //Render category dropdown
                case 'category':?>
                    <li>
                        <fieldset class="title">
                            <div class="inner">
                                <label for="<?=$value['id']?>"><?=$value['name']?></label>
                                <?php if($value['desc']): ?><kbd><?=$value['desc']?></kbd><?php endif;?>
                            </div>
                        </fieldset>
                        
                        <fieldset class="data">
                            <div class="inner">
                                
                                <select name="<?=$value['id']?>" id="<?=$value['id']?>" <?=$attr?>>
                                    <option value=""><?php if($value['default_text']): echo $value['default_text']; else: echo "None"; endif;?></option>
                                    <?php
                                    $i = $wpdb->get_results("SELECT $wpdb->terms.name, $wpdb->terms.slug, $wpdb->term_taxonomy.term_id FROM $wpdb->terms LEFT JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id WHERE $wpdb->term_taxonomy.taxonomy = 'category' ORDER BY $wpdb->terms.name", ARRAY_A);
                                    foreach($i as $row):
                                            if($up_options->$value['id']):
                                                if($row['slug'] == $up_options->$value['id']):
                                                    $selected = " selected='selected'";
                                                endif;
                                            else:
                                                if($value['value'] == $row['slug']):
                                                    $selected = ' selected = "selected"';
                                                endif;
                                            endif;
                                        echo "<option value='".$row['slug']."'".$selected.">".$row['name']."</option>";
                                        $selected = '';
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                        </fieldset>
                    </li>
                    
                    <?php $attr = '';
                break;
                
                //Render categories multiple select
                case 'categories':?>
                    <li>
                        <fieldset class="title">
                            <div class="inner">
                                <label for="<?=$value['id']?>"><?=$value['name']?></label>
                                <?php if($value['desc']): ?><kbd><?=$value['desc']?></kbd><?php endif;?>
                            </div>
                        </fieldset>
                        
                        <fieldset class="data">
                            <div class="inner">
                                <select MULTIPLE name="<?=$value['id']?>[]" id="<?=$value['id']?>" <?=$attr?>>
                                    <option value=""><?php if($value['default_text']): echo $value['default_text']; else: echo "None"; endif;?></option>
                                    <?php
                                    $i = $wpdb->get_results("SELECT $wpdb->terms.name, $wpdb->terms.slug, $wpdb->term_taxonomy.term_id FROM $wpdb->terms LEFT JOIN $wpdb->term_taxonomy ON $wpdb->terms.term_id = $wpdb->term_taxonomy.term_id WHERE $wpdb->term_taxonomy.taxonomy = 'category' ORDER BY $wpdb->terms.name", ARRAY_A);
                                    foreach($i as $row):
                                        if(!empty($up_options->$value['id'])):
                                            foreach($up_options->$value['id'] as $std):
                                                if($std == $row['slug']):
                                                    $selected = ' selected = "selected"';
                                                endif;
                                            endforeach;
                                        else:
                                            if($value['value']):
                                                if(preg_match('/,/', $value['value'])):
                                                    $cats = explode(', ', $value['value']);
                                                    foreach($cats as $cat):
                                                        if(preg_match('/\b'.$row['slug'].'\b/', $cat)):
                                                            $selected = ' selected = "selected"';
                                                        endif;
                                                    endforeach;
                                                else:
                                                    if($value['value'] == $row['slug'] ):
                                                        $selected = ' selected = "selected"';
                                                    endif;
                                                endif;
                                            else:
                                                if($value['value'] == $row['post_title'] ):
                                                    $selected = ' selected = "selected"';
                                                endif;
                                            endif;
                                        endif;
                                        
                                        echo "<option value='".$row['slug']."'".$selected.">".$row['name']."</option>";
                                        $selected = '';
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                        </fieldset>
                    </li>
                    
                    <?php $attr = '';
                break;
                
                //Render page dropdown
                case 'page':?>
                    <li>
                        <fieldset class="title">
                            <div class="inner">
                                <label for="<?=$value['id']?>"><?=$value['name']?></label>
                                <?php if($value['desc']): ?><kbd><?=$value['desc']?></kbd><?php endif;?>
                            </div>
                        </fieldset>
                        
                        <fieldset class="data">
                            <div class="inner">
                                <select name="<?=$value['id']?>" id="<?=$value['id']?>" <?=$attr?>>
                                    <option value=""><?php if($value['default_text']): echo $value['default_text']; else: echo "None"; endif;?></option>
                                    <?php
                                    $i = $wpdb->get_results("SELECT post_title, ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type='page' ORDER BY post_title", ARRAY_A);
                                    foreach($i as $row):
                                        if($up_options->$value['id']):
                                            if($row['ID'] == $up_options->$value['id']):
                                                $selected = " selected='selected'";
                                            endif;
                                        else:
                                            if($row['post_title'] == $value['value']):
                                                $selected = " selected='selected'";
                                            endif;
                                        endif;
                                        echo "<option value='".$row['ID']."'".$selected.">".$row['post_title']."</option>";
                                        $selected = '';
                                    endforeach;
                                    ?>
                                </select>
                            </div>
                        </fieldset>
                    </li>
                    
                    <?php $attr = '';
                break;
                
                //Render pages muliple select
                case 'pages':?>
                    <li>
                        <fieldset class="title">
                            <div class="inner">
                                <label for="<?=$value['id']?>"><?=$value['name']?></label>
                                <?php if($value['desc']): ?><kbd><?=$value['desc']?></kbd><?php endif;?>
                            </div>
                        </fieldset>
                        
                        <fieldset class="data">
                            <div class="inner">
                                <select multiple="multiple" name="<?=$value['id']?>[]" id="<?=$value['id']?>" <?=$attr?>>
                                    <option value=""><?php if($value['default_text']): echo $value['default_text']; else: echo "None"; endif;?></option>
                                    <?php
                                    $i = $wpdb->get_results("SELECT post_title, ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type='page' ORDER BY post_title", ARRAY_A);
                                    foreach($i as $row):
                                        if(!empty($up_options->$value['id'])):
                                            if($up_options->$value['id']):
                                                foreach($up_options->$value['id'] as $std):
                                                    if($std == $row['ID']):
                                                        $selected = ' selected = "selected"';
                                                    endif;
                                                endforeach;
                                            endif;
                                        else:
                                            if($value['value']):
                                                if(preg_match('/,/', $value['value'])):
                                                    $pages = explode(', ', $value['value']);
                                                    foreach($pages as $page):
                                                        if(preg_match('/\b'.$row['post_title'].'\b/', $page)):
                                                            $selected = ' selected = "selected"';
                                                        endif;
                                                    endforeach;
                                                else:
                                                    if($value['value'] == $row['post_title'] ):
                                                        $selected = ' selected = "selected"';
                                                    endif;
                                                    
                                                endif;
                                            else:
                                                if($value['value'] == $row['post_title'] ):
                                                    $selected = ' selected = "selected"';
                                                endif;
                                            endif;
                                        endif;
                                        echo "<option value='".$row['ID']."'".$selected.">".$row['post_title']."</option>";
                                        $selected = '';
                                    endforeach
                                    ?>
                                </select>
                            </div>
                        </fieldset>
                    </li>
                    <?php $attr = '';
                break;
            
                //Render Form Button
                case 'submit':?>
                    <li>
                        <fieldset class="title">
                            <div class="inner">
                                <label><?=$value['name']?></label>
                                <?php if($value['desc']): ?><kbd><?=$value['desc']?></kbd><?php endif;?>
                            </div>
                        </fieldset>
                        
                        <fieldset class="data">
                            <div class="inner">
                                <div class="uploadify">
                                <button type="submit" id="<?=$value['id']?>" name="<?=$value['id']?>" class="secondary" <?=$attr?>><?=$value['value']?></button>
                                </div>
                            </div>
                        </fieldset>
                    </li>
                    
                    <?php $attr = '';
                break;
            
                //Render Form Button
                case 'button':?>
                    <li>
                        <fieldset class="title">
                            <div class="inner">
                                <label><?=$value['name']?></label>
                                <?php if($value['desc']): ?><kbd><?=$value['desc']?></kbd><?php endif;?>
                            </div>
                        </fieldset>
                        
                        <fieldset class="data">
                            <div class="inner">
                                <div class="uploadify">
                                <button type="button" id="<?=$value['id']?>" name="<?=$value['id']?>" class="secondary" <?=$attr?>><?=$value['value']?></button>
                                </div>
                            </div>
                        </fieldset>
                    </li>
                    
                    <?php $attr = '';
                break;

            
                default:
                break;
            }
        }
    }
endif;

// ---------------  Remove Ugly First Link in WP Sidebar Menu -------------------- //

function remove_ugly_first_link(){?>
    <script type="text/javascript">
	jQuery(document).ready(function(){
	    jQuery('li#toplevel_page_upthemes li.wp-first-item').remove();
	});
    </script>
<?php }
if(is_admin()): add_action("admin_head","remove_ugly_first_link"); endif;

// ---------------  Feedburner Functions -------------------- //

// RSS URL: rss('return') will return the value and not echo it.
function rss($i = ''){
    global $up_options;
    if($up_options->feedburner):
        $rss = "http://feeds.feedburner.com/".$up_options->feedburner;
    else:
        $rss = get_bloginfo_rss('rss2_url');
    endif;
    if($i == 'return'): return $rss; else: echo $rss; endif;
}

//RSS Subscribe URL: rss_email('return') will return the value and not echo it.
function rss_email($i = ''){
    global $up_options;
    if($up_options->feedburner):
        $rssemail = "http://www.feedburner.com/fb/a/emailverifySubmit?feedId=" . $up_options->feedburner;
    else:
        $rssemail = "#";
    endif;
    if($i == 'return'): return $rssemail; else: echo $rssemail; endif;
}

// Iterate through multiple option fields
function up_multiple($option, $space = true, $echo = true){
    if(is_array($option)):
        if($space):
            $space = ' ';
            $offset = -1;
        else:
            $offset = -2;
        endif;
        foreach($option as $single):
            $string .= $single.','.$space;
        endforeach;
        $string = substr_replace($string, ','.$space, $offset);
        if(!$echo): return $string; else: echo $string; endif;
    endif;
}

?>