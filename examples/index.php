<?php

require_once '../src/Router.php';

$bogart = new Bogart\Router();

$bogart->get('/',function(){
	echo 'Hello, World!';
});

$bogart->get('/hello/(:alpha)',function($input){
	echo 'Hello, ' . $input[0];
});

$bogart->response();