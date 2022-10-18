<?php

/**
* 
*/
use Controller\Controller;

class auth extends Controller
{
	
	function __construct()
	{
		$this->DB = $this->model('db_models');
		$this->config();
	}

	public function index()
	{
		$this->printJson($this->invalid());
		// header("location:".$this->base_url('auth/login'));
	}

	public function api($apis=false, $method=false, $option=false)
	{
		if (!file_exists('app/api/'.$this->e($apis).'.php')) $this->printJson($this->invalid());

		$api = $this->authApi($this->e($apis));

		if (in_array($this->e($method), get_class_methods($api)) == false) $this->printJson($this->invalid());

		$api->$method($option);
	}

	public function login($uname=false)
	{
		if (isset($_SESSION['member_name']) && !empty($_SESSION['member_name'])) header("location: ".$this->base_url());
        if (isset($_SESSION['member_name']) && !empty($_SESSION['member_name'])) exit();
		## data form file
		$data = [
			"header" => [
				"title" => "Welcome to the Online Exam App",
				"img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
				"desc" => "Welcome to the Online Exam App",
				"brand" => " to the Online Exam App",
			],
			"user" => $this->e($uname),
		];

		## singel call file
		$this->view("templates/login/header", $data);
		$this->view("home/login", $data);
		$this->view("templates/login/footer", $data);

		## multiple call file
		// $this->views(["welcome"], $data);	
	}

	public function logout()
	{
		if (isset($_SESSION['member_name']) && !empty($_SESSION['member_name'])) unset($_SESSION['member_name']);
		if (isset($_SESSION['quest']) && !empty($_SESSION['quest'])) unset($_SESSION['quest']);

		header("location: ".$this->base_url('auth/login'));
	}
}