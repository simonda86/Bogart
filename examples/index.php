<?php

require_once '../bogart.php';

$bogart = new Bogart();

$bogart->get('/',function(){
	echo 'Hello, World!';
});

$bogart->get('/hello/(:alpha)',function($input){
	echo 'Hello, ' . $input[0];
});

$bogart->response();