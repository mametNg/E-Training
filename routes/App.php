<?php
/**
* 
*/
namespace App;
use Controller\Controller;

class App extends Controller
{
	protected $controller = 'home';
	protected $method = 'index';
	protected $params = [];

	function __construct($controller=false, $method=false, $params=[])
	{	
		$url = $this->get_url();

		if (file_exists('app/controllers/'.str_replace("-", "_", ($url[0] ? (is_numeric(substr($url[0], 0,1)) ? "_":"").$url[0] : $controller)).'.php')) {

			$this->controller = str_replace("-", "_", ($url[0] ? (is_numeric(substr($url[0], 0,1)) ? "_":"").$url[0] : $controller));
			unset($controller);
			unset($url[0]);
		}

    	// controller
		include 'app/controllers/'. $this->controller .'.php';
		$this->controller = new $this->controller;
		
    	// method
		if (isset($url[1]) || $method) {
			if (method_exists($this->controller, str_replace("-", "_", (isset($url[1]) ? (is_numeric(substr($url[1], 0,1)) ? "_":"").$url[1] : $method)))) {
				$this->method = str_replace("-", "_", (isset($url[1]) ? (is_numeric(substr($url[1], 0,1)) ? "_":"").$url[1] : $method));
				unset($method);
				unset($url[1]);
			}
		}

   		// params
		if (!empty($url) || !empty($params)) {
			$this->params = array_values(($url ? $url : $params));
		}

    	// running controller and method + params
		call_user_func_array([$this->controller, $this->method], $this->params);
	}

}