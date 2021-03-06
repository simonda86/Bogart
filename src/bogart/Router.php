<?php

namespace Bogart; 

/**
 * Bogart
 *
 * @author Simon Davies <hello@simon-davies.name>
 * @link http://simon-davies.name
 * @license http://opensource.org/licenses/mit-license.php
 *
 */
class Router
{
	private $_routes = array('GET' => array(), 'POST' => array(), 'PUT' => array(), 'DELETE' => array());
	private $_methods = array('GET' => array(), 'POST' => array(), 'PUT' => array(), 'DELETE' => array());
	private $_404 = null;
	
	/**
	 * Register a new GET Route
	 *
	 * @param string 		$uri the uri pattern
	 * @param callback 		$method the callback function for the route
	 * @return void
	 */
	public function get($uri, $method)
	{
		// Process Wildcards
		$uri = $this->_process_wildcards($uri);
		
		$this->_routes['GET'][] = $uri;
		$this->_methods['GET'][] = $method;
	}
	
	/**
	 * Register a new POST Route
	 *
	 * @param string 		$uri the uri pattern
	 * @param callback 		$method the callback function for the route
	 * @return void
	 */
	public function post($uri, $method)
	{
		// Process Wildcards
		$uri = $this->_process_wildcards($uri);
		
		$this->_routes['POST'][] = $uri;
		$this->_methods['POST'][] = $method;
	}

	/**
	 * Register a new PUT Route
	 *
	 * @param string 		$uri the uri pattern
	 * @param callback 		$method the callback function for the route
	 * @return void
	 */
	public function put($uri, $method)
	{
		// Process Wildcards
		$uri = $this->_process_wildcards($uri);

		$this->_routes['PUT'][] = $uri;
		$this->_methods['PUT'][] = $method;
	}

	/**
	 * Register a new DELETE Route
	 *
	 * @param string 		$uri the uri pattern
	 * @param callback 		$method the callback function for the route
	 * @return void
	 */
	public function delete($uri, $method)
	{
		// Process Wildcards
		$uri = $this->_process_wildcards($uri);

		$this->_routes['DELETE'][] = $uri;
		$this->_methods['DELETE'][] = $method;
	}

	/**
	 * Register a custom 404 error message
	 *
	 * @param callback 		$method the callback function
	 * @return void
	 */
	public function set_404($method)
	{
		if(is_callable($method))
		{
			$this->_404 = $method;
		}
	}
	
	/**
	 * Start Bogarts response to a request
	 *
	 * @return void
	 */
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
	
	/**
	 * Check if the request matches any registered routes
	 *
	 * @param string 		$request_uri
	 * @param string 		$request_method
	 * @return bool 		whether a match was found or not
	 */
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
	
	/**
	 * Replace uri wildcards with Regex
	 *
	 * @param string 		$uri
	 * @return void
	 */
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
	
	/**
	 * Display a 404 error message
	 *
	 * @return void
	 */
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
	
	/**
	 * This 404 error will be displayed if a custom one has not been set 
	 *
	 * @return void
	 */
	private function default_404()
	{
		echo '<h1>404 - Page could not be found!</h1>';
	}
}
