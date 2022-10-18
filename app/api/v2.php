<?php

/**
 * API PUBLIC
 */

use Controller\Controller;

class v2 extends Controller
{
	
	function __construct()
	{
		$this->DB = (in_array("db_models", get_declared_classes()) ? new db_models():$this->model('db_models'));
		$this->config();
		$this->Request = $this->helper("Request"); 
	}

	public function saving()
	{
		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) $this->printJson($this->invalid());

		$params = $this->Request->get();
		$getQuest = $this->DB->query("
			SELECT 
			* 
			FROM db_quest 
			WHERE status='1' 
			AND id='". $this->e($params['id']) ."' 
			AND bu='". $this->e($_SESSION['quest']['bu']) ."' 
			AND area='". $this->e($_SESSION['quest']['area']) ."' 
			AND cat='". $this->e($params['cat']) ."'
		", 1);

		if (!$getQuest) $this->printJson($this->invalid());

		$dataTable = [
			"sid" => $this->e($_SESSION['quest']['sid']),
			"uid" => $this->e($_SESSION['member_name']),
			"qid" => $this->e($params['id']),
			"answer" => $this->e($params['input']),
			"bu" => $this->e($_SESSION['quest']['bu']),
			"area" => $this->e($_SESSION['quest']['area']),
			"wave" => $this->e($_SESSION['quest']['wave']),
			"cat" => $this->e($params['cat']),
			"flag" => "0",
			"created" => time(),
		];

		$questSave = [
			"sid" => $this->e($_SESSION['quest']['sid']),
			"uid" => $this->e($_SESSION['member_name']),
			"qid" => $this->e($params['id']),
			"quest" => $this->e($getQuest['quest']),
			"quest_a" => $this->e($getQuest['quest_a']),
			"quest_b" => $this->e($getQuest['quest_b']),
			"quest_c" => $this->e($getQuest['quest_c']),
			"quest_d" => $this->e($getQuest['quest_d']),
			"answer_key" => $this->e($getQuest['answer_key']),
			"image" => $this->e($getQuest['image']),
			"bu" => $this->e($getQuest['bu']),
			"area" => $this->e($getQuest['area']),
			"cat" => $this->e($getQuest['cat']),
			"created" => time(),
		];

		$getAnswer = $this->DB->query("
			SELECT 
			a.id,
			a.answer,
			b.id questid
			FROM 
			db_answer a, db_quest_results b
			WHERE a.sid='". $this->e($dataTable['sid']) ."' 
			AND a.uid='". $this->e($dataTable['uid']) ."' 
			AND a.wave='". $this->e($dataTable['wave']) ."' 
			AND a.qid='". $this->e($dataTable['qid']) ."' 
			AND b.sid = a.sid
			AND b.uid = a.uid
			AND b.qid = a.qid
			AND a.flag='0'", 1);

		if (!$getAnswer) {
			$this->DB->insertTB("db_answer", $dataTable);
			$this->DB->insertTB("db_quest_results", $questSave);
		}
		
		if (isset($getAnswer['answer']) && $getAnswer['answer'] !== $dataTable['answer']) {
			$this->DB->updateTB("db_answer", $dataTable , "id", $getAnswer['id']);
			$this->DB->updateTB("db_quest_results", $questSave , "id", $getAnswer['questid']);
		}
	}

	public function collect()
	{
		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) $this->printJson($this->invalid());
	
		if (!isset($_POST['answer']) && empty($_POST['answer'])) $this->printJson($this->invalid());
		$result = null;

		$startTest = $this->DB->query("
			SELECT
				*
			FROM
				db_start_test
			WHERE
				sid = '". $this->e($_SESSION['quest']['sid']) ."'
				AND uid = '". $this->e($_SESSION['member_name']) ."'
				AND bu = '". $this->e($_SESSION['quest']['bu']) ."'
				AND area = '". $this->e($_SESSION['quest']['area']) ."'
				AND flag = '0'
		", true);

		if (!$startTest) $this->printJson($this->invalid(false, 404 ,"User not found!"));

		// Check Relation Quest
		for ($i=0; $i < count($_POST['answer']); $i++) { 

			$dataTable = [
				"sid" => $this->e($_SESSION['quest']['sid']),
				"uid" => $this->e($_SESSION['member_name']),
				"qid" => $this->e($_POST['answer'][$i]['id']),
				"answer" => $this->e($_POST['answer'][$i]['ans']),
				"bu" => $this->e($_SESSION['quest']['bu']),
				"area" => $this->e($_SESSION['quest']['area']),
				"wave" => $this->e($_SESSION['quest']['wave']),
				"flag" => "1",
			];

			$getAnswer = $this->DB->query("SELECT * FROM db_answer WHERE sid='". $this->e($dataTable['sid']) ."' AND wave='". $this->e($dataTable['wave']) ."' AND uid='". $this->e($dataTable['uid']) ."' AND qid='". $this->e($dataTable['qid']) ."'", 1);
			
			if (!$getAnswer) $this->printJson($this->invalid(false, 404 ,"Data not found!"));

			if (isset($getAnswer['answer']) && isset($getAnswer['flag']) && $getAnswer['flag'] == "0") {
				$update = $this->DB->updateTB("db_answer", $dataTable , "id", $getAnswer['id']);
				$result = ($update ? true:false);
				if (!$result) break;
				// $result = true;
			}
		}

		if ($result !== true) $this->printJson($this->invalid(false, 404 ,"Error data!"));

		$dataTable = [
			"flag" => 1,
			"acc" => 1,
		];

		$update = $this->DB->updateTB("db_start_test", $dataTable , "id", $startTest['id']);

		$_SESSION['quest']['collect'] = $_SESSION['quest']['sid'];
		$this->printJson($this->invalid(true, 200 , "Success.", ["url" => $this->base_url("start/preview")]));
	}

	public function try()
	{
		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['collect']) || empty($_SESSION['quest']['collect'])) $this->printJson($this->invalid());


		$params = $this->Request->get();
		if (strtolower($params) !== "try") $this->printJson($this->invalid());

		$startTest = $this->DB->query("
			SELECT
				*
			FROM
				db_start_test
			WHERE
				sid = '". $this->e($_SESSION['quest']['sid']) ."'
				AND uid = '". $this->e($_SESSION['member_name']) ."'
				AND bu = '". $this->e($_SESSION['quest']['bu']) ."'
				AND area = '". $this->e($_SESSION['quest']['area']) ."'
				AND flag = '1'
				AND acc = '1'
		", true);

		if (!$startTest) $this->printJson($this->invalid(false, 404 ,"User not found!"));
		if ($startTest['try'] >= 2) $this->printJson($this->invalid(false, 403, "Limit max 2 try!"));
		$loader = $this->DB->query("
			SELECT 
				a.*,
				b.answer_key
			from 
				db_answer a,
				db_quest b
			where 
				a.sid='". $this->e($_SESSION['quest']['sid']) ."' 
				AND a.uid='". $this->e($_SESSION['member_name']) ."' 
				AND a.wave='". $this->e($_SESSION['quest']['wave']) ."' 
				AND a.flag='1'
				AND a.qid = b.id
				AND b.status='1'
		");
		
		if (!$loader) $this->printJson($this->invalid());

		$_SESSION['quest']['wave'] = $_SESSION['quest']['wave']+1;

		$isFalse = 0;
		$cloned = [];
		foreach ($loader as $loads) {
			if ($loads['answer'] !== $loads['answer_key']) {
				$isFalse++;

				// 
				// $update = $this->DB->updateTB("db_answer", ["flag" => 0] , "id", $loads['id']);
				// if (!$update) $this->printJson($this->invalid());

				$loads['flag'] = 0;
			}
			unset($loads['answer_key']);
			unset($loads['id']);
			$loads['wave'] = $_SESSION['quest']['wave'];

			$cloned[] = $loads;
		}

		if (!$isFalse) $this->printJson($this->invalid(false, 403 ,"Can't repeat!"));

		for ($i=0; $i < count($cloned); $i++) { 
			$clone = $this->DB->insertTB("db_answer", $cloned[$i]);
			if (!$clone) $this->printJson($this->invalid(false, 403 ,"Error repeat!"));
		}

		if (strtolower($params) == "try") unset($_SESSION['quest']['collect']);

		$regulation = $this->DB->selectTB("db_regulation", 'id', 1, 1);
		$now = time();

		$dataTable = [
			"flag" => 0,
			"try" => ($startTest['try']+1),
			"acc" => 0,
		];

		$update = $this->DB->updateTB("db_start_test", $dataTable , "id", $startTest['id']);

		$_SESSION['quest']['starttime'] = $now;
		$_SESSION['quest']['endtime'] = $now+(60*$regulation['timer']);

		$this->printJson($this->invalid(true, 200 , "Success.", ["url" => $this->base_url("start")]));
	}

	public function finish()
	{
		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['collect']) || empty($_SESSION['quest']['collect'])) $this->printJson($this->invalid());
	
		$params = $this->Request->get();

		if (strtolower($params) == "end") unset($_SESSION['quest']['collect']);
		if (strtolower($params) == "end") unset($_SESSION['quest']['bu']);
		if (strtolower($params) == "end") unset($_SESSION['quest']['area']);
		if (strtolower($params) == "end") $this->printJson($this->invalid(true, 200, ['url' => $this->base_url()]));

		$this->printJson($this->invalid());
	}

	public function timeout()
	{
		// NOT FINISH
		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) $this->printJson($this->invalid());
		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) $this->printJson($this->invalid());

		$params = $this->Request->get();
		if ($params !== "reset") $this->printJson($this->invalid());

		$startTest = $this->DB->query("
			SELECT * FROM db_start_test 
			WHERE sid = '". $this->e($_SESSION['quest']['sid']) ."'
			AND uid = '". $this->e($_SESSION['member_name']) ."'
			AND bu = '". $this->e($_SESSION['quest']['bu']) ."'
			AND area = '". $this->e($_SESSION['quest']['area']) ."'
			AND flag = '0'
			AND acc = '0'
		", 1);
		if (!$startTest) $this->printJson($this->invalid(false, 404 ,"User not found!"));

		$regulation = $this->DB->selectTB("db_regulation", 'id', 1, 1);
		if (!$regulation) $this->printJson($this->invalid());

		$getQuest = $this->DB->query("
			SELECT * FROM db_quest
			WHERE status = '1'
			AND bu = '". $this->e($_SESSION['quest']['bu']) ."'
			AND area = '". $this->e($_SESSION['quest']['area']) ."'
		");
		if (!$getQuest) $this->printJson($this->invalid(false, 404 ,"Quest not found!"));

		$getAnswer = $this->DB->query("
			SELECT * FROM db_answer
			WHERE sid = '". $this->e($_SESSION['quest']['sid']) ."'
			AND uid = '". $this->e($_SESSION['member_name']) ."'
			AND bu = '". $this->e($_SESSION['quest']['bu']) ."'
			AND area = '". $this->e($_SESSION['quest']['area']) ."'
			-- AND flag = '0'
		");

		foreach ($getQuest as $quest) {

			if (strtoupper($quest['answer_key']) == "A") $answer_key = "B";
			if (strtoupper($quest['answer_key']) == "B") $answer_key = "C";
			if (strtoupper($quest['answer_key']) == "C") $answer_key = "D";
			if (strtoupper($quest['answer_key']) == "D") $answer_key = "A";

			if (!$getAnswer) {
				$insertAnswer = [
					"sid" => $this->e($_SESSION['quest']['sid']),
					"uid" => $this->e($_SESSION['member_name']),
					"qid" => $this->e($quest['id']),
					"answer" => $answer_key,
					"bu" => $this->e($_SESSION['quest']['bu']),
					"area" => $this->e($_SESSION['quest']['area']),
					"cat" => $this->e($quest['cat']),
					"flag" => "0",
					"created" => time(),
				];

				$this->DB->insertTB("db_answer", $insertAnswer);
			}

			if ($getAnswer) {
				$find = $this->multi_array_search_by_value($quest['id'], $getAnswer);
				if (!$find) {

					$insertAnswer = [
						"sid" => $this->e($_SESSION['quest']['sid']),
						"uid" => $this->e($_SESSION['member_name']),
						"qid" => $this->e($quest['id']),
						"answer" => $answer_key,
						"bu" => $this->e($_SESSION['quest']['bu']),
						"area" => $this->e($_SESSION['quest']['area']),
						"cat" => $this->e($quest['cat']),
						"flag" => "0",
						"created" => time(),
					];

					$this->DB->insertTB("db_answer", $insertAnswer);
				}
			}
		}


		$getAnswer = $this->DB->query("
			SELECT * FROM db_answer
			WHERE sid = '". $this->e($_SESSION['quest']['sid']) ."'
			AND uid = '". $this->e($_SESSION['member_name']) ."'
			AND bu = '". $this->e($_SESSION['quest']['bu']) ."'
			AND area = '". $this->e($_SESSION['quest']['area']) ."'
			AND flag = '0'
		");

		if (!$getAnswer) $this->printJson($this->invalid(false, 404 ,"Quest not found!"));

		foreach ($getAnswer as $answer) {
			$answer['flag'] = '1';

			$update = $this->DB->updateTB("db_answer", $answer , "id", $answer['id']);
			$result = ($update ? true:false);
			if (!$result) break;
		}



		if ($result !== true) $this->printJson($this->invalid(false, 404 ,"Error data!"));

		$dataTable = [
			"flag" => 1,
			"acc" => 1,
		];

		$update = $this->DB->updateTB("db_start_test", $dataTable , "id", $startTest['id']);

		$_SESSION['quest']['collect'] = $_SESSION['quest']['sid'];
		$this->printJson($this->invalid(true, 200 , "Success.", ["url" => $this->base_url("start/preview")]));
	}
}
// /**
//  * API PUBLIC
//  */

// use Controller\Controller;

// class v2 extends Controller
// {
	
// 	function __construct()
// 	{
// 		$this->DB = (in_array("db_models", get_declared_classes()) ? new db_models():$this->model('db_models'));
// 		$this->config();
// 		$this->Request = $this->helper("Request"); 
// 	}

// 	public function saving()
// 	{
// 		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) $this->printJson($this->invalid());

// 		$params = $this->Request->get();
// 		$getQuest = $this->DB->query("
// 			SELECT 
// 			* 
// 			FROM db_quest 
// 			WHERE status='1' 
// 			AND id='". $this->e($params['id']) ."' 
// 			AND bu='". $this->e($_SESSION['quest']['bu']) ."' 
// 			AND area='". $this->e($_SESSION['quest']['area']) ."' 
// 			AND cat='". $this->e($params['cat']) ."'
// 		", 1);

// 		if (!$getQuest) $this->printJson($this->invalid());

// 		$dataTable = [
// 			"sid" => $this->e($_SESSION['quest']['sid']),
// 			"uid" => $this->e($_SESSION['member_name']),
// 			"qid" => $this->e($params['id']),
// 			"answer" => $this->e($params['input']),
// 			"bu" => $this->e($_SESSION['quest']['bu']),
// 			"area" => $this->e($_SESSION['quest']['area']),
// 			"wave" => $this->e($_SESSION['quest']['wave']),
// 			"cat" => $this->e($params['cat']),
// 			"flag" => "0",
// 			"created" => time(),
// 		];

// 		$getAnswer = $this->DB->query("
// 			SELECT 
// 			* 
// 			FROM 
// 			db_answer 
// 			WHERE sid='". $this->e($dataTable['sid']) ."' 
// 			AND uid='". $this->e($dataTable['uid']) ."' 
// 			AND wave='". $this->e($dataTable['wave']) ."' 
// 			AND qid='". $this->e($dataTable['qid']) ."' 
// 			AND flag='0'", 1);

// 		if (!$getAnswer) $this->DB->insertTB("db_answer", $dataTable);
		
// 		if (isset($getAnswer['answer']) && $getAnswer['answer'] !== $dataTable['answer']) $this->DB->updateTB("db_answer", $dataTable , "id", $getAnswer['id']);
// 	}

// 	public function collect()
// 	{
// 		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) $this->printJson($this->invalid());
	
// 		if (!isset($_POST['answer']) && empty($_POST['answer'])) $this->printJson($this->invalid());
// 		$result = null;

// 		$startTest = $this->DB->query("
// 			SELECT
// 				*
// 			FROM
// 				db_start_test
// 			WHERE
// 				sid = '". $this->e($_SESSION['quest']['sid']) ."'
// 				AND uid = '". $this->e($_SESSION['member_name']) ."'
// 				AND bu = '". $this->e($_SESSION['quest']['bu']) ."'
// 				AND area = '". $this->e($_SESSION['quest']['area']) ."'
// 				AND flag = '0'
// 		", true);

// 		if (!$startTest) $this->printJson($this->invalid(false, 404 ,"User not found!"));

// 		// Check Relation Quest
// 		for ($i=0; $i < count($_POST['answer']); $i++) { 

// 			$dataTable = [
// 				"sid" => $this->e($_SESSION['quest']['sid']),
// 				"uid" => $this->e($_SESSION['member_name']),
// 				"qid" => $this->e($_POST['answer'][$i]['id']),
// 				"answer" => $this->e($_POST['answer'][$i]['ans']),
// 				"bu" => $this->e($_SESSION['quest']['bu']),
// 				"area" => $this->e($_SESSION['quest']['area']),
// 				"wave" => $this->e($_SESSION['quest']['wave']),
// 				"flag" => "1",
// 			];

// 			$getAnswer = $this->DB->query("SELECT * FROM db_answer WHERE sid='". $this->e($dataTable['sid']) ."' AND wave='". $this->e($dataTable['wave']) ."' AND uid='". $this->e($dataTable['uid']) ."' AND qid='". $this->e($dataTable['qid']) ."'", 1);
			
// 			if (!$getAnswer) $this->printJson($this->invalid(false, 404 ,"Data not found!"));

// 			if (isset($getAnswer['answer']) && isset($getAnswer['flag']) && $getAnswer['flag'] == "0") {
// 				$update = $this->DB->updateTB("db_answer", $dataTable , "id", $getAnswer['id']);
// 				$result = ($update ? true:false);
// 				if (!$result) break;
// 				// $result = true;
// 			}
// 		}

// 		if ($result !== true) $this->printJson($this->invalid(false, 404 ,"Error data!"));

// 		$dataTable = [
// 			"flag" => 1,
// 			"acc" => 1,
// 		];

// 		$update = $this->DB->updateTB("db_start_test", $dataTable , "id", $startTest['id']);

// 		$_SESSION['quest']['collect'] = $_SESSION['quest']['sid'];
// 		$this->printJson($this->invalid(true, 200 , "Success.", ["url" => $this->base_url("start/preview")]));
// 	}

// 	public function try()
// 	{
// 		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['wave']) || empty($_SESSION['quest']['wave'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['collect']) || empty($_SESSION['quest']['collect'])) $this->printJson($this->invalid());


// 		$params = $this->Request->get();
// 		if (strtolower($params) !== "try") $this->printJson($this->invalid());

// 		$startTest = $this->DB->query("
// 			SELECT
// 				*
// 			FROM
// 				db_start_test
// 			WHERE
// 				sid = '". $this->e($_SESSION['quest']['sid']) ."'
// 				AND uid = '". $this->e($_SESSION['member_name']) ."'
// 				AND bu = '". $this->e($_SESSION['quest']['bu']) ."'
// 				AND area = '". $this->e($_SESSION['quest']['area']) ."'
// 				AND flag = '1'
// 				AND acc = '1'
// 		", true);

// 		if (!$startTest) $this->printJson($this->invalid(false, 404 ,"User not found!"));
// 		if ($startTest['try'] >= 2) $this->printJson($this->invalid(false, 403, "Limit max 2 try!"));
// 		$loader = $this->DB->query("
// 			SELECT 
// 				a.*,
// 				b.answer_key
// 			from 
// 				db_answer a,
// 				db_quest b
// 			where 
// 				a.sid='". $this->e($_SESSION['quest']['sid']) ."' 
// 				AND a.uid='". $this->e($_SESSION['member_name']) ."' 
// 				AND a.wave='". $this->e($_SESSION['quest']['wave']) ."' 
// 				AND a.flag='1'
// 				AND a.qid = b.id
// 				AND b.status='1'
// 		");
		
// 		if (!$loader) $this->printJson($this->invalid());

// 		$_SESSION['quest']['wave'] = $_SESSION['quest']['wave']+1;

// 		$isFalse = 0;
// 		$cloned = [];
// 		foreach ($loader as $loads) {
// 			if ($loads['answer'] !== $loads['answer_key']) {
// 				$isFalse++;

// 				// 
// 				// $update = $this->DB->updateTB("db_answer", ["flag" => 0] , "id", $loads['id']);
// 				// if (!$update) $this->printJson($this->invalid());

// 				$loads['flag'] = 0;
// 			}
// 			unset($loads['answer_key']);
// 			unset($loads['id']);
// 			$loads['wave'] = $_SESSION['quest']['wave'];

// 			$cloned[] = $loads;
// 		}

// 		if (!$isFalse) $this->printJson($this->invalid(false, 403 ,"Can't repeat!"));

// 		for ($i=0; $i < count($cloned); $i++) { 
// 			$clone = $this->DB->insertTB("db_answer", $cloned[$i]);
// 			if (!$clone) $this->printJson($this->invalid(false, 403 ,"Error repeat!"));
// 		}

// 		if (strtolower($params) == "try") unset($_SESSION['quest']['collect']);

// 		$regulation = $this->DB->selectTB("db_regulation", 'id', 1, 1);
// 		$now = time();

// 		$dataTable = [
// 			"flag" => 0,
// 			"try" => ($startTest['try']+1),
// 			"acc" => 0,
// 		];

// 		$update = $this->DB->updateTB("db_start_test", $dataTable , "id", $startTest['id']);

// 		$_SESSION['quest']['starttime'] = $now;
// 		$_SESSION['quest']['endtime'] = $now+(60*$regulation['timer']);

// 		$this->printJson($this->invalid(true, 200 , "Success.", ["url" => $this->base_url("start")]));
// 	}

// 	public function finish()
// 	{
// 		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['collect']) || empty($_SESSION['quest']['collect'])) $this->printJson($this->invalid());
	
// 		$params = $this->Request->get();

// 		if (strtolower($params) == "end") unset($_SESSION['quest']['collect']);
// 		if (strtolower($params) == "end") unset($_SESSION['quest']['bu']);
// 		if (strtolower($params) == "end") unset($_SESSION['quest']['area']);
// 		if (strtolower($params) == "end") $this->printJson($this->invalid(true, 200, ['url' => $this->base_url()]));

// 		$this->printJson($this->invalid());
// 	}

// 	public function timeout()
// 	{
// 		// NOT FINISH
// 		if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['bu']) || empty($_SESSION['quest']['bu'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['area']) || empty($_SESSION['quest']['area'])) $this->printJson($this->invalid());
// 		if (!isset($_SESSION['quest']['sid']) || empty($_SESSION['quest']['sid'])) $this->printJson($this->invalid());

// 		$params = $this->Request->get();
// 		if ($params !== "reset") $this->printJson($this->invalid());

// 		$startTest = $this->DB->query("
// 			SELECT * FROM db_start_test 
// 			WHERE sid = '". $this->e($_SESSION['quest']['sid']) ."'
// 			AND uid = '". $this->e($_SESSION['member_name']) ."'
// 			AND bu = '". $this->e($_SESSION['quest']['bu']) ."'
// 			AND area = '". $this->e($_SESSION['quest']['area']) ."'
// 			AND flag = '0'
// 			AND acc = '0'
// 		", 1);
// 		if (!$startTest) $this->printJson($this->invalid(false, 404 ,"User not found!"));

// 		$regulation = $this->DB->selectTB("db_regulation", 'id', 1, 1);
// 		if (!$regulation) $this->printJson($this->invalid());

// 		$getQuest = $this->DB->query("
// 			SELECT * FROM db_quest
// 			WHERE status = '1'
// 			AND bu = '". $this->e($_SESSION['quest']['bu']) ."'
// 			AND area = '". $this->e($_SESSION['quest']['area']) ."'
// 		");
// 		if (!$getQuest) $this->printJson($this->invalid(false, 404 ,"Quest not found!"));

// 		$getAnswer = $this->DB->query("
// 			SELECT * FROM db_answer
// 			WHERE sid = '". $this->e($_SESSION['quest']['sid']) ."'
// 			AND uid = '". $this->e($_SESSION['member_name']) ."'
// 			AND bu = '". $this->e($_SESSION['quest']['bu']) ."'
// 			AND area = '". $this->e($_SESSION['quest']['area']) ."'
// 			-- AND flag = '0'
// 		");

// 		foreach ($getQuest as $quest) {

// 			if (strtoupper($quest['answer_key']) == "A") $answer_key = "B";
// 			if (strtoupper($quest['answer_key']) == "B") $answer_key = "C";
// 			if (strtoupper($quest['answer_key']) == "C") $answer_key = "D";
// 			if (strtoupper($quest['answer_key']) == "D") $answer_key = "A";

// 			if (!$getAnswer) {
// 				$insertAnswer = [
// 					"sid" => $this->e($_SESSION['quest']['sid']),
// 					"uid" => $this->e($_SESSION['member_name']),
// 					"qid" => $this->e($quest['id']),
// 					"answer" => $answer_key,
// 					"bu" => $this->e($_SESSION['quest']['bu']),
// 					"area" => $this->e($_SESSION['quest']['area']),
// 					"cat" => $this->e($quest['cat']),
// 					"flag" => "0",
// 					"created" => time(),
// 				];

// 				$this->DB->insertTB("db_answer", $insertAnswer);
// 			}

// 			if ($getAnswer) {
// 				$find = $this->multi_array_search_by_value($quest['id'], $getAnswer);
// 				if (!$find) {

// 					$insertAnswer = [
// 						"sid" => $this->e($_SESSION['quest']['sid']),
// 						"uid" => $this->e($_SESSION['member_name']),
// 						"qid" => $this->e($quest['id']),
// 						"answer" => $answer_key,
// 						"bu" => $this->e($_SESSION['quest']['bu']),
// 						"area" => $this->e($_SESSION['quest']['area']),
// 						"cat" => $this->e($quest['cat']),
// 						"flag" => "0",
// 						"created" => time(),
// 					];

// 					$this->DB->insertTB("db_answer", $insertAnswer);
// 				}
// 			}
// 		}


// 		$getAnswer = $this->DB->query("
// 			SELECT * FROM db_answer
// 			WHERE sid = '". $this->e($_SESSION['quest']['sid']) ."'
// 			AND uid = '". $this->e($_SESSION['member_name']) ."'
// 			AND bu = '". $this->e($_SESSION['quest']['bu']) ."'
// 			AND area = '". $this->e($_SESSION['quest']['area']) ."'
// 			AND flag = '0'
// 		");

// 		if (!$getAnswer) $this->printJson($this->invalid(false, 404 ,"Quest not found!"));

// 		foreach ($getAnswer as $answer) {
// 			$answer['flag'] = '1';

// 			$update = $this->DB->updateTB("db_answer", $answer , "id", $answer['id']);
// 			$result = ($update ? true:false);
// 			if (!$result) break;
// 		}



// 		if ($result !== true) $this->printJson($this->invalid(false, 404 ,"Error data!"));

// 		$dataTable = [
// 			"flag" => 1,
// 			"acc" => 1,
// 		];

// 		$update = $this->DB->updateTB("db_start_test", $dataTable , "id", $startTest['id']);

// 		$_SESSION['quest']['collect'] = $_SESSION['quest']['sid'];
// 		$this->printJson($this->invalid(true, 200 , "Success.", ["url" => $this->base_url("start/preview")]));
// 	}
// }