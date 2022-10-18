<?php

/**
* 
*/
use Dompdf\Options;
use Dompdf\Dompdf;
use Controller\Controller;

class otj extends Controller
{
	public $menuActive = false;
	public $defaultFile = 'admin/body';
	
	function __construct()
	{
		$this->config();
		$this->DB = $this->model('db_models');
                $this->curl = $this->helper('http');
                $this->dompdfOptions = new Options();
                $this->dompdf = new Dompdf();
                $this->dom = new DOMDocument();
                
	}

	public function index($id=false, $bu=false, $area=false, $cmd=false)
	{	
		if (!isset($_SESSION['username']) || empty($_SESSION['username'])) header("location: ".$this->base_url('/admin/login'));
                if (!isset($_SESSION['username']) || empty($_SESSION['username'])) exit();

                if (!$this->e($id) || !$this->e($bu) || !$this->e($area) || !$this->e($cmd)) $this->printJson($this->invalid());
                if ($this->e($cmd) !== "print") $this->printJson($this->invalid());        

                $user = $this->DB->selectTB("db_admin", "id", $this->e($_SESSION['username']), true);

                $data = [
                        "header" => [
                                "title" => "Dashboard",
                                "img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
                                "desc" => "",
                        ],
                        "menu" => $this->DB->getAllTB("tb_menu"),
                        "user" => ($user ? $user : false),
                        "regulation" => $this->DB->selectTB("db_regulation", "id", 1, 1),
                        "result" => $this->DB->query("
                                SELECT 
                                b.id,
                                a.sid,
                                b.name,
                                a.qid,
                                c.bu,
                                c.area,
                                c.cat,
                                a.answer,
                                c.answer_key,
                                a.created,
                                b.created AS 'join'
                                FROM db_answer a, db_members b, db_quest c
                                WHERE TO_BASE64(a.uid)='". $this->e($id) ."' 
                                AND TO_BASE64(a.bu) = '". $this->e($bu) ."'
                                AND TO_BASE64(a.area) = '". $this->e($area) ."'
                                AND a.flag='1'
                                AND a.uid=b.id
                                AND a.qid=c.id
                                AND a.cat=c.cat
                        "),
                ];

                if (!$data['result']) $this->printJson($this->invalid());      
                
                $result = [];
                $i=0;
                $_historys = $this->array_group($data['result'], "sid", "cat");
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
                                $result[$i]['id'] = $history['id'];
                                $result[$i]['join'] = $history['join'];
                                if ($history['answer_key'] == $history['answer']) $t++;
                                if ($history['answer_key'] !== $history['answer']) $f++;
                        }
                        $result[$i]['true'] = $t;
                        $result[$i]['false'] = $f;
                        $result[$i]['score'] = $this->e(substr(((100/count($historys))*$t), 0, 3), ".");
                        $result[$i]['desc'] = ((100/count($historys))*$t >= $data['regulation']['min_val'] ? "Congrats":"Failed");

                        $i++;
                }

                $data['result'] = $result;

                // $this->printJson($result);

                $views = [
                        "templates/otj/header",
                        "otj/print",
                        "templates/otj/footer",
                ];

                $this->views($views, $data);
	}

        public function print($id=false, $sid=false,$bu=false, $area=false, $cmd=false)
        {
                if (!isset($_SESSION['username']) || empty($_SESSION['username'])) header("location: ".$this->base_url('/admin/login'));
                if (!isset($_SESSION['username']) || empty($_SESSION['username'])) exit();

                if (!$this->e($id) || !$this->e($sid) || !$this->e($bu) || !$this->e($area) || !$this->e($cmd)) $this->printJson($this->invalid());
                if ($this->e($cmd) !== "1") $this->printJson($this->invalid());        

                $user = $this->DB->selectTB("db_admin", "id", $this->e($_SESSION['username']), true);

                $data = [
                        "header" => [
                                "title" => "Dashboard",
                                "img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
                                "desc" => "",
                        ],
                        "menu" => $this->DB->getAllTB("tb_menu"),
                        "user" => ($user ? $user : false),
                        "member" => $this->DB->query("
                                SELECT
                                b.*,
                                a.min_value
                                FROM db_start_test a, db_members b
                                WHERE TO_BASE64(a.uid)='". $this->e($id) ."' 
                                AND TO_BASE64(a.sid) = '". $this->e($sid) ."'
                                AND a.uid=b.id
                        ", 1),
                        "regulation" => $this->DB->selectTB("db_regulation", "id", 1, 1),
                        "result" => $this->DB->query("
                                SELECT 
                                b.id,
                                a.sid,
                                b.name,
                                b.join_date,
                                a.qid,
                                c.bu,
                                c.area,
                                c.cat,
                                a.answer,
                                c.answer_key,
                                a.wave,
                                a.created,
                                b.created AS 'join'
                                FROM db_answer a, db_members b, db_quest_results c
                                WHERE TO_BASE64(a.uid)='". $this->e($id) ."' 
                                AND TO_BASE64(a.sid) = '". $this->e($sid) ."'
                                AND TO_BASE64(a.bu) = '". $this->e($bu) ."'
                                AND TO_BASE64(a.area) = '". $this->e($area) ."'
                                AND a.flag='1'
                                AND a.uid=b.id
                                AND a.qid=c.qid
                                AND a.cat=c.cat
                                AND b.id=c.uid
                                AND a.sid=c.sid
                                order by a.created desc
                        "),
                ];


                if (!$data['result']) $this->printJson($this->invalid());      
                
                $data['header']['title'] = "Training Log - ".ucwords(strtolower($data['result'][0]['name']));

                $i=0;
                $sub_result = [];
                $_historys = $this->array_group($data['result'], "sid", "cat");
                foreach ($_historys as $key => $historys) {
                        $historys = $this->array_sort($historys, "wave");
                        foreach ($historys as $key => $history) $sub_result[$i][$history['cat']][$history['wave']][] = $history;
                        $i++;
                }

                $result = [];
                $i=0;
                foreach ($sub_result as $key1 => $sub_results) {
                        foreach ($sub_results as $key2 => $sub_result_) {
                                foreach ($sub_result_ as $key3 => $_sub_result_) {
                                        $tr = 0;
                                        $fl = 0;
                                        foreach ($_sub_result_ as $key4 => $results) {
                                                $result['bu'] = $results['bu'];
                                                $result['area'] = $results['area'];
                                                $result['join'] = $results['join'];
                                                $result['cat'][$results['cat']][$key3]['wave'] = $results['wave'];
                                                $result['cat'][$results['cat']][$key3]['created'] = $results['created'];

                                                if ($results['answer_key'] == $results['answer']) $tr++;
                                                if ($results['answer_key'] !== $results['answer']) $fl++;
                                        }
                                        $result['cat'][$results['cat']][$key3]['true'] = $tr;
                                        $result['cat'][$results['cat']][$key3]['false'] = $fl;
                                        $result['cat'][$results['cat']][$key3]['score'] = $this->e(substr(((100/count($_sub_result_))*$tr), 0, 3), ".");
                                        $result['cat'][$results['cat']][$key3]['desc'] = ((100/count($_sub_result_))*$tr >= $data['member']['min_value'] ? "Passed":"Failed");
                                        $result['cat'][$results['cat']][$key3]['clr'] = ((100/count($_sub_result_))*$tr >= $data['member']['min_value'] ? "success":"danger");
                                }
                        }
                        $i++;
                }
                
                $data['result'] = $result;

                // $this->printJson($data);

                $views = [
                        "templates/otj/header",
                        "otj/print",
                        "templates/otj/footer",
                ];

                $this->views($views, $data);
        }

        public function pdf($id=false, $bu=false, $area=false, $cmd=false)
        {

                if (!isset($_SESSION['username']) || empty($_SESSION['username'])) header("location: ".$this->base_url('/admin/login'));
                if (!isset($_SESSION['username']) || empty($_SESSION['username'])) exit();

                if (!$this->e($id) || !$this->e($bu) || !$this->e($area) || !$this->e($cmd)) $this->printJson($this->invalid());
                if ($this->e($cmd) !== "print") $this->printJson($this->invalid());        

                $url = $this->base_url("otj/$id/$bu/$area/$cmd");

                $headers = [
                        "Accept : text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
                        "Accept-Encoding : gzip, deflate, br",
                        "Accept-Language : en,id-ID;q=0.9,id;q=0.8,en-US;q=0.7",
                        "Cache-Control : max-age=0",
                        "Connection : keep-alive",
                        "Cookie : PHPSESSID=5dq3936jso67jha01h6gfrtg3s",
                        "Host : localhosts",
                        // "Referer : http://localhost/UTAC-GROUP/e-training/admin/exam-results",
                        // 'sec-ch-ua : "Not A;Brand";v="99", "Chromium";v="102", "Google Chrome";v="102"',
                        "sec-ch-ua-mobile : ?0",
                        "sec-ch-ua-platform : Windows",
                        "Sec-Fetch-Dest : document",
                        "Sec-Fetch-Mode : navigate",
                        "Sec-Fetch-Site : same-origin",
                        "Sec-Fetch-User : ?1",
                        "Upgrade-Insecure-Requests : 1",
                        "User-Agent : Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/102.0.0.0 Safari/537.36",
                ];

                $response = $this->curl->get($url, [], $headers);

                if ($response['code'] !== 200) $this->printJson($this->invalid());

                
                @$this->dom->loadHTML($response['data']);

                $css = [];
                foreach ($this->dom->getElementsByTagName('link') as $link) {
                        $href = $link->getAttribute("href");
                        $scss = explode(".", $href);

                        if (end($scss) == "css") $css[] = [
                                        "url" => $href,
                                        "script" => file_get_contents($href),
                                ];
                }

                $js = [];
                foreach ($this->dom->getElementsByTagName('script') as $script) {
                        $src = $script->getAttribute("src");
                        $sjs = explode(".", $src);

                        if (end($sjs) == "js") $js[] = [
                                        "url" => $src,
                                        "script" => file_get_contents($src),
                                ];
                }

                $response['data'] = explode("\n", $response['data']);

                $script = '';

                foreach ($response['data'] as $sc) {
                        $check = false;
                        foreach ($css as $icss) {
                                if (strpos($sc, $icss['url'])) $script .= '<style type="text/css">'. $icss['script'] .'</style>';
                                if (strpos($sc, $icss['url'])) $check = true;
                        }

                        foreach ($js as $ijs) {
                                if (strpos($sc, $ijs['url'])) $script .= '<script type="text/javascript">'. $ijs['script'] .'</script>';
                                if (strpos($sc, $ijs['url'])) $check = true;
                        }

                        if (!$check) {
                                $script .= $sc."\n";
                        }
                }

                $this->dompdf->loadHtml($script);

                // $this->dompdf->setPaper('A4', 'landscape');
                $this->dompdf->setPaper('A4', 'portrait');

                $this->dompdf->render();

                $this->dompdf->stream("test.pdf", array('Attachment' => 0));
        }
}