<style type="text/css">
	img {max-width:250px; max-height:250px; border:none;}
	a {display:block;}
	
</style>
<?php
$pageURL = 'http';
if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
    $pageURL .= "://";
if ($_SERVER["SERVER_PORT"] != "80") {
    $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
} else {
    $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
}
$uploads = dirname($pageURL)."/";
$theme = explode('admin/uploads/', $pageURL);
$theme = $theme[0];
$timthumb = $theme."timthumb/timthumb.php";?>

<script src="<?=$theme?>/js/jquery-1.4.1.min.js" type="text/javascript"></script>
<script type="text/javascript" src="<?= $theme?>/js/preview.js"></script>

<?php $path = "./";
$dir_handle = @opendir($path) or die("Unable to open folder");

while (false !== ($file = readdir($dir_handle))) {

if($file == "index.php") continue;
if($file == ".") continue;
if($file == "..") continue;
if($file == "list.php") continue;
if($file == "Thumbs.db") continue;?>
<a class="preview" href="<?php echo $uploads.$file;?>"><img src="<?=$uploads.$file?>" /></a>
<?php }
closedir($dir_handle);
?>
