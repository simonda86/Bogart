# Bogart #

Bogart is a simple RESTful PHP library, the syntax is based on ruby's sinatra so Bogart was named after fellow Rat Packer Humpfrey Bogart. Bogart was designed for use with PHP anonymous functions, so PHP version 5.3.0 or higher is required.

## Installation ##
Download the library, include the bogart file and create an new instance of the class. 

	require 'bogart.php';
	$bogart = new Bogart();
	
You will need to create a .htaccess file with the following for your project.
	
	RewriteEngine On
	RewriteCond %{REQUEST_FILENAME} !-f
	RewriteCond %{REQUEST_FILENAME} !-d
	RewriteRule ^(.+)$ index.php?uri=$1 [QSA,L]
	
## Usage ##
When you the library installed all you need to do is setup your routes and call the response function.

	$bogart->get('/',function(){
		echo 'Hello, World!';
	);
	
	$bogart->response();
	
Bogart uses 4 HTTP methods for CRUD operations.

	$bogart->get('/',function(){
		echo 'List all items';
	);
	
	$bogart->post('/',function(){
		echo 'Create a new item';
	);
	
	$bogart->put('/',function(){
		echo 'Update an item';
	);
	$bogart->delete('/',function(){
		echo 'Delete an item';
	);

Put and Delete are both actually POST requests with a hidden form _method element

	<input type="hidden" name="_method" value="PUT" />
	
## Wildcards ##
You can use wilcards to recieve variables such (:num) and (:alpha), the variables will then be passed to the function as an argument.

	$bogart->get('/item/(:num)', function($id){
		echo 'Item id: ' . $id;
	});
	$bogart->get('/item/(:alpha)', function($name){
		echo 'Item name: ' . $name;
	}