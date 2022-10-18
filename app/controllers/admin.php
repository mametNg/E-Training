<?php

/**
* 
*/
use Controller\Controller;

class admin extends Controller
{
	public $menuActive = false;
	public $defaultFile = 'admin/body';
	
	function __construct()
	{
		$this->config();
		$this->DB = $this->model('db_models');
	}

	public function index()
	{	
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) header("location: ".$this->base_url('/admin/login'));
                if (!isset($_SESSION['username']) || empty($_SESSION['username'])) exit();

                $user = $this->DB->selectTB("db_admin", "id", $this->e($_SESSION['username']), true);

                $data = [
                       "header" => [
                              "title" => "Dashboard",
                              "img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
                              "desc" => "",
                      ],
                      "menu" => $this->DB->getAllTB("db_menu"),
                      "user" => ($user ? $user : false),
                      "users" => [
                              "lulus" => $this->DB->query("SELECT count(*) as total FROM tbl_nilai WHERE keterangan='Lulus'", 1),
                              "tidak_lulus" => $this->DB->query("SELECT count(*) as total FROM tbl_nilai WHERE keterangan='Lulus'", 1),
                              "total_user" => $this->DB->query("SELECT count(*) as total FROM tbl_nilai WHERE keterangan='Lulus'", 1),
                              "total_soal" => $this->DB->query("SELECT count(*) as total FROM tbl_nilai WHERE keterangan='Lulus'", 1),
                      ],
              ];
              // echo $this->w3llEncode(password_hash('N1141', PASSWORD_DEFAULT));

              $this->view("templates/admin/header", $data);
              $this->view("templates/admin/sidebar", $data);
              $this->view("templates/admin/topbar", $data);
              $this->view("admin/body", $data);
              $this->view("templates/admin/footer", $data);

	}

	public function manage_exam()
	{	
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) header("location: ".$this->base_url('/admin/login'));
        if (!isset($_SESSION['username']) || empty($_SESSION['username'])) exit();

        $user = $this->DB->selectTB("db_admin", "id", $this->e($_SESSION['username']), true);

        $url = $this->get_url();
        $menu = $this->DB->selectTB("db_menu", "url", "/".$url[1], true);

        $data = [
        	"header" => [
        		"title" => "Dashboard",
        		"img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
                "desc" => "",
        	],
        	"user" => ($user ? $user : false),
        	"menu" => $this->DB->getAllTB("db_menu"),
                // "soal" => $this->DB->query("SELECT * FROM db_quest ORDER BY id DESC LIMIT 10"),
                // "soal" => $this->DB->getAllTB("db_quest"),
                "soal" => [],
                "type" => [
                        "cat" => $this->DB->query("SELECT bu FROM db_quest WHERE BU!='' GROUP BY bu ASC"),
                ],
        ];
        
        $this->view("templates/admin/header", $data);
        $this->view("templates/admin/sidebar", $data);
        $this->view("templates/admin/topbar", $data);
        $this->view($menu['filename'], $data);
        $this->view("templates/admin/footer", $data);

	}

	public function exam_results()
	{	
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) header("location: ".$this->base_url('/admin/login'));
                if (!isset($_SESSION['username']) || empty($_SESSION['username'])) exit();

                $user = $this->DB->selectTB("db_admin", "id", $this->e($_SESSION['username']), true);

                $url = $this->get_url();
                $menu = $this->DB->selectTB("db_menu", "url", "/".$url[1], true);

                $data = [
                        "header" => [
                                "title" => "Dashboard",
                                "img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
                                "desc" => "",
                        ],
                        "user" => ($user ? $user : false),
                        "menu" => $this->DB->getAllTB("db_menu"),
                        "type" => [
                                "cat" => $this->DB->query("SELECT bu FROM db_quest WHERE BU!='' GROUP BY bu ASC"),
                                "quest" => [],
                        ],
                ];


                $this->view("templates/admin/header", $data);
                $this->view("templates/admin/sidebar", $data);
                $this->view("templates/admin/topbar", $data);
                $this->view($menu['filename'], $data);
                $this->view("templates/admin/footer", $data);

        }

        public function graphic()
        {
                if (!isset($_SESSION['username']) || empty($_SESSION['username'])) header("location: ".$this->base_url('/admin/login'));
                if (!isset($_SESSION['username']) || empty($_SESSION['username'])) exit();

                $user = $this->DB->selectTB("db_admin", "id", $this->e($_SESSION['username']), true);

                $url = $this->get_url();
                $menu = $this->DB->selectTB("db_menu", "url", "/".$url[1], true);

                $data = [
                        "header" => [
                                "title" => "Dashboard",
                                "img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
                                "desc" => "",
                        ],
                        "user" => ($user ? $user : false),
                        "users" => $this->DB->selectTB("db_members", "flag", 0),
                        "menu" => $this->DB->getAllTB("db_menu"),
                        "type" => [
                                "cat" => $this->DB->query("SELECT bu FROM db_quest WHERE BU!='' GROUP BY bu ASC"),
                                "quest" => [],
                        ],
                ];


                $this->view("templates/admin/header", $data);
                $this->view("templates/admin/sidebar", $data);
                $this->view("templates/admin/topbar", $data);
                $this->view($menu['filename'], $data);
                $this->view("templates/admin/footer", $data);

        }

	public function test_results_per_users()
	{	
                if (!isset($_SESSION['username']) || empty($_SESSION['username'])) header("location: ".$this->base_url('/admin/login'));
                if (!isset($_SESSION['username']) || empty($_SESSION['username'])) exit();

                $user = $this->DB->selectTB("db_admin", "id", $this->e($_SESSION['username']), true);

                $url = $this->get_url();
                $menu = $this->DB->selectTB("db_menu", "url", "/".$url[1], true);

                $data = [
                        "header" => [
                                "title" => "Dashboard",
                                "img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
                                "desc" => "",
                        ],
                        "user" => ($user ? $user : false),
                        "menu" => $this->DB->getAllTB("db_menu"),
                        "type" => [
                                "cat" => $this->DB->query("SELECT bu FROM db_quest WHERE BU!='' GROUP BY bu ASC"),
                                "quest" => [],
                        ],
                ];

                $this->view("templates/admin/header", $data);
                $this->view("templates/admin/sidebar", $data);
                $this->view("templates/admin/topbar", $data);
                $this->view($menu['filename'], $data);
                $this->view("templates/admin/footer", $data);

        }

	public function exam_settings()
	{	
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) header("location: ".$this->base_url('/admin/login'));
        if (!isset($_SESSION['username']) || empty($_SESSION['username'])) exit();

        $user = $this->DB->selectTB("db_admin", "id", $this->e($_SESSION['username']), true);

        $url = $this->get_url();
        $menu = $this->DB->selectTB("db_menu", "url", "/".$url[1], true);

        $data = [
        	"header" => [
        		"title" => "Dashboard",
        		"img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
                "desc" => "",
        	],
        	"user" => ($user ? $user : false),
        	"menu" => $this->DB->getAllTB("db_menu"),
                "regulation" => $this->DB->selectTB("db_regulation", "id", 1, true),
        ];
        
        $this->view("templates/admin/header", $data);
        $this->view("templates/admin/sidebar", $data);
        $this->view("templates/admin/topbar", $data);
        $this->view($menu['filename'], $data);
        $this->view("templates/admin/footer", $data);

	}

	public function guide()
	{	
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) header("location: ".$this->base_url('/admin/login'));
        if (!isset($_SESSION['username']) || empty($_SESSION['username'])) exit();

        $user = $this->DB->selectTB("db_admin", "id", $this->e($_SESSION['username']), true);

        $url = $this->get_url();
        $menu = $this->DB->selectTB("db_menu", "url", "/".$url[1], true);

        $data = [
        	"header" => [
        		"title" => "Dashboard",
        		"img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
                "desc" => "",
        	],
        	"user" => ($user ? $user : false),
        	"menu" => $this->DB->getAllTB("db_menu"),
        ];
        
        $this->view("templates/admin/header", $data);
        $this->view("templates/admin/sidebar", $data);
        $this->view("templates/admin/topbar", $data);
        $this->view($menu['filename'], $data);
        $this->view("templates/admin/footer", $data);

	}

	public function user_list($method=false, $param=false)
	{	
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) header("location: ".$this->base_url('/admin/login'));
        if (!isset($_SESSION['username']) || empty($_SESSION['username'])) exit();

        $user = $this->DB->selectTB("db_admin", "id", $this->e($_SESSION['username']), true);

        $url = $this->get_url();
        $menu = $this->DB->selectTB("db_menu", "url", "/".$url[1], true);

        $data = [
        	"header" => [
        		"title" => "Dashboard",
        		"img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
                "desc" => "",
        	],
        	"user" => ($user ? $user : false),
        	"menu" => $this->DB->getAllTB("db_menu"),
                "users" => $this->DB->selectTB("db_members", "flag", 0),
        ];
        
        $this->view("templates/admin/header", $data);
        $this->view("templates/admin/sidebar", $data);
        $this->view("templates/admin/topbar", $data);
        $this->view($menu['filename'], $data);
        $this->view("templates/admin/footer", $data);

	}

	public function upload_training_materials()
	{	
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) header("location: ".$this->base_url('/admin/login'));
        if (!isset($_SESSION['username']) || empty($_SESSION['username'])) exit();

        $user = $this->DB->selectTB("db_admin", "id", $this->e($_SESSION['username']), true);

        $url = $this->get_url();
        $menu = $this->DB->selectTB("db_menu", "url", "/".$url[1], true);

        $data = [
        	"header" => [
        		"title" => "Dashboard",
        		"img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
                "desc" => "",
        	],
        	"user" => ($user ? $user : false),
        	"menu" => $this->DB->getAllTB("db_menu"),
        ];
        
        $this->view("templates/admin/header", $data);
        $this->view("templates/admin/sidebar", $data);
        $this->view("templates/admin/topbar", $data);
        $this->view($menu['filename'], $data);
        $this->view("templates/admin/footer", $data);

	}

        public function logout()
        {
                if (isset($_SESSION['username']) && !empty($_SESSION['username'])) unset($_SESSION['username']);
                // if (isset($_SESSION['quest']) && !empty($_SESSION['quest'])) unset($_SESSION['quest']);

                header("location: ".$this->base_url("admin/login"));
        }

	public function login($uname=false)
	{

		if (isset($_SESSION['username']) && !empty($_SESSION['username'])) header("location: ".$this->base_url('/admin'));
        if (isset($_SESSION['username']) && !empty($_SESSION['username'])) exit();
		## data form file
		$data = [
			"header" => [
				"title" => "Welcome to the Online Exam App",
				"img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
				"desc" => "Welcome to the Online Exam App",
				"brand" => " back Admin!",
			],
			"user" => $this->e($uname),
		];

		## singel call file
		// $this->view("admin/login", $data);

		$dataFiles = [
			"templates/login/header",
			"admin/login",
			"templates/login/footer",
		];

		## multiple call file
		$this->views($dataFiles, $data);	
	}
}