<?php

require_once '../bogart.php';

$bogart = new Bogart();

$bogart->get('/',function(){
	echo 'Hello, World!';
});

$bogart->get('/hello/(:alpha)',function($name){
	echo 'Hello, ' . $name;
});

$bogart->response();