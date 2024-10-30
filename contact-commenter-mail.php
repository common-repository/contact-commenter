<?php

	if ( 'POST' != $_SERVER['REQUEST_METHOD'] ) {
		header('Allow: POST');
		header('HTTP/1.1 405 Method Not Allowed');
		header('Content-Type: text/plain');
		exit;
	}
	
	require_once '../../../wp-load.php';
		
	$subject = $_POST["title"];
	$name = get_option('blogname');
	$from = get_option('admin_email');
	$body = '<div style="font-family:tahoma;">'.$_POST["body"].'</div>';
	$body = str_replace('\"', '"', $body);
	//$cc = get_option('admin_email');
	$bcc = get_option('admin_email');
	//$reply = get_option('admin_email');
	$to = $_POST["author_email"];
	
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/html; charset=UTF-8;\r\n";
	$headers .= 'From: ' . $name . ' <' . $from . ">\r\n";
	//$headers .= 'Cc: ' . $cc .  "\r\n";
	$headers .= 'Bcc: ' . $bcc .  "\r\n";
	//$headers .= 'Reply-To: ' . $reply .  "\r\n";
	wp_mail($to, $subject, $body, $headers);
	
	echo 'OK';
	
?>
