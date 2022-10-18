<?php

/**
 * USER ONLY
 */

use Controller\Controller;

class v1 extends Controller
{
	
	function __construct()
	{
		$this->DB = (in_array("db_models", get_declared_classes()) ? new db_models():$this->model('db_models'));
		$this->config();
		$this->Request = $this->helper("Request"); 
	}

	public function login()
	{
		// Check session login
		if (isset($_SESSION['member_name']) && !empty($_SESSION['member_name'])) $this->printJson($this->invalid(false, 403, "This session already exist!", ["url" => $this->base_url().""]));

		// get request
		$params = $this->Request->get();

		// Filter username
		if (!isset($params['uname']) || empty($params['uname'])) $this->printJson($this->invalid(false, 403, "This username cannot be empty!"));

		// Filter Password
		if (!isset($params['password']) || empty($params['password'])) $this->printJson($this->invalid(false, 403, "This password cannot be empty!"));

		// Get User
		// $user = $this->DB->selectTB("db_members", "id", $this->e($params['uname']), true);
		$user = $this->DB->query("SELECT * FROM db_members WHERE flag='0' AND id='".$this->e($params['uname'])."'", 1);

		// Login Failed
		if (!$user) $this->printJson($this->invalid(false, 403, "This username isn't registered"));
		
		// Valid password
		if ($this->e($params['password']) !== $user['password']) $this->printJson($this->invalid(false, 403, "Wrong Password"));

		// Create session login
		$_SESSION['member_name'] = $this->e($user['id']);

		// Login success
		$response = $this->invalid(true, 200, "Login Success");
		$this->printJson($response);
	}

	public function catBu()
	{
		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) $this->printJson($this->invalid());

		// get request
		$params = $this->Request->get();

		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
		
		$bu = $this->w3llDecode($this->e($params['bu']));

		if (!$bu) $this->printJson($this->invalid(false, 403, "This bu isn't valid!"));

		$catBu = $this->DB->query("SELECT area FROM db_quest WHERE BU='$bu' GROUP BY area");

		if (!$catBu) $this->printJson($this->invalid(false, 403, "Bu category not found!"));
		$this->printJson($this->invalid(true, 200, "ok", $catBu));
	}

	public function start()
	{
		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) $this->printJson($this->invalid());
		if (isset($_SESSION['quest']['bu']) && isset($_SESSION['quest']['area'])) $this->printJson($this->invalid());

		// get request
		$params = $this->Request->get();

		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu category cannot be empty!"));
		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
		if (!isset($params['checked']) || empty($params['checked'])) $this->printJson($this->invalid(false, 403, "This checked cannot be empty!"));

		$bu = $this->w3llDecode($this->e($params['bu']));

		if (!$bu) $this->printJson($this->invalid(false, 403, "This bu isn't valid!"));

		$getQuest = $this->DB->query("SELECT cat FROM db_quest WHERE bu='". $this->e($bu) ."' AND area='". $this->e($params['area']) ."'");

		if (!$getQuest) $this->printJson($this->invalid(false, 403, "Access Denied!"));

		$stat = $this->DB->query("SELECT * FROM db_results WHERE username='". $this->e($_SESSION['member_name']) ."' AND bu='". $this->e($bu) ."' and area='". $this->e($params['area']) ."' and keterangan='Lulus'", true);

		if ($stat) $this->printJson($this->invalid(false, 403, "Access Denied!, your after success"));

		$regulation = $this->DB->selectTB("db_regulation", 'id', 1, 1);
		
		// RESET PER 6 BULAN (ON PROGRESS)
		$exp = 14515200;

		$checked = $this->DB->query("
			SELECT
				sid,
				created
			FROM 
				db_start_test 
			WHERE 
				uid='". $this->e($_SESSION['member_name']) ."' 
				AND bu='". $this->e($bu) ."' 
				AND area='". $this->e($params['area']) ."' 
				AND flag='1'
				AND is_delt='0'
				AND acc='1'
		");

		if ($checked) if (count($checked) >= 1 ) if ((end($checked)['created']+$exp) >= time()) $this->printJson($this->invalid(false, 403, "Limit max 1 try!"));

		$dataScores = $this->DB->query("
	        SELECT
				a.sid,
				a.uid,
				a.qid,
				a.answer,
				b.answer_key,
				c.min_value
			FROM 
				db_answer a,
				db_quest b,
				db_start_test c
			WHERE 
				a.uid = '". $this->e($_SESSION['member_name']) ."' 
				AND a.bu ='". $this->e($bu) ."' 
				AND a.area ='". $this->e($params['area']) ."' 
				AND a.qid = b.id
				AND a.flag ='1'
				AND c.is_delt='0'
				AND c.acc='1'
				AND a.sid = c.sid
		");

		if ($dataScores) {
			$val = 0;
			foreach ($dataScores as $key => $score) if ($score['answer_key'] == $score['answer']) $val++;


			if ($dataScores['min_value'] <= $this->e(substr((100/count($dataScores)*$val), 0,3), ".")) $this->printJson($this->invalid(false, 403, "Sudah Lulus!"));
		}
		
		$now = time();

		$setStr = [
			"sid" => ($_SESSION['member_name'] + $now),
			"uid" => $this->e($_SESSION['member_name']),
			"bu" => $this->e($bu),
			"area" => $this->e($params['area']),
			"min_value" => $this->e($regulation['min_val']),
			"flag" => 0,
			"try" => 0,
			"acc" => 0,
			"starttime" => $now,
			"endtime" => $now+(60*$regulation['timer']),
			"created" => $now,
		];

		$inst = $this->DB->insertTB("db_start_test", $setStr);
		if (!$inst) $this->printJson($this->invalid(false, 403, "Time out"));

		$_SESSION['quest']['sid'] = ($_SESSION['member_name'] + $now);
		$_SESSION['quest']['bu'] = $this->e($bu);
		$_SESSION['quest']['area'] = $this->e($params['area']);
		$_SESSION['quest']['starttime'] = $now;
		$_SESSION['quest']['wave'] = 1;
		$_SESSION['quest']['endtime'] = $now+(60*$regulation['timer']);
		$this->printJson($this->invalid(true, 200, "ok", ['url'=> $this->base_url('test')]));
	}
}