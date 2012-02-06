<?php
//Upload Security
$upload_security = md5($_SERVER['SERVER_ADDR']);
$uploaddir = '../uploads/'; 
if($_FILES):
    foreach($_FILES as $file):
	$file = $uploaddir . basename($file['name']); 
	 
	if (move_uploaded_file($_FILES[$upload_security]['tmp_name'], $file)) { 
	    echo "success"; 
	} else {
	    echo "error".$_FILES[$upload_security]['tmp_name'];
	}
    endforeach;
endif;
?>