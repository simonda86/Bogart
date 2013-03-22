<?php

/**
* 
*/
class Bogart
{
	private $_routes = array('GET' => array(), 'POST' => array(), 'PUT' => array(), 'DELETE' => array());
	private $_methods = array('GET' => array(), 'POST' => array(), 'PUT' => array(), 'DELETE' => array());
	private $_404 = null;
	
	public function get($uri, $method)
	{
		// Process Wildcards
		$uri = $this->_process_wildcards($uri);
		
		$this->_routes['GET'][] = $uri;
		$this->_methods['GET'][] = $method;
	}
	
	public function post($uri, $method)
	{
		// Process Wildcards
		$uri = $this->_process_wildcards($uri);
		
		$this->_routes['POST'][] = $uri;
		$this->_methods['POST'][] = $method;
	}

	public function put($uri, $method)
	{
		// Process Wildcards
		$uri = $this->_process_wildcards($uri);

		$this->_routes['PUT'][] = $uri;
		$this->_methods['PUT'][] = $method;
	}

	public function delete($uri, $method)
	{
		// Process Wildcards
		$uri = $this->_process_wildcards($uri);

		$this->_routes['DELETE'][] = $uri;
		$this->_methods['DELETE'][] = $method;
	}

	public function set_404($method)
	{
		if(is_callable($method))
		{
			$this->_404 = $method;
		}
	}
	
	public function response()
	{				
		$request_uri = $_SERVER['REQUEST_URI'];
		$request_method = $_SERVER['REQUEST_METHOD'];

		if($request_method == 'POST' && isset($_POST['_method']))
		{
			$request_method = strtoupper($_POST['_method']);
		}
		
		if(!$this->_check_for_match($request_uri, $request_method))
		{
			$this->display_404();
		}
	}
	
	private function _check_for_match($request_uri, $request_method)
	{
		foreach($this->_routes[$request_method] as $key => $route)
		{
			if(preg_match("#^$route$#", $request_uri, $matches))
			{
				// Get the variables passed
				$input = $matches;
				unset($input[0]); // Remove the full pattern

				// Merge array to reset index
				$input = array_merge($input);

				// If a match is found call the matching method and return true
				call_user_func($this->_methods[$request_method][$key], $input);
				return true;
			}
		}
		
		// If no matches found return false
		return false;
	}
	
	private function _process_wildcards($uri)
	{
		// Number
		$pattern[] = '/:num/';
		$replacement[] = '[0-9]+';
		
		// Alpha
		$pattern[] = '/:alpha/';
		$replacement[] = '[a-zA-Z]+';
		
		return preg_replace($pattern, $replacement, $uri);
	}
	
	private function display_404()
	{
		// Set the HTTP status code
		header("HTTP/1.0 404 Not Found");
		
		// Show custom 404 if set or dispay the default
		if(is_null($this->_404))
		{
			$this->default_404();
		}
		else
		{
			call_user_func($this->_404);
		}
	}
	
	private function default_404()
	{
		echo '<h1>404 - Page could not be found!</h1>';
	}
}
