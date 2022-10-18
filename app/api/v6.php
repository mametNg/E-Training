<?php

/**
 * SYSTEM ONLY
 */

use Controller\Controller;

class v6 extends Controller
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
		if (isset($_SESSION['username']) && !empty($_SESSION['username'])) $this->printJson($this->invalid(false, 403, "This session already exist!", ["url" => $this->base_url().""]));

		// get request
		$params = $this->Request->get();

		// Filter username
		if (!isset($params['uname']) || empty($params['uname'])) $this->printJson($this->invalid(false, 403, "This username cannot be empty!"));

		// Filter Password
		if (!isset($params['password']) || empty($params['password'])) $this->printJson($this->invalid(false, 403, "This password cannot be empty!"));

		// Get User
		$user = $this->DB->selectTB("db_admin", "id", $this->e($params['uname']), true);

		// Login Failed
		if (!$user) $this->printJson($this->invalid(false, 403, "This username isn't registered"));

		// Valid password
		if (!password_verify($this->e($params['password']), $this->w3llDecode($user['password']))) $this->printJson($this->invalid(false, 403, "Wrong Password"));

		// Create session login
		$_SESSION['username'] = $this->e($user['id']);

		// Login success
		$response = $this->invalid(true, 200, "Login Success");
		$this->printJson($response);
	}

	public function manegeExamBu()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
		// get request
		$params = $this->Request->get();

		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
		
		$bu = $this->w3llDecode($this->e($params['bu']));

		if (!$bu) $this->printJson($this->invalid(false, 403, "This bu isn't valid!"));

		$catBu = $this->DB->query("SELECT area FROM db_quest WHERE BU='$bu' GROUP BY area");

		if (!$catBu) $this->printJson($this->invalid(false, 403, "Bu category not found!"));
		$this->printJson($this->invalid(true, 200, "ok", $catBu));
	}

	public function manegeExamArea()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
		// get request
		$params = $this->Request->get();

		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
			
		$bu = $this->w3llDecode($this->e($params['bu']));

		if (!$bu) $this->printJson($this->invalid(false, 403, "This bu isn't valid!"));

		$cats = [];
		$cat = [];

		$quests = $this->DB->query("
			SELECT
			id,
			quest,
			bu,
			area,
			cat,
			status
			FROM 
			db_quest
			WHERE 
			bu='".$this->e($bu)."'
			AND flag='0'
			AND area='".$this->e($params['area'])."'
			ORDER BY id ASC
		");

		if (!$quests) $this->printJson($this->invalid(false, 403, "Quest not found!"));

		for ($i=0; $i < count($quests); $i++) $quests[$i]['quest'] = hex2bin(strip_tags($this->e($quests[$i]['quest'])));
		for ($i=0; $i < count($quests); $i++) $cats[$quests[$i]['cat']] = $quests[$i]['cat'];
		foreach ($cats as $val) array_push($cat, $val);

		$dataTable = [
			"cat" => $cat,
			"quests" => $quests,
		];

		$this->printJson($this->invalid(true, 200, "ok", $dataTable));
	}

	public function manegeExamCat()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
		// get request
		$params = $this->Request->get();

		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
		if (!isset($params['cat']) || empty($params['cat'])) $this->printJson($this->invalid(false, 403, "This category cannot be empty!"));

		$bu = $this->w3llDecode($this->e($params['bu']));

		if (!$bu) $this->printJson($this->invalid(false, 403, "This bu isn't valid!"));

		$quests = $this->DB->query("
			SELECT
			id,
			quest,
			bu,
			area,
			cat,
			status
			FROM 
			db_quest
			WHERE 
			bu='".$this->e($bu)."'
			AND flag='0'
			AND area='".$this->e($params['area'])."'
			AND cat='".$this->e($params['cat'])."'
			ORDER BY id ASC
		");

		if (!$quests) $this->printJson($this->invalid(false, 403, "Quest not found!"));

		for ($i=0; $i < count($quests); $i++) $quests[$i]['quest'] = hex2bin(strip_tags($this->e($quests[$i]['quest'])));

		$this->printJson($this->invalid(true, 200, "ok", $quests));
	}

	public function sortExamResult()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
		// get request
		$params = $this->Request->get();

		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
			
		$bu = $this->w3llDecode($this->e($params['bu']));

		if (!$bu) $this->printJson($this->invalid(false, 403, "This bu isn't valid!"));
		$exp = 14515200;
		$quests = $this->DB->query("
			SELECT
			a.id,
			a.uid,
			b.name,
			a.bu,
			a.area,
			a.acc,
			a.created
			FROM 
			db_start_test a,
			db_members b
			WHERE
			a.flag = '1'
			AND a.is_delt = '0'
			AND bu='".$this->e($bu)."'
			AND area='".$this->e($params['area'])."'
			AND a.uid = b.id
			AND a.created <= '". (time()+$exp) ."'
			-- GROUP BY a.uid, a.bu, a.area
			ORDER BY a.created DESC
		");

		if (!$quests) $this->printJson($this->invalid(false, 403, "Quest not found!"));

		$setQuests = $this->array_group($quests, "uid", "bu", "area");
		
		$resultSet = [];
		foreach ($setQuests as $key => $setQuest) {
			$setQuest[0]['created'] = date("Y-m-d", $setQuest[0]['created']);
			if (count($setQuest) >= 2) {
				$found = false;
				for ($i=0; $i < count($setQuest); $i++) { 
					if ($setQuest[$i]['acc'] == 1) $resultSet[] = $setQuest[$i];
					if ($setQuest[$i]['acc'] == 1) $found = true;
					if ($setQuest[$i]['acc'] == 1) break;
				}

				if (!$found) $resultSet[] = $setQuest[0];
			} else {
				$resultSet[] = $setQuest[0];
			}
		}

		$quests = $resultSet;

		$this->printJson($this->invalid(true, 200, "ok", $quests));
	}

	public function sortAllExamResult()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
		// get request
		$params = $this->Request->get();

		if (!isset($params['quest']) || empty($params['quest'])) $this->printJson($this->invalid(false, 403, "This quest cannot be empty!"));
		if ($params['quest'] !== "all") $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));

		$exp = 14515200;
		$quests = $this->DB->query("
			SELECT
			a.id,
			a.uid,
			b.name,
			a.bu,
			a.area,
			a.acc,
			a.created
			FROM 
			db_start_test a,
			db_members b
			WHERE
			a.flag = '1'
			AND a.is_delt = '0'
			AND a.uid = b.id
			AND a.created <= '". (time()+$exp) ."'
			-- GROUP BY a.uid, a.bu, a.area
			ORDER BY a.created DESC
		");

		if (!$quests) $this->printJson($this->invalid(false, 403, "Quest not found!"));

		$setQuests = $this->array_group($quests, "uid", "bu", "area");
		
		$resultSet = [];
		foreach ($setQuests as $key => $setQuest) {
			$setQuest[0]['created'] = date("Y-m-d", $setQuest[0]['created']);
			if (count($setQuest) >= 2) {
				$found = false;
				for ($i=0; $i < count($setQuest); $i++) { 
					if ($setQuest[$i]['acc'] == 1) $resultSet[] = $setQuest[$i];
					if ($setQuest[$i]['acc'] == 1) $found = true;
					if ($setQuest[$i]['acc'] == 1) break;
				}

				if (!$found) $resultSet[] = $setQuest[0];
			} else {
				$resultSet[] = $setQuest[0];
			}
		}

		$quests = $resultSet;

		$this->printJson($this->invalid(true, 200, "ok", $quests));
	}

	public function resetResultExam()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
		// get request
		$params = $this->Request->get();

		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));
		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));

		$check = $this->DB->query("
			SELECT 
			* 
			FROM
			db_start_test
			WHERE
			flag='1'
			AND acc='1'
			AND uid='". $this->e($params['id']) ."'
			AND bu='". $this->e($params['bu']) ."'
			AND area='". $this->e($params['area']) ."'
		");

		if (!$check) $this->printJson($this->invalid(false, 404, "No data!"));

		$isUpdate = true;

		foreach ($check as $dataTable) {
			$dataTable['acc'] = "0";

			$update = $this->DB->updateTB("db_start_test", $dataTable, "id", $this->e($dataTable['id']));

			if (!$update) $isUpdate = false;
			if (!$update) break;
		}

		if (!$isUpdate) $this->printJson($this->invalid(false, 403, "Reset Failed!"));
		if ($isUpdate) $this->printJson($this->invalid(true, 200, "Reset success."));
	}

	public function deleteResultExam()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
		// get request
		$params = $this->Request->get();

		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));
		if (!isset($params['sid']) || empty($params['sid'])) $this->printJson($this->invalid(false, 403, "This sid cannot be empty!"));

		$load = $this->DB->query("SELECT * FROM db_start_test WHERE flag='1' AND is_delt='0' AND uid='". $this->e($params['id']) ."' AND sid='". $this->e($params['sid']) ."'", 1);
		if (!$load) $this->printJson($this->invalid(false, 404, "No data!"));

		$dataTable = [
			"is_delt" => 1
		];

		$deleted = $this->DB->updateTB("db_start_test", $dataTable, "id", $this->e($load['id']));

		if (!$deleted) $this->printJson($this->invalid(false, 403, "Delete exam failed!"));
		$this->printJson($this->invalid(true, 200, "Delete exam success."));
	}

	public function printDetailExamResult()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
		// get request
		$params = $this->Request->get();

		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));
		if (!isset($params['sid']) || empty($params['sid'])) $this->printJson($this->invalid(false, 403, "This sid cannot be empty!"));
		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));

		$users = $this->DB->query("
			SELECT 
				a.sid,
				b.name,
				a.uid,
				c.bu,
				c.area,
				c.cat,
				a.answer,
				c.answer_key,
				a.created,
				d.min_value
			FROM 
				db_answer a, 
				db_members b, 
				db_quest_results c, 
				db_start_test d
			WHERE 
				a.uid='". $this->e($params['id']) ."' 
				AND a.bu = '". $this->e($params['bu']) ."'
				AND a.area = '". $this->e($params['area']) ."'
				AND a.sid = '". $this->e($params['sid']) ."'
				-- AND c.sid = '". $this->e($params['sid']) ."'
				AND a.flag='1'
				AND a.uid=b.id
				AND a.qid=c.qid
				AND a.uid=c.uid
				AND a.cat=c.cat
				AND a.uid=d.uid
				AND a.sid=d.sid
				AND a.wave=(d.try+1)
				ORDER BY a.created desc
		");

		$regulation = $this->DB->selectTB("db_regulation", "id", 1, 1);
		if (!$users || !$regulation) $this->printJson($this->invalid(false, 403, "Data isn't valid!"));

		$result = [];
		$i=0;
		$_historys = $this->array_group($users, "sid", "cat");

		foreach ($_historys as $historys) {
			$f = 0;
			$t = 0;
			foreach ($historys as $history) {
				$result[$i]['created'] = $history['created'];
				$result[$i]['name'] = $history['name'];
				$result[$i]['sid'] = $history['sid'];
				$result[$i]['cat'] = $history['cat'];
				$result[$i]['area'] = $history['area'];
				$result[$i]['bu'] = $history['bu'];
				$result[$i]['created'] = date("d-m-Y h:i:s a", $this->e($history['created']));
				if ($history['answer_key'] == $history['answer']) $t++;
				if ($history['answer_key'] !== $history['answer']) $f++;
			}
			$result[$i]['true'] = $t;
			$result[$i]['false'] = $f;
			$result[$i]['score'] = $this->e(substr(((100/count($historys))*$t), 0, 3), ".");
			$result[$i]['desc'] = ((100/count($historys))*$t >= $historys[0]['min_value'] ? "Congrats":"Failed");

			$i++;
		}

        $this->printJson($this->invalid(true, 200, "ok", $result));
	}

	public function printExamStatic()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

		// get request
		$params = $this->Request->get();

		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));
		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
		$load = $this->DB->query("
			SELECT 
			a.uid,
			a.sid,
			a.qid,
			a.bu,
			a.area,
			c.cat,
			a.answer,
			c.answer_key,
			b.name,
			b.id,
			d.created,
			d.min_value
			FROM 
			db_answer a, 
			db_members b, 
			db_quest_results c, 
			db_start_test d
			WHERE 
			b.id='". $this->e($params['id']) ."' 
			AND d.uid='". $this->e($params['id']) ."' 
			AND d.bu = '". $this->e($params['bu']) ."'
			AND d.area = '". $this->e($params['area']) ."'
			AND d.is_delt = '0'
			AND d.flag = '1'
			AND a.flag = '1'
			AND a.sid = d.sid
			AND a.uid = d.uid
			AND a.wave = (d.try+1)
			AND a.uid=c.uid
			AND a.sid=c.sid
			AND a.qid=c.qid
			AND d.created BETWEEN '".(time() - (604800*2))."' AND '".time()."'
		");

		$regulation = $this->DB->selectTB("db_regulation", "id", 1, 1);
		if (!$load || !$regulation) $this->printJson($this->invalid(false, 403, "Data isn't valid!"));
		
		$user = [];
		$sampleResult = [];
		$result = [];
		$i=0;
		$_historys = $this->array_group($load, "sid", "cat");

		foreach ($_historys as $historys) {
			$f = 0;
			$t = 0;
			foreach ($historys as $history) {
				$user['name'] = $history['name'];
				$user['id'] = $history['id'];

				$sampleResult[$i]['label'] = $history['bu']." - ".$history['area']." - ".$history['cat'];
				$sampleResult[$i]['created'] = date("Y-m-d h:i:s", $history['created']);

				if ($history['answer_key'] == $history['answer']) $t++;
				if ($history['answer_key'] !== $history['answer']) $f++;
			}
			$sampleResult[$i]['score'] = $this->e(substr(((100/count($historys))*$t), 0, 3), ".");
			$i++;
		}

		$i=0;
		foreach ($this->array_group($sampleResult, "label") as $key => $samples) {
			$result[$i]['label'] = $key;
			$result[$i]['data'] = [];

			$x=0;
			foreach ($samples as $sample) {
				$result[$i]['data'][$x]['score'] = $sample['score'];
				$result[$i]['data'][$x]['created'] = $sample['created'];
				$x++;
			}
			$i++;
		}
		// foreach ($_historys as $historys) {
		// 	$f = 0;
		// 	$t = 0;
		// 	foreach ($historys as $history) {
		// 		$user['name'] = $history['name'];
		// 		$user['id'] = $history['id'];

		// 		$result[$i]['created'] = $history['created'];
		// 		$result[$i]['sid'] = $history['sid'];
		// 		$result[$i]['cat'] = $history['cat'];
		// 		$result[$i]['area'] = $history['area'];
		// 		$result[$i]['bu'] = $history['bu'];
		// 		$result[$i]['created'] = date("d-m-Y h:i:s a", $this->e($history['created']));

		// 		if ($history['answer_key'] == $history['answer']) $t++;
		// 		if ($history['answer_key'] !== $history['answer']) $f++;
		// 	}
		// 	$result[$i]['true'] = $t;
		// 	$result[$i]['false'] = $f;
		// 	$result[$i]['score'] = $this->e(substr(((100/count($historys))*$t), 0, 3), ".");
		// 	$result[$i]['desc'] = ((100/count($historys))*$t >= $historys[$i]['min_value'] ? "Congrats":"Failed");

		// 	$i++;
		// }
		$result = [
			"user" => $user,
			"data" => $result,
		];
		$this->printJson($this->invalid(true, 200, "ok", $result));
	}

	public function findPrintExamStatic()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

		// get request
		$params = $this->Request->get();

		if (!isset($params['uid']) || empty($params['uid'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));
		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
		if (!isset($params['start-date']) || empty($params['start-date'])) $this->printJson($this->invalid(false, 403, "This start date cannot be empty!"));
		if (!isset($params['end-date']) || empty($params['end-date'])) $this->printJson($this->invalid(false, 403, "This end date cannot be empty!"));

		$load = $this->DB->query("
			SELECT 
			a.uid,
			a.sid,
			a.qid,
			a.bu,
			a.area,
			c.cat,
			a.answer,
			c.answer_key,
			b.name,
			b.id,
			d.created,
			d.min_value
			FROM 
			db_answer a, 
			db_members b, 
			db_quest_results c, 
			db_start_test d
			WHERE 
			d.uid='". $this->e($params['uid']) ."' 
			AND d.bu = '". $this->e($params['bu']) ."'
			AND d.area = '". $this->e($params['area']) ."'
			AND d.is_delt = '0'
			AND d.flag = '1'
			AND a.flag = '1'
			AND a.sid = d.sid
			AND a.uid = d.uid
			AND a.wave = (d.try+1)
			AND a.uid=b.id
			AND a.qid=c.qid
			AND a.sid=c.sid
			AND a.uid=c.uid
			AND FROM_UNIXTIME(d.created, '%Y-%m-%d') BETWEEN '".$this->e($params['start-date'])."' AND '".$this->e($params['end-date'])."'
		");

		$regulation = $this->DB->selectTB("db_regulation", "id", 1, 1);
		if (!$load || !$regulation) $this->printJson($this->invalid(false, 403, "Data isn't valid!"));
		
		$user = [];
		$sampleResult = [];
		$result = [];
		$i=0;
		$_historys = $this->array_group($load, "sid", "cat");

		foreach ($_historys as $historys) {
			$f = 0;
			$t = 0;
			foreach ($historys as $history) {
				$user['name'] = $history['name'];
				$user['id'] = $history['id'];

				$sampleResult[$i]['label'] = $history['bu']." - ".$history['area']." - ".$history['cat'];
				$sampleResult[$i]['created'] = date("Y-m-d h:i:s", $history['created']);

				if ($history['answer_key'] == $history['answer']) $t++;
				if ($history['answer_key'] !== $history['answer']) $f++;
			}
			$sampleResult[$i]['score'] = $this->e(substr(((100/count($historys))*$t), 0, 3), ".");
			$i++;
		}

		$i=0;
		foreach ($this->array_group($sampleResult, "label") as $key => $samples) {
			$result[$i]['label'] = $key;
			$result[$i]['data'] = [];

			$x=0;
			foreach ($samples as $sample) {
				$result[$i]['data'][$x]['score'] = $sample['score'];
				$result[$i]['data'][$x]['created'] = $sample['created'];
				$x++;
			}
			$i++;
		}
		// foreach ($_historys as $historys) {
		// 	$f = 0;
		// 	$t = 0;
		// 	foreach ($historys as $history) {
		// 		$user['name'] = $history['name'];
		// 		$user['id'] = $history['id'];

		// 		$result[$i]['created'] = $history['created'];
		// 		$result[$i]['sid'] = $history['sid'];
		// 		$result[$i]['cat'] = $history['cat'];
		// 		$result[$i]['area'] = $history['area'];
		// 		$result[$i]['bu'] = $history['bu'];
		// 		$result[$i]['created'] = date("d-m-Y h:i:s a", $this->e($history['created']));

		// 		if ($history['answer_key'] == $history['answer']) $t++;
		// 		if ($history['answer_key'] !== $history['answer']) $f++;
		// 	}
		// 	$result[$i]['true'] = $t;
		// 	$result[$i]['false'] = $f;
		// 	$result[$i]['score'] = $this->e(substr(((100/count($historys))*$t), 0, 3), ".");
		// 	$result[$i]['desc'] = ((100/count($historys))*$t >= $historys[$i]['min_value'] ? "Congrats":"Failed");

		// 	$i++;
		// }
		$result = [
			"user" => $user,
			"data" => $result,
		];
		$this->printJson($this->invalid(true, 200, "ok", $result));
	}

	public function printExamResult()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
		// get request
		$params = $this->Request->get();

		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));
		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
		$users = $this->DB->query("
			SELECT 
				a.sid,
				b.name,
				a.uid,
				d.bu,
				d.area,
				DATE_FORMAT(FROM_UNIXTIME(d.created), '%Y-%m-%d %h:%i:%s %p') as 'created'
			FROM 
				db_answer a,
				db_members b,
				-- db_quest_results c, 
				db_start_test d
			WHERE 
				a.uid = '". $this->e($params['id']) ."' 
				AND a.bu = '". $this->e($params['bu']) ."'
				AND a.area = '". $this->e($params['area']) ."'
				AND a.flag = '1'
				AND d.is_delt = '0'
				AND a.uid = b.id
				-- AND a.qid = c.qid
				-- AND a.sid = c.sid
				-- AND a.uid = c.uid
				-- AND a.cat = c.cat
				AND a.uid = d.uid
				AND a.sid = d.sid
				GROUP BY a.sid
				ORDER BY a.created DESC
		");

		$regulation = $this->DB->selectTB("db_regulation", "id", 1, 1);
		if (!$users || !$regulation) $this->printJson($this->invalid(false, 403, "Data isn't valid!"));

        $this->printJson($this->invalid(true, 200, "ok", $users));
	}

	public function manegeExamShowAll()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
		// get request
		$params = $this->Request->get();

		if (!isset($params['quest']) || empty($params['quest'])) $this->printJson($this->invalid(false, 403, "invalid data!"));
		if ($this->e($params['quest']) !== "all") $this->printJson($this->invalid(false, 403, "invalid data!"));

		$quests = $this->DB->query("
			SELECT
			id,
			quest,
			bu,
			area,
			cat,
			status
			FROM 
			db_quest
			WHERE flag='0'
			ORDER BY id ASC
		");

		if (!$quests) $this->printJson($this->invalid(false, 403, "Quest not found!"));

		for ($i=0; $i < count($quests); $i++) $quests[$i]['quest'] = hex2bin(strip_tags($this->e($quests[$i]['quest'])));

		$this->printJson($this->invalid(true, 200, "ok", $quests));
	}

	public function newExam()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

		// get request
		$params = $this->Request->get();

		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
		if (!isset($params['category']) || empty($params['category'])) $this->printJson($this->invalid(false, 403, "This category cannot be empty!"));
		if (!isset($_POST['exam']) || empty($_POST['exam'])) $this->printJson($this->invalid(false, 403, "This exam cannot be empty!"));
		if (!isset($params['answerA']) || empty($params['answerA'])) $this->printJson($this->invalid(false, 403, "This answer A cannot be empty!"));
		if (!isset($params['answerB']) || empty($params['answerB'])) $this->printJson($this->invalid(false, 403, "This answer B cannot be empty!"));
		if (!isset($params['answerC']) || empty($params['answerC'])) $this->printJson($this->invalid(false, 403, "This answer C cannot be empty!"));
		if (!isset($params['answerD']) || empty($params['answerD'])) $this->printJson($this->invalid(false, 403, "This answer D cannot be empty!"));
		if (!isset($params['answerKey']) || empty($params['answerKey'])) $this->printJson($this->invalid(false, 403, "This answer key cannot be empty!"));

		if (strlen($this->e($params['bu'])) < 3) $this->printJson($this->invalid(false, 403, "This bu minimum length 3 characters!"));
		if (strlen($this->e($params['area'])) < 3) $this->printJson($this->invalid(false, 403, "This area minimum length 3 characters!"));
		if (strlen($this->e($params['category'])) < 3) $this->printJson($this->invalid(false, 403, "This category minimum length 3 characters!"));
		if (strlen($this->e(base64_decode($_POST['exam']))) < 6) $this->printJson($this->invalid(false, 403, "This exam minimum length 6 characters!"));
		if (strlen($this->e($params['answerA'])) < 3) $this->printJson($this->invalid(false, 403, "This answer A minimum length 3 characters!"));
		if (strlen($this->e($params['answerB'])) < 3) $this->printJson($this->invalid(false, 403, "This answer B minimum length 3 characters!"));
		if (strlen($this->e($params['answerC'])) < 3) $this->printJson($this->invalid(false, 403, "This answer C minimum length 3 characters!"));
		if (strlen($this->e($params['answerD'])) < 3) $this->printJson($this->invalid(false, 403, "This answer D minimum length 3 characters!"));
		if (strtolower($this->e($params['answerKey'])) == "A" || strtolower($this->e($params['answerKey'])) == "B" || strtolower($this->e($params['answerKey'])) == "C" || strtolower($this->e($params['answerKey'])) == "D") $this->printJson($this->invalid(false, 403, "This answer key isn't valid!"));

			// $_POST['exam'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', base64_decode($_POST['exam'])); // remove text non ascii
			// $_POST['exam'] = str_replace("  ", "", $_POST['exam']);

		$dataTable = [
			"quest" => $this->e(bin2hex(base64_decode($_POST['exam']))),
			"quest_a" => $this->e(bin2hex($params['answerA'])),
			"quest_b" => $this->e(bin2hex($params['answerB'])),
			"quest_c" => $this->e(bin2hex($params['answerC'])),
			"quest_d" => $this->e(bin2hex($params['answerD'])),
			"answer_key" => $this->e($params['answerKey']),
			"image" => "",
			"created" => time(),
			"status" => 1,
			"bu" => $this->e($params['bu']),
			"area" => $this->e($params['area']),
			"cat" => $this->e($params['category']),
		];

		if (isset($params['on-image']) && $params['on-image'] == true) {

			if (!isset($_FILES['image']) || empty($_FILES['image'])) $this->printJson($this->invalid(false, 403, "This image cannot be empty!"));
			$filterImg = $this->filterImg($_FILES['image']);
			if ($filterImg['status'] !== true) $this->printJson($this->invalid(false, 403, $filterImg['msg']));

			// Random file name
			$randFilename = $this->randString(50);

			$fileExam = [
				'size'		=> trim($_FILES['image']['size']),
				'tmp'		=> trim($_FILES['image']['tmp_name']),
				'pixel'		=> @getimagesize($_FILES['image']['tmp_name']),
				'error'		=> trim($_FILES['image']['error']),
				'extension'	=> explode(".", trim($_FILES['image']['name'])),
			];

			$img = [
				'filename'	=> $randFilename.".".end($fileExam['extension']),
				'dir'	=> "assets/img/exam/",
			];

			if (end($fileExam['extension']) == 'svg') $this->printJson($this->invalid(false, 403, "The file must be an image!"));

			// valid size
			if ($fileExam['size'] > 6000000) $this->printJson($this->invalid(false, 403, "Max size 6MB!"));

			// valid pixel
			if ($fileExam['pixel'][0] > 5000 && $fileExam['pixel'][1] > 5000) $this->printJson($this->invalid(false, 403, "Upload JPG or PNG image. 5000 x 5000 required!"));

			// Upload Image
			$upFileExam = move_uploaded_file($fileExam['tmp'], $img['dir'] . $img['filename']);

			// valid upload image
			if (!$upFileExam) if (file_exists($img['dir'] . $img['filename'])) unlink($img['dir'] . $img['filename']);

			// if (file_exists($img['dir'] . $user['img'])) unlink($img['dir'] . $user['img']);

			$dataTable['image'] = $img['filename'];
		}

		$insert = $this->DB->insertTB("db_quest", $dataTable);

		if (!$insert) $this->printJson($this->invalid(false, 403, "Add new exam failed!"));
		$this->printJson($this->invalid(true, 200, "Add new exam success."));
	}

	public function editExam()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

		// get request
		$params = $this->Request->get();

		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));
		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
		if (!isset($params['category']) || empty($params['category'])) $this->printJson($this->invalid(false, 403, "This category cannot be empty!"));
		if (!isset($_POST['exam']) || empty($_POST['exam'])) $this->printJson($this->invalid(false, 403, "This exam cannot be empty!"));
		if (!isset($params['answerA']) || empty($params['answerA'])) $this->printJson($this->invalid(false, 403, "This answer A cannot be empty!"));
		if (!isset($params['answerB']) || empty($params['answerB'])) $this->printJson($this->invalid(false, 403, "This answer B cannot be empty!"));
		if (!isset($params['answerC']) || empty($params['answerC'])) $this->printJson($this->invalid(false, 403, "This answer C cannot be empty!"));
		if (!isset($params['answerD']) || empty($params['answerD'])) $this->printJson($this->invalid(false, 403, "This answer D cannot be empty!"));
		if (!isset($params['answerKey']) || empty($params['answerKey'])) $this->printJson($this->invalid(false, 403, "This answer key cannot be empty!"));

		if (strlen($this->e($params['bu'])) < 3) $this->printJson($this->invalid(false, 403, "This bu minimum length 3 characters!"));
		if (strlen($this->e($params['area'])) < 3) $this->printJson($this->invalid(false, 403, "This area minimum length 3 characters!"));
		if (strlen($this->e($params['category'])) < 3) $this->printJson($this->invalid(false, 403, "This category minimum length 3 characters!"));
		if (strlen($this->e(base64_decode($_POST['exam']))) < 6) $this->printJson($this->invalid(false, 403, "This exam minimum length 6 characters!"));
		if (strlen($this->e($params['answerA'])) < 3) $this->printJson($this->invalid(false, 403, "This answer A minimum length 3 characters!"));
		if (strlen($this->e($params['answerB'])) < 3) $this->printJson($this->invalid(false, 403, "This answer B minimum length 3 characters!"));
		if (strlen($this->e($params['answerC'])) < 3) $this->printJson($this->invalid(false, 403, "This answer C minimum length 3 characters!"));
		if (strlen($this->e($params['answerD'])) < 3) $this->printJson($this->invalid(false, 403, "This answer D minimum length 3 characters!"));
		if (strtolower($this->e($params['answerKey'])) == "A" || strtolower($this->e($params['answerKey'])) == "B" || strtolower($this->e($params['answerKey'])) == "C" || strtolower($this->e($params['answerKey'])) == "D") $this->printJson($this->invalid(false, 403, "This answer key isn't valid!"));
		
		$exam = $this->DB->selectTB("db_quest", "id", $this->e($params['id']), true);
		if (!$exam) $this->printJson($this->invalid(false, 403, "ID not found!"));

		# $_POST['exam'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', base64_decode($_POST['exam'])); // remove text non ascii
		// $_POST['exam'] = str_replace("  ", "", $_POST['exam']);

		$dataTable = [
			"quest" => $this->e(bin2hex(base64_decode($_POST['exam']))),
			"quest_a" => $this->e(bin2hex($params['answerA'])),
			"quest_b" => $this->e(bin2hex($params['answerB'])),
			"quest_c" => $this->e(bin2hex($params['answerC'])),
			"quest_d" => $this->e(bin2hex($params['answerD'])),
			"answer_key" => $this->e($params['answerKey']),
			"bu" => $this->e($params['bu']),
			"area" => $this->e($params['area']),
			"cat" => $this->e($params['category']),
		];

		if (isset($params['on-image']) && $params['on-image'] == true) {

			if (!isset($_FILES['image']) || empty($_FILES['image'])) $this->printJson($this->invalid(false, 403, "This image cannot be empty!"));
			$filterImg = $this->filterImg($_FILES['image']);
			if ($filterImg['status'] !== true) $this->printJson($this->invalid(false, 403, $filterImg['msg']));

			// Random file name
			$randFilename = $this->randString(50);

			$fileExam = [
				'size'		=> trim($_FILES['image']['size']),
				'tmp'		=> trim($_FILES['image']['tmp_name']),
				'pixel'		=> @getimagesize($_FILES['image']['tmp_name']),
				'error'		=> trim($_FILES['image']['error']),
				'extension'	=> explode(".", trim($_FILES['image']['name'])),
			];

			$img = [
				'filename'	=> $randFilename.".".end($fileExam['extension']),
				'dir'	=> "assets/img/exam/",
			];

			if (end($fileExam['extension']) == 'svg') $this->printJson($this->invalid(false, 403, "The file must be an image!"));

			// valid size
			if ($fileExam['size'] > 6000000) $this->printJson($this->invalid(false, 403, "Max size 6MB!"));

			// valid pixel
			if ($fileExam['pixel'][0] > 5000 && $fileExam['pixel'][1] > 5000) $this->printJson($this->invalid(false, 403, "Upload JPG or PNG image. 5000 x 5000 required!"));

			// Upload Image
			$upFileExam = move_uploaded_file($fileExam['tmp'], $img['dir'] . $img['filename']);

			// valid upload image
			if (!$upFileExam) if (!empty($img['filename']) && file_exists($img['dir'] . $img['filename'])) unlink($img['dir'] . $img['filename']);

			if (!empty($exam['image']) && file_exists($img['dir'] . $exam['image'])) unlink($img['dir'] . $exam['image']);

			$dataTable['image'] = $img['filename'];
		}

		$update = $this->DB->updateTB("db_quest", $dataTable, "id", $this->e($params['id']));

		if (!$update) $this->printJson($this->invalid(false, 403, "Update exam failed!"));
		$this->printJson($this->invalid(true, 200, "Update exam success."));
	}

	public function printExam()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

			// get request
		$params = $this->Request->get();

		// Filter username
		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));

		// Get User
		$exam = $this->DB->selectTB("db_quest", "id", $this->e($params['id']), true);

		// Login Failed
		if (!$exam) $this->printJson($this->invalid(false, 403, "Exam not found!"));

		$exam['quest'] = hex2bin(strip_tags($this->e($exam['quest'])));
		$exam['quest_a'] = hex2bin($this->e($exam['quest_a']));
		$exam['quest_b'] = hex2bin($this->e($exam['quest_b']));
		$exam['quest_c'] = hex2bin($this->e($exam['quest_c']));
		$exam['quest_d'] = hex2bin($this->e($exam['quest_d']));

		if (isset($exam['image']) && !empty($this->e($exam['image']))) {
			$exam['image'] = $this->base_url("assets/img/exam/".$this->e($exam['image']));
		}

		$response = $this->invalid(true, 200, "OK", $exam);
		$this->printJson($response);
	}

	public function setExamp()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

		// get request
		$params = $this->Request->get();

		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));

		if (!isset($params['status']) || empty($params['status'])) $this->printJson($this->invalid(false, 403, "This status cannot be empty!"));

		$exam = $this->DB->selectTB("db_quest", "id", $this->e($params['id']), true);

		if (!$exam) $this->printJson($this->invalid(false, 403, "Exam not found!"));

		if ($this->e(strtolower($params['status'])) !== "y" && $this->e(strtolower($params['status'])) !== "n") $this->printJson($this->invalid(false, 403, "Error status exam!"));

		if ($exam['status'] == $this->e($params['status'])) $this->printJson($this->invalid(false, 403, "Exam cannot be edited!"));

		$dataTable = [
			"status" => ($this->e(strtolower($params['status'])) == "y" ? 1:0),
		];

		$update = $this->DB->updateTB("db_quest", $dataTable, "id", $this->e($params['id']));

		if (!$update) $this->printJson($this->invalid(false, 403, "Edit exam failed!"));
		$this->printJson($this->invalid(true, 200, "Edit exam success."));
	}

	public function deltExam()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

		// get request
		$params = $this->Request->get();

		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));

		$exam = $this->DB->selectTB("db_quest", "id", $this->e($params['id']), true);

		if (!$exam) $this->printJson($this->invalid(false, 403, "Exam not found!"));

		if (!empty($exam['image']) && file_exists("assets/img/exam/".$exam['image'])) unlink("assets/img/exam/".$exam['image']);

		// $deleted = $this->DB->deltTB("db_quest", "id", $this->e($params['id']));
		$dataTable = [
			"flag" => 1
		];

		$deleted = $this->DB->updateTB("db_quest", $dataTable, "id", $this->e($params['id']));

		if (!$deleted) $this->printJson($this->invalid(false, 403, "Delete exam failed!"));
		$this->printJson($this->invalid(true, 200, "Delete exam success."));
	}

	public function printUser()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

		// get request
		$params = $this->Request->get();

		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));

		// Get User
		$user = $this->DB->selectTB("db_members", "id", $this->e($params['id']), true);

		// Login Failed
		if (!$user) $this->printJson($this->invalid(false, 403, "User not found!"));
		unset($user['password']);
		$response = $this->invalid(true, 200, "OK", $user);
		$this->printJson($response);
	}

	public function addNewUser()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

		// get request
		$params = $this->Request->get();

		if (!isset($params['username']) || empty($params['username'])) $this->printJson($this->invalid(false, 403, "This username cannot be empty!"));
		if (!isset($params['name']) || empty($params['name'])) $this->printJson($this->invalid(false, 403, "This name cannot be empty!"));
		if (!isset($params['password']) || empty($params['password'])) $this->printJson($this->invalid(false, 403, "This password cannot be empty!"));
		if (!isset($params['departement']) || empty($params['departement'])) $this->printJson($this->invalid(false, 403, "This departement cannot be empty!"));
		if (!isset($params['phone']) || empty($params['phone'])) $this->printJson($this->invalid(false, 403, "This phone cannot be empty!"));
		if (!isset($params['address']) || empty($params['address'])) $this->printJson($this->invalid(false, 403, "This address cannot be empty!"));

		if (!is_numeric($this->e($params['username'])) && !intval($this->e($params['username']))) $this->printJson($this->invalid(false, 403, "Username must number only!"));

		// Get User
		$user = $this->DB->selectTB("db_members", "id", $this->e($params['username']), true);

		// valid user
		if ($user) $this->printJson($this->invalid(false, 403, "This username already exist!"));
		
		$dataTable = [
			"id" => $this->e($params['username']),
			"name" => ucwords(strtolower($this->e($params['name']))),
			"password" => $this->e($params['password']),
			"dept" => $this->e($params['departement']),
			"phone" => $this->e($params['phone']),
			"address" => $this->e($params['address']),
			"status" => "1",
			"created" => time(),
		];

		$insert = $this->DB->insertTB("db_members", $dataTable);

		if (!$insert) $this->printJson($this->invalid(false, 403, "Add new user failed!"));
		$this->printJson($this->invalid(true, 200, "Add new user success."));
	}

	public function editUser()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

		// get request
		$params = $this->Request->get();

		if (!isset($params['username']) || empty($params['username'])) $this->printJson($this->invalid(false, 403, "This username cannot be empty!"));
		if (!isset($params['name']) || empty($params['name'])) $this->printJson($this->invalid(false, 403, "This name cannot be empty!"));
		if (!isset($params['password']) || empty($params['password'])) $this->printJson($this->invalid(false, 403, "This password cannot be empty!"));
		if (!isset($params['departement']) || empty($params['departement'])) $this->printJson($this->invalid(false, 403, "This departement cannot be empty!"));
		if (!isset($params['phone']) || empty($params['phone'])) $this->printJson($this->invalid(false, 403, "This phone cannot be empty!"));
		if (!isset($params['address']) || empty($params['address'])) $this->printJson($this->invalid(false, 403, "This address cannot be empty!"));

		if (!is_numeric($this->e($params['username'])) && !intval($this->e($params['username']))) $this->printJson($this->invalid(false, 403, "Username must number only!"));

		// Get User
		$user = $this->DB->selectTB("db_members", "id", $this->e($params['username']), true);

		// valid user
		if (!$user) $this->printJson($this->invalid(false, 403, "User not found!"));

		$dataTable = [
			"name" => ucwords(strtolower($this->e($params['name']))),
			"password" => $this->e($params['password']),
			"dept" => $this->e($params['departement']),
			"phone" => $this->e($params['phone']),
			"address" => $this->e($params['address']),
		];

		$excecute = $this->DB->updateTB("db_members", $dataTable, "id", $this->e($params['username']));

		if (!$excecute) $this->printJson($this->invalid(false, 403, "Edit user failed!"));
		$this->printJson($this->invalid(true, 200, "Edit user success."));
	}

	public function setUser()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

		// get request
		$params = $this->Request->get();

		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));

		if (!isset($params['status']) || empty($params['status'])) $this->printJson($this->invalid(false, 403, "This status cannot be empty!"));

		$user = $this->DB->selectTB("db_members", "id", $this->e($params['id']), true);

		if (!$user) $this->printJson($this->invalid(false, 403, "User not found!"));

		if ($this->e(strtolower($params['status'])) !== "y" && $this->e(strtolower($params['status'])) !== "n") $this->printJson($this->invalid(false, 403, "Error status user!"));

		if ($user['status'] == ($this->e(strtoupper($params['status'])) == "Y" ? 1:0)) $this->printJson($this->invalid(false, 403, "User cannot be edited!"));

		$dataTable = [
			"status" => ($this->e(strtoupper($params['status'])) == "Y" ? 1:0),
		];

		$update = $this->DB->updateTB("db_members", $dataTable, "id", $this->e($params['id']));

		if (!$update) $this->printJson($this->invalid(false, 403, "Edit user failed!"));
		$this->printJson($this->invalid(true, 200, "Edit user success."));
	}

	public function deltUser()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

		// get request
		$params = $this->Request->get();

		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));

		$user = $this->DB->selectTB("db_members", "id", $this->e($params['id']), true);

		if (!$user) $this->printJson($this->invalid(false, 403, "User not found!"));

		$deleted = $this->DB->deltTB("db_members", "id", $this->e($params['id']));

		if (!$deleted) $this->printJson($this->invalid(false, 403, "Delete user failed!"));
		$this->printJson($this->invalid(true, 200, "Delete user success."));
	}

	public function examSettings()
	{
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

		// get request
		$params = $this->Request->get();

		if (!isset($params['title']) || empty($params['title'])) $this->printJson($this->invalid(false, 403, "This title cannot be empty!"));
		if (!isset($params['time']) || empty($params['time'])) $this->printJson($this->invalid(false, 403, "This time cannot be empty!"));
		if (!isset($params['val']) || empty($params['val'])) $this->printJson($this->invalid(false, 403, "This val cannot be empty!"));
		if (!isset($_POST['regulation']) || empty($this->e($_POST['regulation']))) $this->printJson($this->invalid(false, 403, "This regulation cannot be empty!"));

		$dataTable = [
			"Subject" => $this->e($params['title']),
			"timer" => $this->e($params['time']),
			"min_val" => $this->e($params['val']),
			"rule" => $this->e($_POST['regulation']),
		];

		$update = $this->DB->updateTB("db_regulation", $dataTable, "id", 1);

		if (!$update) $this->printJson($this->invalid(false, 403, "Update failed!"));
		$this->printJson($this->invalid(true, 200, "Update success."));
	}

}

// /**
//  * SYSTEM ONLY
//  */

// use Controller\Controller;

// class v6 extends Controller
// {
	
// 	function __construct()
// 	{
// 		$this->DB = (in_array("db_models", get_declared_classes()) ? new db_models():$this->model('db_models'));
// 		$this->config();
// 		$this->Request = $this->helper("Request"); 
// 	}

// 	public function login()
// 	{
// 		// Check session login
// 		if (isset($_SESSION['username']) && !empty($_SESSION['username'])) $this->printJson($this->invalid(false, 403, "This session already exist!", ["url" => $this->base_url().""]));

// 		// get request
// 		$params = $this->Request->get();

// 		// Filter username
// 		if (!isset($params['uname']) || empty($params['uname'])) $this->printJson($this->invalid(false, 403, "This username cannot be empty!"));

// 		// Filter Password
// 		if (!isset($params['password']) || empty($params['password'])) $this->printJson($this->invalid(false, 403, "This password cannot be empty!"));

// 		// Get User
// 		$user = $this->DB->selectTB("db_admin", "id", $this->e($params['uname']), true);

// 		// Login Failed
// 		if (!$user) $this->printJson($this->invalid(false, 403, "This username isn't registered"));

// 		// Valid password
// 		if (!password_verify($this->e($params['password']), $this->w3llDecode($user['password']))) $this->printJson($this->invalid(false, 403, "Wrong Password"));

// 		// Create session login
// 		$_SESSION['username'] = $this->e($user['id']);

// 		// Login success
// 		$response = $this->invalid(true, 200, "Login Success");
// 		$this->printJson($response);
// 	}

// 	public function manegeExamBu()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
		
// 		$bu = $this->w3llDecode($this->e($params['bu']));

// 		if (!$bu) $this->printJson($this->invalid(false, 403, "This bu isn't valid!"));

// 		$catBu = $this->DB->query("SELECT area FROM db_quest WHERE BU='$bu' GROUP BY area");

// 		if (!$catBu) $this->printJson($this->invalid(false, 403, "Bu category not found!"));
// 		$this->printJson($this->invalid(true, 200, "ok", $catBu));
// 	}

// 	public function manegeExamArea()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
// 		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
			
// 		$bu = $this->w3llDecode($this->e($params['bu']));

// 		if (!$bu) $this->printJson($this->invalid(false, 403, "This bu isn't valid!"));

// 		$cats = [];
// 		$cat = [];

// 		$quests = $this->DB->query("
// 			SELECT
// 			id,
// 			quest,
// 			bu,
// 			area,
// 			cat,
// 			status
// 			FROM 
// 			db_quest
// 			WHERE 
// 			bu='".$this->e($bu)."'
// 			AND flag='0'
// 			AND area='".$this->e($params['area'])."'
// 			ORDER BY id ASC
// 		");

// 		if (!$quests) $this->printJson($this->invalid(false, 403, "Quest not found!"));

// 		for ($i=0; $i < count($quests); $i++) $quests[$i]['quest'] = hex2bin(strip_tags($this->e($quests[$i]['quest'])));
// 		for ($i=0; $i < count($quests); $i++) $cats[$quests[$i]['cat']] = $quests[$i]['cat'];
// 		foreach ($cats as $val) array_push($cat, $val);

// 		$dataTable = [
// 			"cat" => $cat,
// 			"quests" => $quests,
// 		];

// 		$this->printJson($this->invalid(true, 200, "ok", $dataTable));
// 	}

// 	public function manegeExamCat()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
// 		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
// 		if (!isset($params['cat']) || empty($params['cat'])) $this->printJson($this->invalid(false, 403, "This category cannot be empty!"));

// 		$bu = $this->w3llDecode($this->e($params['bu']));

// 		if (!$bu) $this->printJson($this->invalid(false, 403, "This bu isn't valid!"));

// 		$quests = $this->DB->query("
// 			SELECT
// 			id,
// 			quest,
// 			bu,
// 			area,
// 			cat,
// 			status
// 			FROM 
// 			db_quest
// 			WHERE 
// 			bu='".$this->e($bu)."'
// 			AND flag='0'
// 			AND area='".$this->e($params['area'])."'
// 			AND cat='".$this->e($params['cat'])."'
// 			ORDER BY id ASC
// 		");

// 		if (!$quests) $this->printJson($this->invalid(false, 403, "Quest not found!"));

// 		for ($i=0; $i < count($quests); $i++) $quests[$i]['quest'] = hex2bin(strip_tags($this->e($quests[$i]['quest'])));

// 		$this->printJson($this->invalid(true, 200, "ok", $quests));
// 	}

// 	public function sortExamResult()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
// 		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
			
// 		$bu = $this->w3llDecode($this->e($params['bu']));

// 		if (!$bu) $this->printJson($this->invalid(false, 403, "This bu isn't valid!"));
// 		$exp = 14515200;
// 		$quests = $this->DB->query("
// 			SELECT
// 			a.id,
// 			a.uid,
// 			b.name,
// 			a.bu,
// 			a.area,
// 			a.acc,
// 			a.created
// 			FROM 
// 			db_start_test a,
// 			db_members b
// 			WHERE
// 			a.flag = '1'
// 			AND a.is_delt = '0'
// 			AND bu='".$this->e($bu)."'
// 			AND area='".$this->e($params['area'])."'
// 			AND a.uid = b.id
// 			AND a.created <= '". (time()+$exp) ."'
// 			-- GROUP BY a.uid, a.bu, a.area
// 			ORDER BY a.created DESC
// 		");

// 		if (!$quests) $this->printJson($this->invalid(false, 403, "Quest not found!"));

// 		$setQuests = $this->array_group($quests, "uid", "bu", "area");
		
// 		$resultSet = [];
// 		foreach ($setQuests as $key => $setQuest) {
// 			$setQuest[0]['created'] = date("Y-m-d", $setQuest[0]['created']);
// 			if (count($setQuest) >= 2) {
// 				$found = false;
// 				for ($i=0; $i < count($setQuest); $i++) { 
// 					if ($setQuest[$i]['acc'] == 1) $resultSet[] = $setQuest[$i];
// 					if ($setQuest[$i]['acc'] == 1) $found = true;
// 					if ($setQuest[$i]['acc'] == 1) break;
// 				}

// 				if (!$found) $resultSet[] = $setQuest[0];
// 			} else {
// 				$resultSet[] = $setQuest[0];
// 			}
// 		}

// 		$quests = $resultSet;

// 		$this->printJson($this->invalid(true, 200, "ok", $quests));
// 	}

// 	public function sortAllExamResult()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['quest']) || empty($params['quest'])) $this->printJson($this->invalid(false, 403, "This quest cannot be empty!"));
// 		if ($params['quest'] !== "all") $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));

// 		$exp = 14515200;
// 		$quests = $this->DB->query("
// 			SELECT
// 			a.id,
// 			a.uid,
// 			b.name,
// 			a.bu,
// 			a.area,
// 			a.acc,
// 			a.created
// 			FROM 
// 			db_start_test a,
// 			db_members b
// 			WHERE
// 			a.flag = '1'
// 			AND a.is_delt = '0'
// 			AND a.uid = b.id
// 			AND a.created <= '". (time()+$exp) ."'
// 			-- GROUP BY a.uid, a.bu, a.area
// 			ORDER BY a.created DESC
// 		");

// 		if (!$quests) $this->printJson($this->invalid(false, 403, "Quest not found!"));

// 		$setQuests = $this->array_group($quests, "uid", "bu", "area");
		
// 		$resultSet = [];
// 		foreach ($setQuests as $key => $setQuest) {
// 			$setQuest[0]['created'] = date("Y-m-d", $setQuest[0]['created']);
// 			if (count($setQuest) >= 2) {
// 				$found = false;
// 				for ($i=0; $i < count($setQuest); $i++) { 
// 					if ($setQuest[$i]['acc'] == 1) $resultSet[] = $setQuest[$i];
// 					if ($setQuest[$i]['acc'] == 1) $found = true;
// 					if ($setQuest[$i]['acc'] == 1) break;
// 				}

// 				if (!$found) $resultSet[] = $setQuest[0];
// 			} else {
// 				$resultSet[] = $setQuest[0];
// 			}
// 		}

// 		$quests = $resultSet;

// 		$this->printJson($this->invalid(true, 200, "ok", $quests));
// 	}

// 	public function resetResultExam()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));
// 		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
// 		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));

// 		$check = $this->DB->query("
// 			SELECT 
// 			* 
// 			FROM
// 			db_start_test
// 			WHERE
// 			flag='1'
// 			AND acc='1'
// 			AND uid='". $this->e($params['id']) ."'
// 			AND bu='". $this->e($params['bu']) ."'
// 			AND area='". $this->e($params['area']) ."'
// 		");

// 		if (!$check) $this->printJson($this->invalid(false, 404, "No data!"));

// 		$isUpdate = true;

// 		foreach ($check as $dataTable) {
// 			$dataTable['acc'] = "0";

// 			$update = $this->DB->updateTB("db_start_test", $dataTable, "id", $this->e($dataTable['id']));

// 			if (!$update) $isUpdate = false;
// 			if (!$update) break;
// 		}

// 		if (!$isUpdate) $this->printJson($this->invalid(false, 403, "Reset Failed!"));
// 		if ($isUpdate) $this->printJson($this->invalid(true, 200, "Reset success."));
// 	}

// 	public function deleteResultExam()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));
// 		if (!isset($params['sid']) || empty($params['sid'])) $this->printJson($this->invalid(false, 403, "This sid cannot be empty!"));

// 		$load = $this->DB->query("SELECT * FROM db_start_test WHERE flag='1' AND is_delt='0' AND uid='". $this->e($params['id']) ."' AND sid='". $this->e($params['sid']) ."'", 1);
// 		if (!$load) $this->printJson($this->invalid(false, 404, "No data!"));

// 		$dataTable = [
// 			"is_delt" => 1
// 		];

// 		$deleted = $this->DB->updateTB("db_start_test", $dataTable, "id", $this->e($load['id']));

// 		if (!$deleted) $this->printJson($this->invalid(false, 403, "Delete exam failed!"));
// 		$this->printJson($this->invalid(true, 200, "Delete exam success."));
// 	}

// 	public function printDetailExamResult()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));
// 		if (!isset($params['sid']) || empty($params['sid'])) $this->printJson($this->invalid(false, 403, "This sid cannot be empty!"));
// 		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
// 		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));

// 		$users = $this->DB->query("
// 			SELECT 
// 			a.sid,
// 			b.name,
// 			a.uid,
// 			c.bu,
// 			c.area,
// 			c.cat,
// 			a.answer,
// 			c.answer_key,
// 			a.created,
// 			d.min_value
// 			FROM db_answer a, db_members b, db_quest c, db_start_test d
// 			WHERE a.uid='". $this->e($params['id']) ."' 
// 			AND a.bu = '". $this->e($params['bu']) ."'
// 			AND a.area = '". $this->e($params['area']) ."'
// 			AND a.sid = '". $this->e($params['sid']) ."'
// 			AND a.flag='1'
// 			AND a.uid=b.id
// 			AND a.qid=c.id
// 			AND a.cat=c.cat
// 			AND a.uid=d.uid
// 			AND a.sid=d.sid
// 			AND a.wave=(d.try+1)
// 			ORDER BY a.created desc
// 		");
// 		$regulation = $this->DB->selectTB("db_regulation", "id", 1, 1);
// 		if (!$users || !$regulation) $this->printJson($this->invalid(false, 403, "Data isn't valid!"));

// 		$result = [];
// 		$i=0;
// 		$_historys = $this->array_group($users, "sid", "cat");
// 		foreach ($_historys as $historys) {
// 			$f = 0;
// 			$t = 0;
// 			foreach ($historys as $history) {
// 				$result[$i]['created'] = $history['created'];
// 				$result[$i]['name'] = $history['name'];
// 				$result[$i]['sid'] = $history['sid'];
// 				$result[$i]['cat'] = $history['cat'];
// 				$result[$i]['area'] = $history['area'];
// 				$result[$i]['bu'] = $history['bu'];
// 				$result[$i]['created'] = date("d-m-Y h:i:s a", $this->e($history['created']));
// 				if ($history['answer_key'] == $history['answer']) $t++;
// 				if ($history['answer_key'] !== $history['answer']) $f++;
// 			}
// 			$result[$i]['true'] = $t;
// 			$result[$i]['false'] = $f;
// 			$result[$i]['score'] = $this->e(substr(((100/count($historys))*$t), 0, 3), ".");
// 			$result[$i]['desc'] = ((100/count($historys))*$t >= $historys[$i]['min_value'] ? "Congrats":"Failed");

// 			$i++;
// 		}

//         $this->printJson($this->invalid(true, 200, "ok", $result));
// 	}

// 	public function printExamStatic()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));
// 		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
// 		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));

// 		$load = $this->DB->query("
// 			SELECT 
// 			a.uid,
// 			a.sid,
// 			a.qid,
// 			a.bu,
// 			a.area,
// 			c.cat,
// 			a.answer,
// 			c.answer_key,
// 			b.name,
// 			b.id,
// 			d.created,
// 			d.min_value
// 			FROM 
// 			db_answer a, 
// 			db_members b, 
// 			db_quest c, 
// 			db_start_test d
// 			WHERE 
// 			d.uid='". $this->e($params['id']) ."' 
// 			AND d.bu = '". $this->e($params['bu']) ."'
// 			AND d.area = '". $this->e($params['area']) ."'
// 			AND d.is_delt = '0'
// 			AND d.flag = '1'
// 			AND a.flag = '1'
// 			AND a.sid = d.sid
// 			AND a.uid = d.uid
// 			AND a.wave = (d.try+1)
// 			AND a.uid=b.id
// 			AND a.qid=c.id
// 			AND d.created BETWEEN '".(time() - (604800*2))."' AND '".time()."'
// 		");

// 		$regulation = $this->DB->selectTB("db_regulation", "id", 1, 1);
// 		if (!$load || !$regulation) $this->printJson($this->invalid(false, 403, "Data isn't valid!"));
		
// 		$user = [];
// 		$sampleResult = [];
// 		$result = [];
// 		$i=0;
// 		$_historys = $this->array_group($load, "sid", "cat");

// 		foreach ($_historys as $historys) {
// 			$f = 0;
// 			$t = 0;
// 			foreach ($historys as $history) {
// 				$user['name'] = $history['name'];
// 				$user['id'] = $history['id'];

// 				$sampleResult[$i]['label'] = $history['bu']." - ".$history['area']." - ".$history['cat'];
// 				$sampleResult[$i]['created'] = date("Y-m-d h:i:s", $history['created']);

// 				if ($history['answer_key'] == $history['answer']) $t++;
// 				if ($history['answer_key'] !== $history['answer']) $f++;
// 			}
// 			$sampleResult[$i]['score'] = $this->e(substr(((100/count($historys))*$t), 0, 3), ".");
// 			$i++;
// 		}

// 		$i=0;
// 		foreach ($this->array_group($sampleResult, "label") as $key => $samples) {
// 			$result[$i]['label'] = $key;
// 			$result[$i]['data'] = [];

// 			$x=0;
// 			foreach ($samples as $sample) {
// 				$result[$i]['data'][$x]['score'] = $sample['score'];
// 				$result[$i]['data'][$x]['created'] = $sample['created'];
// 				$x++;
// 			}
// 			$i++;
// 		}
// 		// foreach ($_historys as $historys) {
// 		// 	$f = 0;
// 		// 	$t = 0;
// 		// 	foreach ($historys as $history) {
// 		// 		$user['name'] = $history['name'];
// 		// 		$user['id'] = $history['id'];

// 		// 		$result[$i]['created'] = $history['created'];
// 		// 		$result[$i]['sid'] = $history['sid'];
// 		// 		$result[$i]['cat'] = $history['cat'];
// 		// 		$result[$i]['area'] = $history['area'];
// 		// 		$result[$i]['bu'] = $history['bu'];
// 		// 		$result[$i]['created'] = date("d-m-Y h:i:s a", $this->e($history['created']));

// 		// 		if ($history['answer_key'] == $history['answer']) $t++;
// 		// 		if ($history['answer_key'] !== $history['answer']) $f++;
// 		// 	}
// 		// 	$result[$i]['true'] = $t;
// 		// 	$result[$i]['false'] = $f;
// 		// 	$result[$i]['score'] = $this->e(substr(((100/count($historys))*$t), 0, 3), ".");
// 		// 	$result[$i]['desc'] = ((100/count($historys))*$t >= $historys[$i]['min_value'] ? "Congrats":"Failed");

// 		// 	$i++;
// 		// }
// 		$result = [
// 			"user" => $user,
// 			"data" => $result,
// 		];
// 		$this->printJson($this->invalid(true, 200, "ok", $result));
// 	}

// 	public function findPrintExamStatic()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['uid']) || empty($params['uid'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));
// 		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
// 		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
// 		if (!isset($params['start-date']) || empty($params['start-date'])) $this->printJson($this->invalid(false, 403, "This start date cannot be empty!"));
// 		if (!isset($params['end-date']) || empty($params['end-date'])) $this->printJson($this->invalid(false, 403, "This end date cannot be empty!"));

// 		$load = $this->DB->query("
// 			SELECT 
// 			a.uid,
// 			a.sid,
// 			a.qid,
// 			a.bu,
// 			a.area,
// 			c.cat,
// 			a.answer,
// 			c.answer_key,
// 			b.name,
// 			b.id,
// 			d.created,
// 			d.min_value
// 			FROM 
// 			db_answer a, 
// 			db_members b, 
// 			db_quest c, 
// 			db_start_test d
// 			WHERE 
// 			d.uid='". $this->e($params['uid']) ."' 
// 			AND d.bu = '". $this->e($params['bu']) ."'
// 			AND d.area = '". $this->e($params['area']) ."'
// 			AND d.is_delt = '0'
// 			AND d.flag = '1'
// 			AND a.flag = '1'
// 			AND a.sid = d.sid
// 			AND a.uid = d.uid
// 			AND a.wave = (d.try+1)
// 			AND a.uid=b.id
// 			AND a.qid=c.id
// 			AND FROM_UNIXTIME(d.created, '%Y-%m-%d') BETWEEN '".$this->e($params['start-date'])."' AND '".$this->e($params['end-date'])."'
// 		");

// 		$regulation = $this->DB->selectTB("db_regulation", "id", 1, 1);
// 		if (!$load || !$regulation) $this->printJson($this->invalid(false, 403, "Data isn't valid!"));
		
// 		$user = [];
// 		$sampleResult = [];
// 		$result = [];
// 		$i=0;
// 		$_historys = $this->array_group($load, "sid", "cat");

// 		foreach ($_historys as $historys) {
// 			$f = 0;
// 			$t = 0;
// 			foreach ($historys as $history) {
// 				$user['name'] = $history['name'];
// 				$user['id'] = $history['id'];

// 				$sampleResult[$i]['label'] = $history['bu']." - ".$history['area']." - ".$history['cat'];
// 				$sampleResult[$i]['created'] = date("Y-m-d h:i:s", $history['created']);

// 				if ($history['answer_key'] == $history['answer']) $t++;
// 				if ($history['answer_key'] !== $history['answer']) $f++;
// 			}
// 			$sampleResult[$i]['score'] = $this->e(substr(((100/count($historys))*$t), 0, 3), ".");
// 			$i++;
// 		}

// 		$i=0;
// 		foreach ($this->array_group($sampleResult, "label") as $key => $samples) {
// 			$result[$i]['label'] = $key;
// 			$result[$i]['data'] = [];

// 			$x=0;
// 			foreach ($samples as $sample) {
// 				$result[$i]['data'][$x]['score'] = $sample['score'];
// 				$result[$i]['data'][$x]['created'] = $sample['created'];
// 				$x++;
// 			}
// 			$i++;
// 		}
// 		// foreach ($_historys as $historys) {
// 		// 	$f = 0;
// 		// 	$t = 0;
// 		// 	foreach ($historys as $history) {
// 		// 		$user['name'] = $history['name'];
// 		// 		$user['id'] = $history['id'];

// 		// 		$result[$i]['created'] = $history['created'];
// 		// 		$result[$i]['sid'] = $history['sid'];
// 		// 		$result[$i]['cat'] = $history['cat'];
// 		// 		$result[$i]['area'] = $history['area'];
// 		// 		$result[$i]['bu'] = $history['bu'];
// 		// 		$result[$i]['created'] = date("d-m-Y h:i:s a", $this->e($history['created']));

// 		// 		if ($history['answer_key'] == $history['answer']) $t++;
// 		// 		if ($history['answer_key'] !== $history['answer']) $f++;
// 		// 	}
// 		// 	$result[$i]['true'] = $t;
// 		// 	$result[$i]['false'] = $f;
// 		// 	$result[$i]['score'] = $this->e(substr(((100/count($historys))*$t), 0, 3), ".");
// 		// 	$result[$i]['desc'] = ((100/count($historys))*$t >= $historys[$i]['min_value'] ? "Congrats":"Failed");

// 		// 	$i++;
// 		// }
// 		$result = [
// 			"user" => $user,
// 			"data" => $result,
// 		];
// 		$this->printJson($this->invalid(true, 200, "ok", $result));
// 	}

// 	public function printExamResult()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));
// 		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
// 		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));

// 		$users = $this->DB->query("
// 			SELECT 
// 			a.sid,
// 			b.name,
// 			a.uid,
// 			c.bu,
// 			c.area,
// 			DATE_FORMAT(FROM_UNIXTIME(d.created), '%Y-%m-%d %h:%i:%s %p') as 'created'
// 			FROM db_answer a, db_members b, db_quest c, db_start_test d
// 			WHERE a.uid='". $this->e($params['id']) ."' 
// 			AND a.bu = '". $this->e($params['bu']) ."'
// 			AND a.area = '". $this->e($params['area']) ."'
// 			AND a.flag='1'
// 			AND d.is_delt='0'
// 			AND a.uid=b.id
// 			AND a.qid=c.id
// 			AND a.cat=c.cat
// 			AND a.uid=d.uid
// 			AND a.sid=d.sid
// 			GROUP BY a.sid
// 			ORDER BY a.created DESC
// 		");
// 		$regulation = $this->DB->selectTB("db_regulation", "id", 1, 1);
// 		if (!$users || !$regulation) $this->printJson($this->invalid(false, 403, "Data isn't valid!"));

//         $this->printJson($this->invalid(true, 200, "ok", $users));
// 	}

// 	public function manegeExamShowAll()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());
// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['quest']) || empty($params['quest'])) $this->printJson($this->invalid(false, 403, "invalid data!"));
// 		if ($this->e($params['quest']) !== "all") $this->printJson($this->invalid(false, 403, "invalid data!"));

// 		$quests = $this->DB->query("
// 			SELECT
// 			id,
// 			quest,
// 			bu,
// 			area,
// 			cat,
// 			status
// 			FROM 
// 			db_quest
// 			WHERE flag='0'
// 			ORDER BY id ASC
// 		");

// 		if (!$quests) $this->printJson($this->invalid(false, 403, "Quest not found!"));

// 		for ($i=0; $i < count($quests); $i++) $quests[$i]['quest'] = hex2bin(strip_tags($this->e($quests[$i]['quest'])));

// 		$this->printJson($this->invalid(true, 200, "ok", $quests));
// 	}

// 	public function newExam()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
// 		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
// 		if (!isset($params['category']) || empty($params['category'])) $this->printJson($this->invalid(false, 403, "This category cannot be empty!"));
// 		if (!isset($_POST['exam']) || empty($_POST['exam'])) $this->printJson($this->invalid(false, 403, "This exam cannot be empty!"));
// 		if (!isset($params['answerA']) || empty($params['answerA'])) $this->printJson($this->invalid(false, 403, "This answer A cannot be empty!"));
// 		if (!isset($params['answerB']) || empty($params['answerB'])) $this->printJson($this->invalid(false, 403, "This answer B cannot be empty!"));
// 		if (!isset($params['answerC']) || empty($params['answerC'])) $this->printJson($this->invalid(false, 403, "This answer C cannot be empty!"));
// 		if (!isset($params['answerD']) || empty($params['answerD'])) $this->printJson($this->invalid(false, 403, "This answer D cannot be empty!"));
// 		if (!isset($params['answerKey']) || empty($params['answerKey'])) $this->printJson($this->invalid(false, 403, "This answer key cannot be empty!"));

// 		if (strlen($this->e($params['bu'])) < 3) $this->printJson($this->invalid(false, 403, "This bu minimum length 3 characters!"));
// 		if (strlen($this->e($params['area'])) < 3) $this->printJson($this->invalid(false, 403, "This area minimum length 3 characters!"));
// 		if (strlen($this->e($params['category'])) < 3) $this->printJson($this->invalid(false, 403, "This category minimum length 3 characters!"));
// 		if (strlen($this->e(base64_decode($_POST['exam']))) < 6) $this->printJson($this->invalid(false, 403, "This exam minimum length 6 characters!"));
// 		if (strlen($this->e($params['answerA'])) < 3) $this->printJson($this->invalid(false, 403, "This answer A minimum length 3 characters!"));
// 		if (strlen($this->e($params['answerB'])) < 3) $this->printJson($this->invalid(false, 403, "This answer B minimum length 3 characters!"));
// 		if (strlen($this->e($params['answerC'])) < 3) $this->printJson($this->invalid(false, 403, "This answer C minimum length 3 characters!"));
// 		if (strlen($this->e($params['answerD'])) < 3) $this->printJson($this->invalid(false, 403, "This answer D minimum length 3 characters!"));
// 		if (strtolower($this->e($params['answerKey'])) == "A" || strtolower($this->e($params['answerKey'])) == "B" || strtolower($this->e($params['answerKey'])) == "C" || strtolower($this->e($params['answerKey'])) == "D") $this->printJson($this->invalid(false, 403, "This answer key isn't valid!"));

// 			// $_POST['exam'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', base64_decode($_POST['exam'])); // remove text non ascii
// 			// $_POST['exam'] = str_replace("  ", "", $_POST['exam']);

// 		$dataTable = [
// 			"quest" => $this->e(bin2hex(base64_decode($_POST['exam']))),
// 			"quest_a" => $this->e(bin2hex($params['answerA'])),
// 			"quest_b" => $this->e(bin2hex($params['answerB'])),
// 			"quest_c" => $this->e(bin2hex($params['answerC'])),
// 			"quest_d" => $this->e(bin2hex($params['answerD'])),
// 			"answer_key" => $this->e($params['answerKey']),
// 			"image" => "",
// 			"created" => time(),
// 			"status" => 1,
// 			"bu" => $this->e($params['bu']),
// 			"area" => $this->e($params['area']),
// 			"cat" => $this->e($params['category']),
// 		];

// 		if (isset($params['on-image']) && $params['on-image'] == true) {

// 			if (!isset($_FILES['image']) || empty($_FILES['image'])) $this->printJson($this->invalid(false, 403, "This image cannot be empty!"));
// 			$filterImg = $this->filterImg($_FILES['image']);
// 			if ($filterImg['status'] !== true) $this->printJson($this->invalid(false, 403, $filterImg['msg']));

// 			// Random file name
// 			$randFilename = $this->randString(50);

// 			$fileExam = [
// 				'size'		=> trim($_FILES['image']['size']),
// 				'tmp'		=> trim($_FILES['image']['tmp_name']),
// 				'pixel'		=> @getimagesize($_FILES['image']['tmp_name']),
// 				'error'		=> trim($_FILES['image']['error']),
// 				'extension'	=> explode(".", trim($_FILES['image']['name'])),
// 			];

// 			$img = [
// 				'filename'	=> $randFilename.".".end($fileExam['extension']),
// 				'dir'	=> "assets/img/exam/",
// 			];

// 			if (end($fileExam['extension']) == 'svg') $this->printJson($this->invalid(false, 403, "The file must be an image!"));

// 			// valid size
// 			if ($fileExam['size'] > 6000000) $this->printJson($this->invalid(false, 403, "Max size 6MB!"));

// 			// valid pixel
// 			if ($fileExam['pixel'][0] > 5000 && $fileExam['pixel'][1] > 5000) $this->printJson($this->invalid(false, 403, "Upload JPG or PNG image. 5000 x 5000 required!"));

// 			// Upload Image
// 			$upFileExam = move_uploaded_file($fileExam['tmp'], $img['dir'] . $img['filename']);

// 			// valid upload image
// 			if (!$upFileExam) if (file_exists($img['dir'] . $img['filename'])) unlink($img['dir'] . $img['filename']);

// 			// if (file_exists($img['dir'] . $user['img'])) unlink($img['dir'] . $user['img']);

// 			$dataTable['image'] = $img['filename'];
// 		}

// 		$insert = $this->DB->insertTB("db_quest", $dataTable);

// 		if (!$insert) $this->printJson($this->invalid(false, 403, "Add new exam failed!"));
// 		$this->printJson($this->invalid(true, 200, "Add new exam success."));
// 	}

// 	public function editExam()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));
// 		if (!isset($params['bu']) || empty($params['bu'])) $this->printJson($this->invalid(false, 403, "This bu cannot be empty!"));
// 		if (!isset($params['area']) || empty($params['area'])) $this->printJson($this->invalid(false, 403, "This area cannot be empty!"));
// 		if (!isset($params['category']) || empty($params['category'])) $this->printJson($this->invalid(false, 403, "This category cannot be empty!"));
// 		if (!isset($_POST['exam']) || empty($_POST['exam'])) $this->printJson($this->invalid(false, 403, "This exam cannot be empty!"));
// 		if (!isset($params['answerA']) || empty($params['answerA'])) $this->printJson($this->invalid(false, 403, "This answer A cannot be empty!"));
// 		if (!isset($params['answerB']) || empty($params['answerB'])) $this->printJson($this->invalid(false, 403, "This answer B cannot be empty!"));
// 		if (!isset($params['answerC']) || empty($params['answerC'])) $this->printJson($this->invalid(false, 403, "This answer C cannot be empty!"));
// 		if (!isset($params['answerD']) || empty($params['answerD'])) $this->printJson($this->invalid(false, 403, "This answer D cannot be empty!"));
// 		if (!isset($params['answerKey']) || empty($params['answerKey'])) $this->printJson($this->invalid(false, 403, "This answer key cannot be empty!"));

// 		if (strlen($this->e($params['bu'])) < 3) $this->printJson($this->invalid(false, 403, "This bu minimum length 3 characters!"));
// 		if (strlen($this->e($params['area'])) < 3) $this->printJson($this->invalid(false, 403, "This area minimum length 3 characters!"));
// 		if (strlen($this->e($params['category'])) < 3) $this->printJson($this->invalid(false, 403, "This category minimum length 3 characters!"));
// 		if (strlen($this->e(base64_decode($_POST['exam']))) < 6) $this->printJson($this->invalid(false, 403, "This exam minimum length 6 characters!"));
// 		if (strlen($this->e($params['answerA'])) < 3) $this->printJson($this->invalid(false, 403, "This answer A minimum length 3 characters!"));
// 		if (strlen($this->e($params['answerB'])) < 3) $this->printJson($this->invalid(false, 403, "This answer B minimum length 3 characters!"));
// 		if (strlen($this->e($params['answerC'])) < 3) $this->printJson($this->invalid(false, 403, "This answer C minimum length 3 characters!"));
// 		if (strlen($this->e($params['answerD'])) < 3) $this->printJson($this->invalid(false, 403, "This answer D minimum length 3 characters!"));
// 		if (strtolower($this->e($params['answerKey'])) == "A" || strtolower($this->e($params['answerKey'])) == "B" || strtolower($this->e($params['answerKey'])) == "C" || strtolower($this->e($params['answerKey'])) == "D") $this->printJson($this->invalid(false, 403, "This answer key isn't valid!"));
		
// 		$exam = $this->DB->selectTB("db_quest", "id", $this->e($params['id']), true);
// 		if (!$exam) $this->printJson($this->invalid(false, 403, "ID not found!"));

// 		# $_POST['exam'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', base64_decode($_POST['exam'])); // remove text non ascii
// 		// $_POST['exam'] = str_replace("  ", "", $_POST['exam']);

// 		$dataTable = [
// 			"quest" => $this->e(bin2hex(base64_decode($_POST['exam']))),
// 			"quest_a" => $this->e(bin2hex($params['answerA'])),
// 			"quest_b" => $this->e(bin2hex($params['answerB'])),
// 			"quest_c" => $this->e(bin2hex($params['answerC'])),
// 			"quest_d" => $this->e(bin2hex($params['answerD'])),
// 			"answer_key" => $this->e($params['answerKey']),
// 			"bu" => $this->e($params['bu']),
// 			"area" => $this->e($params['area']),
// 			"cat" => $this->e($params['category']),
// 		];

// 		if (isset($params['on-image']) && $params['on-image'] == true) {

// 			if (!isset($_FILES['image']) || empty($_FILES['image'])) $this->printJson($this->invalid(false, 403, "This image cannot be empty!"));
// 			$filterImg = $this->filterImg($_FILES['image']);
// 			if ($filterImg['status'] !== true) $this->printJson($this->invalid(false, 403, $filterImg['msg']));

// 			// Random file name
// 			$randFilename = $this->randString(50);

// 			$fileExam = [
// 				'size'		=> trim($_FILES['image']['size']),
// 				'tmp'		=> trim($_FILES['image']['tmp_name']),
// 				'pixel'		=> @getimagesize($_FILES['image']['tmp_name']),
// 				'error'		=> trim($_FILES['image']['error']),
// 				'extension'	=> explode(".", trim($_FILES['image']['name'])),
// 			];

// 			$img = [
// 				'filename'	=> $randFilename.".".end($fileExam['extension']),
// 				'dir'	=> "assets/img/exam/",
// 			];

// 			if (end($fileExam['extension']) == 'svg') $this->printJson($this->invalid(false, 403, "The file must be an image!"));

// 			// valid size
// 			if ($fileExam['size'] > 6000000) $this->printJson($this->invalid(false, 403, "Max size 6MB!"));

// 			// valid pixel
// 			if ($fileExam['pixel'][0] > 5000 && $fileExam['pixel'][1] > 5000) $this->printJson($this->invalid(false, 403, "Upload JPG or PNG image. 5000 x 5000 required!"));

// 			// Upload Image
// 			$upFileExam = move_uploaded_file($fileExam['tmp'], $img['dir'] . $img['filename']);

// 			// valid upload image
// 			if (!$upFileExam) if (!empty($img['filename']) && file_exists($img['dir'] . $img['filename'])) unlink($img['dir'] . $img['filename']);

// 			if (!empty($exam['image']) && file_exists($img['dir'] . $exam['image'])) unlink($img['dir'] . $exam['image']);

// 			$dataTable['image'] = $img['filename'];
// 		}

// 		$update = $this->DB->updateTB("db_quest", $dataTable, "id", $this->e($params['id']));

// 		if (!$update) $this->printJson($this->invalid(false, 403, "Update exam failed!"));
// 		$this->printJson($this->invalid(true, 200, "Update exam success."));
// 	}

// 	public function printExam()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

// 			// get request
// 		$params = $this->Request->get();

// 		// Filter username
// 		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));

// 		// Get User
// 		$exam = $this->DB->selectTB("db_quest", "id", $this->e($params['id']), true);

// 		// Login Failed
// 		if (!$exam) $this->printJson($this->invalid(false, 403, "Exam not found!"));

// 		$exam['quest'] = hex2bin(strip_tags($this->e($exam['quest'])));
// 		$exam['quest_a'] = hex2bin($this->e($exam['quest_a']));
// 		$exam['quest_b'] = hex2bin($this->e($exam['quest_b']));
// 		$exam['quest_c'] = hex2bin($this->e($exam['quest_c']));
// 		$exam['quest_d'] = hex2bin($this->e($exam['quest_d']));

// 		if (isset($exam['image']) && !empty($this->e($exam['image']))) {
// 			$exam['image'] = $this->base_url("assets/img/exam/".$this->e($exam['image']));
// 		}

// 		$response = $this->invalid(true, 200, "OK", $exam);
// 		$this->printJson($response);
// 	}

// 	public function setExamp()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));

// 		if (!isset($params['status']) || empty($params['status'])) $this->printJson($this->invalid(false, 403, "This status cannot be empty!"));

// 		$exam = $this->DB->selectTB("db_quest", "id", $this->e($params['id']), true);

// 		if (!$exam) $this->printJson($this->invalid(false, 403, "Exam not found!"));

// 		if ($this->e(strtolower($params['status'])) !== "y" && $this->e(strtolower($params['status'])) !== "n") $this->printJson($this->invalid(false, 403, "Error status exam!"));

// 		if ($exam['status'] == $this->e($params['status'])) $this->printJson($this->invalid(false, 403, "Exam cannot be edited!"));

// 		$dataTable = [
// 			"status" => ($this->e(strtolower($params['status'])) == "y" ? 1:0),
// 		];

// 		$update = $this->DB->updateTB("db_quest", $dataTable, "id", $this->e($params['id']));

// 		if (!$update) $this->printJson($this->invalid(false, 403, "Edit exam failed!"));
// 		$this->printJson($this->invalid(true, 200, "Edit exam success."));
// 	}

// 	public function deltExam()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));

// 		$exam = $this->DB->selectTB("db_quest", "id", $this->e($params['id']), true);

// 		if (!$exam) $this->printJson($this->invalid(false, 403, "Exam not found!"));

// 		if (!empty($exam['image']) && file_exists("assets/img/exam/".$exam['image'])) unlink("assets/img/exam/".$exam['image']);

// 		// $deleted = $this->DB->deltTB("db_quest", "id", $this->e($params['id']));
// 		$dataTable = [
// 			"flag" => 1
// 		];

// 		$deleted = $this->DB->updateTB("db_quest", $dataTable, "id", $this->e($params['id']));

// 		if (!$deleted) $this->printJson($this->invalid(false, 403, "Delete exam failed!"));
// 		$this->printJson($this->invalid(true, 200, "Delete exam success."));
// 	}

// 	public function printUser()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));

// 		// Get User
// 		$user = $this->DB->selectTB("db_members", "id", $this->e($params['id']), true);

// 		// Login Failed
// 		if (!$user) $this->printJson($this->invalid(false, 403, "User not found!"));
// 		unset($user['password']);
// 		$response = $this->invalid(true, 200, "OK", $user);
// 		$this->printJson($response);
// 	}

// 	public function addNewUser()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['username']) || empty($params['username'])) $this->printJson($this->invalid(false, 403, "This username cannot be empty!"));
// 		if (!isset($params['name']) || empty($params['name'])) $this->printJson($this->invalid(false, 403, "This name cannot be empty!"));
// 		if (!isset($params['password']) || empty($params['password'])) $this->printJson($this->invalid(false, 403, "This password cannot be empty!"));
// 		if (!isset($params['departement']) || empty($params['departement'])) $this->printJson($this->invalid(false, 403, "This departement cannot be empty!"));
// 		if (!isset($params['phone']) || empty($params['phone'])) $this->printJson($this->invalid(false, 403, "This phone cannot be empty!"));
// 		if (!isset($params['address']) || empty($params['address'])) $this->printJson($this->invalid(false, 403, "This address cannot be empty!"));

// 		if (!is_numeric($this->e($params['username'])) && !intval($this->e($params['username']))) $this->printJson($this->invalid(false, 403, "Username must number only!"));

// 		// Get User
// 		$user = $this->DB->selectTB("db_members", "id", $this->e($params['username']), true);

// 		// valid user
// 		if ($user) $this->printJson($this->invalid(false, 403, "This username already exist!"));
		
// 		$dataTable = [
// 			"id" => $this->e($params['username']),
// 			"name" => ucwords(strtolower($this->e($params['name']))),
// 			"password" => $this->e($params['password']),
// 			"dept" => $this->e($params['departement']),
// 			"phone" => $this->e($params['phone']),
// 			"address" => $this->e($params['address']),
// 			"status" => "1",
// 			"created" => time(),
// 		];

// 		$insert = $this->DB->insertTB("db_members", $dataTable);

// 		if (!$insert) $this->printJson($this->invalid(false, 403, "Add new user failed!"));
// 		$this->printJson($this->invalid(true, 200, "Add new user success."));
// 	}

// 	public function editUser()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['username']) || empty($params['username'])) $this->printJson($this->invalid(false, 403, "This username cannot be empty!"));
// 		if (!isset($params['name']) || empty($params['name'])) $this->printJson($this->invalid(false, 403, "This name cannot be empty!"));
// 		if (!isset($params['password']) || empty($params['password'])) $this->printJson($this->invalid(false, 403, "This password cannot be empty!"));
// 		if (!isset($params['departement']) || empty($params['departement'])) $this->printJson($this->invalid(false, 403, "This departement cannot be empty!"));
// 		if (!isset($params['phone']) || empty($params['phone'])) $this->printJson($this->invalid(false, 403, "This phone cannot be empty!"));
// 		if (!isset($params['address']) || empty($params['address'])) $this->printJson($this->invalid(false, 403, "This address cannot be empty!"));

// 		if (!is_numeric($this->e($params['username'])) && !intval($this->e($params['username']))) $this->printJson($this->invalid(false, 403, "Username must number only!"));

// 		// Get User
// 		$user = $this->DB->selectTB("db_members", "id", $this->e($params['username']), true);

// 		// valid user
// 		if (!$user) $this->printJson($this->invalid(false, 403, "User not found!"));

// 		$dataTable = [
// 			"name" => ucwords(strtolower($this->e($params['name']))),
// 			"password" => $this->e($params['password']),
// 			"dept" => $this->e($params['departement']),
// 			"phone" => $this->e($params['phone']),
// 			"address" => $this->e($params['address']),
// 		];

// 		$excecute = $this->DB->updateTB("db_members", $dataTable, "id", $this->e($params['username']));

// 		if (!$excecute) $this->printJson($this->invalid(false, 403, "Edit user failed!"));
// 		$this->printJson($this->invalid(true, 200, "Edit user success."));
// 	}

// 	public function setUser()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));

// 		if (!isset($params['status']) || empty($params['status'])) $this->printJson($this->invalid(false, 403, "This status cannot be empty!"));

// 		$user = $this->DB->selectTB("db_members", "id", $this->e($params['id']), true);

// 		if (!$user) $this->printJson($this->invalid(false, 403, "User not found!"));

// 		if ($this->e(strtolower($params['status'])) !== "y" && $this->e(strtolower($params['status'])) !== "n") $this->printJson($this->invalid(false, 403, "Error status user!"));

// 		if ($user['status'] == ($this->e(strtoupper($params['status'])) == "Y" ? 1:0)) $this->printJson($this->invalid(false, 403, "User cannot be edited!"));

// 		$dataTable = [
// 			"status" => ($this->e(strtoupper($params['status'])) == "Y" ? 1:0),
// 		];

// 		$update = $this->DB->updateTB("db_members", $dataTable, "id", $this->e($params['id']));

// 		if (!$update) $this->printJson($this->invalid(false, 403, "Edit user failed!"));
// 		$this->printJson($this->invalid(true, 200, "Edit user success."));
// 	}

// 	public function deltUser()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['id']) || empty($params['id'])) $this->printJson($this->invalid(false, 403, "This id cannot be empty!"));

// 		$user = $this->DB->selectTB("db_members", "id", $this->e($params['id']), true);

// 		if (!$user) $this->printJson($this->invalid(false, 403, "User not found!"));

// 		$deleted = $this->DB->deltTB("db_members", "id", $this->e($params['id']));

// 		if (!$deleted) $this->printJson($this->invalid(false, 403, "Delete user failed!"));
// 		$this->printJson($this->invalid(true, 200, "Delete user success."));
// 	}

// 	public function examSettings()
// 	{
// 		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) $this->printJson($this->invalid());

// 		// get request
// 		$params = $this->Request->get();

// 		if (!isset($params['title']) || empty($params['title'])) $this->printJson($this->invalid(false, 403, "This title cannot be empty!"));
// 		if (!isset($params['time']) || empty($params['time'])) $this->printJson($this->invalid(false, 403, "This time cannot be empty!"));
// 		if (!isset($params['val']) || empty($params['val'])) $this->printJson($this->invalid(false, 403, "This val cannot be empty!"));
// 		if (!isset($_POST['regulation']) || empty($this->e($_POST['regulation']))) $this->printJson($this->invalid(false, 403, "This regulation cannot be empty!"));

// 		$dataTable = [
// 			"Subject" => $this->e($params['title']),
// 			"timer" => $this->e($params['time']),
// 			"min_val" => $this->e($params['val']),
// 			"rule" => $this->e($_POST['regulation']),
// 		];

// 		$update = $this->DB->updateTB("db_regulation", $dataTable, "id", 1);

// 		if (!$update) $this->printJson($this->invalid(false, 403, "Update failed!"));
// 		$this->printJson($this->invalid(true, 200, "Update success."));
// 	}

// }