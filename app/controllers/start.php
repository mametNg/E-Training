<?php

/**
* 
*/
use Controller\Controller;

class start extends Controller
{
	public $menuActive = false;
	public $defaultFile = 'home/body';
	public $def = "000";
	
	function __construct()
	{
		$this->config();
		$this->DB = $this->model('db_models');
	}

	public function index()
	{	
		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) header("location: ".$this->base_url('/auth/login'));
		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) exit();
		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) header("location: ".$this->base_url());
		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) exit();
		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) header("location: ".$this->base_url());
		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) exit();
		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) header("location: ".$this->base_url());
		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) exit();
		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) header("location: ".$this->base_url());
		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) exit();
		if (isset($_SESSION['quest']['collect']) && !empty($_SESSION['quest']['collect'])) header("location: ".$this->base_url("start/preview"));
		if (isset($_SESSION['quest']['collect']) && !empty($_SESSION['quest']['collect'])) exit();
		if (!isset($_SESSION['quest']['rand'])) $_SESSION['quest']['rand'] = (mt_rand() / mt_getrandmax());
		$data = [
			"header" => [
				"title" => "Dashboard",
				"img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
				"desc" => "",
			],
			"user" => $this->DB->selectTB("db_members", "id", $this->e($_SESSION['member_name']), true),
			"regualation" => $this->DB->selectTB("db_regulation", "id", 1, true),
			"quest" => $this->DB->query("
				SELECT 
				id, quest, image, quest_a, quest_b, quest_c, quest_d, cat, answer_key, RAND() rand
				FROM db_quest
				WHERE flag='0' AND status='1' AND bu='". $_SESSION['quest']['bu'] ."'
				AND area='". $_SESSION['quest']['area'] ."' 
				ORDER BY RAND(".$_SESSION['quest']['rand'].")
				"),
			"load" => $this->DB->query("SELECT qid, answer from db_answer where sid='". $this->e($_SESSION['quest']['sid']) ."' AND wave='". $this->e($_SESSION['quest']['wave']) ."' AND flag='0'"),
			"finish" => $this->DB->query("SELECT qid, answer from db_answer where sid='". $this->e($_SESSION['quest']['sid']) ."' AND wave='". $this->e($_SESSION['quest']['wave']) ."' AND flag='1'"),
			"session" => $this->DB->query("SELECT * from db_start_test where sid='". $this->e($_SESSION['quest']['sid']) ."' AND uid='". $this->e($_SESSION['member_name']) ."'", 1),
		];

		if (!$data['session']) header("location: ".$this->base_url('/auth/logout'));
		if (!$data['session']) exit();

		if (!isset($_SESSION['quest']['rand'])) $_SESSION['quest']['rand'] = $data['quest'][0]['rand'];

		for ($i=0; $i < count($data['quest']); $i++) { 
			$data['quest'][$i]['quest'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest'])));
			$data['quest'][$i]['quest_a'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest_a'])));
			$data['quest'][$i]['quest_b'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest_b'])));
			$data['quest'][$i]['quest_c'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest_c'])));
			$data['quest'][$i]['quest_d'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest_d'])));
		}

		// echo hex2bin(bin2hex("±"));
		// echo hex2bin(bin2hex("°"));
		// die;

		$this->view("templates/home/header", $data);
		$this->view("templates/home/topbar", $data);
		$this->view("home/quest", $data);
		$this->view("templates/home/footer", $data);
	}

	public function preview()
	{
		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) header("location: ".$this->base_url('/auth/login'));
		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) exit();
		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) header("location: ".$this->base_url());
		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) exit();
		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) header("location: ".$this->base_url());
		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) exit();
		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) header("location: ".$this->base_url());
		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) exit();
		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) header("location: ".$this->base_url());
		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) exit();
		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) header("location: ".$this->base_url());
		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) exit();
		if (!isset($_SESSION['quest']['collect']) || empty($_SESSION['quest']['collect'])) header("location: ".$this->base_url('start'));
		if (!isset($_SESSION['quest']['collect']) || empty($_SESSION['quest']['collect'])) exit();

		$data = [
			"header" => [
	        		"title" => "Dashboard",
	        		"img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
	                "desc" => "",
	        	],
	        	"user" => $this->DB->selectTB("db_members", "id", $this->e($_SESSION['member_name']), true),
	        	// "result" => $this->DB->selectTB("db_answer", "sid", $this->e($_SESSION['quest']['sid'])),
	        	"regualation" => $this->DB->selectTB("db_regulation", "id", 1, true),
	        	"result" => $this->DB->query("
	        		SELECT
	        		a.qid,
	        		a.sid,
	        		b.quest,
	        		b.image,
	        		a.answer,
	        		b.answer_key,
	        		a.bu,
	        		a.area,
	        		b.cat
	        		FROM db_answer a, db_quest_results b
	        		WHERE a.bu = b.bu
	        		AND a.area = b.area
	        		AND a.qid = b.qid
	        		AND a.sid = b.sid
	        		AND a.uid = b.uid
	        		AND a.sid='". $this->e($_SESSION['quest']['sid']) ."'
	        		AND a.wave='". $this->e($_SESSION['quest']['wave']) ."'
	        		AND a.flag = '1'
	        		AND a.bu = '". $this->e($_SESSION['quest']['bu']) ."'
	        		AND a.area = '". $this->e($_SESSION['quest']['area']) ."'
	        		AND a.wave = '". $this->e($_SESSION['quest']['wave']) ."'
	        	"),
	        	"results" => [],
		];

		$data['result'] = $this->array_group($data['result'], "cat");

		$i=0;
		foreach ($data['result'] as $key => $value) {
			$x=0;
			$score = 0;
			$unScore = 0;

			$data['results'][$i]['cat'] = $key;
			$data['results'][$i]['score'] = $score;
			$data['results'][$i]['true'] = $score;
			$data['results'][$i]['false'] = $unScore;

			foreach ($value as $valc) {
				$data['results'][$i]['bu'] = $value[$x]['bu'];
				$data['results'][$i]['area'] = $value[$x]['area'];
				
				unset($value[$x]['bu']);
				unset($value[$x]['cat']);
				unset($value[$x]['area']);

				if ($valc['answer_key'] == $valc['answer']) $score++;
				if ($valc['answer_key'] !== $valc['answer']) $unScore++;
				$x++;
			}

			$data['results'][$i]['score'] = $this->e(substr((100/count($value)*$score), 0,3), ".");
			$data['results'][$i]['true'] = $score;
			$data['results'][$i]['false'] = $unScore;
			$data['results'][$i]['desc'] = ((100/count($value)*$score)>=$data['regualation']['min_val'] ? "Congrats.":"Failed");
			$data['results'][$i]['quest'] = $value;

			$i++;
		}

		// die;
		// $this->printJson($data['results']);

		$this->view("templates/home/header", $data);
	    // $this->view("templates/home/sidebar", $data);
		$this->view("templates/home/topbar", $data);
		$this->view("home/preview", $data);
		$this->view("templates/home/footer", $data);
	}

	public function review($id=false, $sid=false,$bu=false, $area=false, $cmd=false)
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) header("location: ".$this->base_url('/admin/login'));
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) exit();

		if (!$this->e($id) || !$this->e($sid) || !$this->e($bu) || !$this->e($area) || !$this->e($cmd)) $this->printJson($this->invalid());
		if ($this->e($cmd) !== "1") $this->printJson($this->invalid()); 

		$user = $this->DB->selectTB("db_admin", "id", $this->e($_SESSION['username']), true);
		if (!$user) $this->printJson($this->invalid()); 

		$data = [
			"header" => [
				"title" => "Dashboard",
				"img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
				"desc" => "",
			],
			"user" => $this->DB->selectTB("db_members", "TO_BASE64(id)", $this->e($id), true),
			"regualation" => $this->DB->selectTB("db_regulation", "id", 1, true),
			"quest" => $this->DB->query("
				SELECT 
				qid, quest, image, quest_a, quest_b, quest_c, quest_d, cat, answer_key
				FROM db_quest_results
				WHERE TO_BASE64(bu)='". $this->e($bu) ."'
				AND TO_BASE64(area)='". $this->e($area) ."' 
				AND TO_BASE64(sid)='". $this->e($sid) ."' 
				AND TO_BASE64(uid)='". $this->e($id) ."' 
				ORDER BY RAND()
				"),
			"load" => $this->DB->query("SELECT qid, answer, wave from db_answer where wave = (SELECT max(wave) from db_answer where TO_BASE64(sid)='". $this->e($sid) ."' AND TO_BASE64(uid)='". $this->e($id) ."' AND flag='0') AND TO_BASE64(sid)='". $this->e($sid) ."' AND TO_BASE64(uid)='". $this->e($id) ."' AND flag='0'"),
			"finish" => $this->DB->query("SELECT qid, answer, wave from db_answer where wave = (SELECT max(wave) from db_answer where TO_BASE64(sid)='". $this->e($sid) ."' AND TO_BASE64(uid)='". $this->e($id) ."' AND flag='1') AND TO_BASE64(sid)='". $this->e($sid) ."' AND TO_BASE64(uid)='". $this->e($id) ."' AND flag='1'"),
			"session" => $this->DB->query("SELECT * from db_start_test where TO_BASE64(sid)='". $this->e($sid) ."' AND TO_BASE64(uid)='". $this->e($id) ."'", 1),
		];
		
		$data['score'] = [
			'true' => 0,
			'false' => 0,
		];

		for ($i=0; $i < count($data['quest']); $i++) { 
			$data['quest'][$i]['quest'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest'])));
			$data['quest'][$i]['quest_a'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest_a'])));
			$data['quest'][$i]['quest_b'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest_b'])));
			$data['quest'][$i]['quest_c'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest_c'])));
			$data['quest'][$i]['quest_d'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest_d'])));
			if ($data['quest'][$i]['answer_key'] == strtoupper($data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]['answer'])) $data['score']['true']++;
			if ($data['quest'][$i]['answer_key'] !== strtoupper($data['finish'][array_search($data['quest'][$i]['qid'] , array_column($data['finish'], 'qid'))]['answer'])) $data['score']['false']++;
		}


		$this->view("templates/home/header", $data);
		$this->view("templates/home/topbar", $data);
		$this->view("home/review", $data);
		$this->view("templates/home/footer", $data);

	}
}

// /**
// * 
// */
// use Controller\Controller;

// class start extends Controller
// {
// 	public $menuActive = false;
// 	public $defaultFile = 'home/body';
// 	public $def = "000";
	
// 	function __construct()
// 	{
// 		$this->config();
// 		$this->DB = $this->model('db_models');
// 	}

// 	public function index()
// 	{	
// 		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) header("location: ".$this->base_url('/auth/login'));
// 		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) exit();
// 		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) header("location: ".$this->base_url());
// 		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) exit();
// 		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) header("location: ".$this->base_url());
// 		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) exit();
// 		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) header("location: ".$this->base_url());
// 		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) exit();
// 		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) header("location: ".$this->base_url());
// 		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) exit();
// 		if (isset($_SESSION['quest']['collect']) && !empty($_SESSION['quest']['collect'])) header("location: ".$this->base_url("start/preview"));
// 		if (isset($_SESSION['quest']['collect']) && !empty($_SESSION['quest']['collect'])) exit();

// 		$data = [
// 			"header" => [
// 				"title" => "Dashboard",
// 				"img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
// 				"desc" => "",
// 			],
// 			"user" => $this->DB->selectTB("db_members", "id", $this->e($_SESSION['member_name']), true),
// 			"regualation" => $this->DB->selectTB("db_regulation", "id", 1, true),
// 			"quest" => $this->DB->query("
// 				SELECT 
// 				id, quest, image, quest_a, quest_b, quest_c, quest_d, cat, answer_key
// 				FROM db_quest
// 				WHERE flag='0' AND status='1' AND bu='". $_SESSION['quest']['bu'] ."'
// 				AND area='". $_SESSION['quest']['area'] ."' 
// 				ORDER BY RAND ()
// 				"),
// 			"load" => $this->DB->query("SELECT qid, answer from db_answer where sid='". $this->e($_SESSION['quest']['sid']) ."' AND wave='". $this->e($_SESSION['quest']['wave']) ."' AND flag='0'"),
// 			"finish" => $this->DB->query("SELECT qid, answer from db_answer where sid='". $this->e($_SESSION['quest']['sid']) ."' AND wave='". $this->e($_SESSION['quest']['wave']) ."' AND flag='1'"),
// 			"session" => $this->DB->query("SELECT * from db_start_test where sid='". $this->e($_SESSION['quest']['sid']) ."' AND uid='". $this->e($_SESSION['member_name']) ."'", 1),
// 		];

// 		if (!$data['session']) header("location: ".$this->base_url('/auth/logout'));
// 		if (!$data['session']) exit();

// 		for ($i=0; $i < count($data['quest']); $i++) { 
// 			$data['quest'][$i]['quest'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest'])));
// 			$data['quest'][$i]['quest_a'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest_a'])));
// 			$data['quest'][$i]['quest_b'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest_b'])));
// 			$data['quest'][$i]['quest_c'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest_c'])));
// 			$data['quest'][$i]['quest_d'] = hex2bin(strip_tags($this->e($data['quest'][$i]['quest_d'])));
// 		}

// 		// echo hex2bin(bin2hex("±"));
// 		// echo hex2bin(bin2hex("°"));
// 		// die;

// 		$this->view("templates/home/header", $data);
// 		$this->view("templates/home/topbar", $data);
// 		$this->view("home/quest", $data);
// 		$this->view("templates/home/footer", $data);
// 	}

// 	public function preview()
// 	{
// 		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) header("location: ".$this->base_url('/auth/login'));
// 		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) exit();
// 		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) header("location: ".$this->base_url());
// 		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) exit();
// 		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) header("location: ".$this->base_url());
// 		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) exit();
// 		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) header("location: ".$this->base_url());
// 		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) exit();
// 		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) header("location: ".$this->base_url());
// 		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) exit();
// 		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) header("location: ".$this->base_url());
// 		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) exit();
// 		if (!isset($_SESSION['quest']['collect']) || empty($_SESSION['quest']['collect'])) header("location: ".$this->base_url('start'));
// 		if (!isset($_SESSION['quest']['collect']) || empty($_SESSION['quest']['collect'])) exit();

// 		$data = [
// 			"header" => [
// 	        		"title" => "Dashboard",
// 	        		"img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
// 	                "desc" => "",
// 	        	],
// 	        	"user" => $this->DB->selectTB("db_members", "id", $this->e($_SESSION['member_name']), true),
// 	        	// "result" => $this->DB->selectTB("db_answer", "sid", $this->e($_SESSION['quest']['sid'])),
// 	        	"regualation" => $this->DB->selectTB("db_regulation", "id", 1, true),
// 	        	"result" => $this->DB->query("
// 	        		SELECT
// 	        		a.qid,
// 	        		a.sid,
// 	        		b.quest,
// 	        		b.image,
// 	        		a.answer,
// 	        		b.answer_key,
// 	        		a.bu,
// 	        		a.area,
// 	        		b.cat
// 	        		FROM db_answer a, db_quest b
// 	        		WHERE a.bu = b.bu
// 	        		AND a.area = b.area
// 	        		AND a.qid = b.id
// 	        		AND a.sid='". $this->e($_SESSION['quest']['sid']) ."'
// 	        		AND a.wave='". $this->e($_SESSION['quest']['wave']) ."'
// 	        		AND a.flag = '1'
// 	        		AND b.flag = '0'
// 	        		AND a.bu = '". $this->e($_SESSION['quest']['bu']) ."'
// 	        		AND a.area = '". $this->e($_SESSION['quest']['area']) ."'
// 	        		AND a.wave = '". $this->e($_SESSION['quest']['wave']) ."'
// 	        	"),
// 	        	"results" => [],
// 		];

// 		$data['result'] = $this->array_group($data['result'], "cat");

// 		$i=0;
// 		foreach ($data['result'] as $key => $value) {
// 			$x=0;
// 			$score = 0;
// 			$unScore = 0;

// 			$data['results'][$i]['cat'] = $key;
// 			$data['results'][$i]['score'] = $score;
// 			$data['results'][$i]['true'] = $score;
// 			$data['results'][$i]['false'] = $unScore;

// 			foreach ($value as $valc) {
// 				$data['results'][$i]['bu'] = $value[$x]['bu'];
// 				$data['results'][$i]['area'] = $value[$x]['area'];
				
// 				unset($value[$x]['bu']);
// 				unset($value[$x]['cat']);
// 				unset($value[$x]['area']);

// 				if ($valc['answer_key'] == $valc['answer']) $score++;
// 				if ($valc['answer_key'] !== $valc['answer']) $unScore++;
// 				$x++;
// 			}

// 			$data['results'][$i]['score'] = $this->e(substr((100/count($value)*$score), 0,3), ".");
// 			$data['results'][$i]['true'] = $score;
// 			$data['results'][$i]['false'] = $unScore;
// 			$data['results'][$i]['desc'] = ((100/count($value)*$score)>=$data['regualation']['min_val'] ? "Congrats.":"Failed");
// 			$data['results'][$i]['quest'] = $value;

// 			$i++;
// 		}

// 		// die;
// 		// $this->printJson($data['results']);

// 		$this->view("templates/home/header", $data);
// 	    // $this->view("templates/home/sidebar", $data);
// 		$this->view("templates/home/topbar", $data);
// 		$this->view("home/preview", $data);
// 		$this->view("templates/home/footer", $data);
// 	}
// }