<?php


function send($message, $status = 200){
	http_response_code($status);
	echo json_encode($message);
	exit;
}


