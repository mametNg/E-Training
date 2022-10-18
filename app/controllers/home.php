<?php

/**
* 
*/
use Controller\Controller;

class home extends Controller
{
  public $menuActive = false;
  public $defaultFile = 'home/body';
  
  function __construct()
  {
    $this->config();
    $this->DB = $this->model('db_models');
  }

  public function index($uname=flase)
  { 
    if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) header("location: ".$this->base_url('/auth/login'));
    if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) exit();
    if (isset($_SESSION['quest']['bu'])) header("location: ".$this->base_url('start'));
    if (isset($_SESSION['quest']['bu'])) exit();
    if (isset($_SESSION['quest']['area'])) header("location: ".$this->base_url('start'));
    if (isset($_SESSION['quest']['area'])) exit();
    $user = $this->DB->selectTB("db_members", "id", $this->e($_SESSION['member_name']), true);

    $data = [
      "header" => [
        "title" => "Dashboard",
        "img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
        "desc" => "",
      ],
      "regulation" => $this->DB->selectTB("db_regulation", "id", 1, 1),
      "user" => ($user ? $user : false),
      "soal" => [
        "cat" => $this->DB->query("SELECT bu FROM db_quest WHERE BU!='' GROUP BY bu ASC"),
      ],
      "historys" => [],
      "history" => $this->DB->query("
        SELECT 
        a.sid,
        b.name,
        a.qid,
        c.bu,
        c.area,
        c.cat,
        a.answer,
        c.answer_key,
        a.created,
        d.min_value
        FROM db_answer a, db_members b, db_quest_results c, db_start_test d
        WHERE 
        a.flag='1'
        AND d.is_delt='0'
        AND a.uid=b.id
        AND a.qid=c.qid
        AND a.cat=c.cat
        AND a.area=c.area
        AND d.uid=c.uid
        AND d.sid=a.sid
        AND a.wave=(d.try+1)
        AND a.uid='". $this->e($_SESSION['member_name']) ."'
        ORDER BY a.created desc
        "),
    ];

    $i=0;
    $_historys = $this->array_group($data['history'], "sid", "cat");
    foreach ($_historys as $historys) {
      $f = 0;
      $t = 0;
      foreach ($historys as $history) {
        $data['historys'][$i]['created'] = $history['created'];
        $data['historys'][$i]['name'] = $history['name'];
        $data['historys'][$i]['sid'] = $history['sid'];
        $data['historys'][$i]['cat'] = $history['cat'];
        $data['historys'][$i]['area'] = $history['area'];
        $data['historys'][$i]['bu'] = $history['bu'];
        if ($history['answer_key'] == $history['answer']) $t++;
        if ($history['answer_key'] !== $history['answer']) $f++;
      }
      $data['historys'][$i]['true'] = $t;
      $data['historys'][$i]['false'] = $f;
      $data['historys'][$i]['score'] = ((100/count($historys))*$t);
      $data['historys'][$i]['desc'] = ((100/count($historys))*$t >= $historys[$i]['min_value'] ? "Congrats":"Failed");

      $i++;
    }

    $this->view("templates/home/header", $data);
    // $this->view("templates/home/sidebar", $data);
    $this->view("templates/home/topbar", $data);
    $this->view("home/body", $data);
    $this->view("templates/home/footer", $data);
  }
}

// /**
// * 
// */
// use Controller\Controller;

// class home extends Controller
// {
//   public $menuActive = false;
//   public $defaultFile = 'home/body';
  
//   function __construct()
//   {
//     $this->config();
//     $this->DB = $this->model('db_models');
//   }

//   public function index($uname=flase)
//   { 
//     if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) header("location: ".$this->base_url('/auth/login'));
//     if (!isset($_SESSION['member_name']) || empty($_SESSION['member_name'])) exit();
//     if (isset($_SESSION['quest']['bu'])) header("location: ".$this->base_url('start'));
//     if (isset($_SESSION['quest']['bu'])) exit();
//     if (isset($_SESSION['quest']['area'])) header("location: ".$this->base_url('start'));
//     if (isset($_SESSION['quest']['area'])) exit();
//     $user = $this->DB->selectTB("db_members", "id", $this->e($_SESSION['member_name']), true);

//     $data = [
//       "header" => [
//         "title" => "Dashboard",
//         "img" => $this->imgToBase64($this->base_url("assets/img/brand/wm.jpg")),
//         "desc" => "",
//       ],
//       "regulation" => $this->DB->selectTB("db_regulation", "id", 1, 1),
//       "user" => ($user ? $user : false),
//       "soal" => [
//         "cat" => $this->DB->query("SELECT bu FROM db_quest WHERE BU!='' GROUP BY bu ASC"),
//       ],
//       "historys" => [],
//       "history" => $this->DB->query("
//         SELECT 
//         a.sid,
//         b.name,
//         a.qid,
//         c.bu,
//         c.area,
//         c.cat,
//         a.answer,
//         c.answer_key,
//         a.created,
//         d.min_value
//         FROM db_answer a, db_members b, db_quest c, db_start_test d
//         WHERE 
//         a.flag='1'
//         AND d.is_delt='0'
//         AND a.uid=b.id
//         AND a.qid=c.id
//         AND a.cat=c.cat
//         AND d.sid=a.sid
//         AND a.wave=(d.try+1)
//         AND a.uid='". $this->e($_SESSION['member_name']) ."'
//         ORDER BY a.created desc
//         "),
//     ];

//     $i=0;
//     $_historys = $this->array_group($data['history'], "sid", "cat");
//     foreach ($_historys as $historys) {
//       $f = 0;
//       $t = 0;
//       foreach ($historys as $history) {
//         $data['historys'][$i]['created'] = $history['created'];
//         $data['historys'][$i]['name'] = $history['name'];
//         $data['historys'][$i]['sid'] = $history['sid'];
//         $data['historys'][$i]['cat'] = $history['cat'];
//         $data['historys'][$i]['area'] = $history['area'];
//         $data['historys'][$i]['bu'] = $history['bu'];
//         if ($history['answer_key'] == $history['answer']) $t++;
//         if ($history['answer_key'] !== $history['answer']) $f++;
//       }
//       $data['historys'][$i]['true'] = $t;
//       $data['historys'][$i]['false'] = $f;
//       $data['historys'][$i]['score'] = ((100/count($historys))*$t);
//       $data['historys'][$i]['desc'] = ((100/count($historys))*$t >= $historys[$i]['min_value'] ? "Congrats":"Failed");

//       $i++;
//     }

//     $this->view("templates/home/header", $data);
//     // $this->view("templates/home/sidebar", $data);
//     $this->view("templates/home/topbar", $data);
//     $this->view("home/body", $data);
//     $this->view("templates/home/footer", $data);
//   }

//   // public function cost()
//   // {
//   //  //  // echo $this->balitbangDecode("53g83");

//   //   $quests = $this->DB->getAllTB("tbl_soal");
//   //   $i=1;
//   //   foreach ($quests as $quest) {
//   //    // $quest['soal'] = preg_replace('/[[:^print:]]/', '', $quest['soal']);
//   //    // $quest['soal'] = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $quest['soal']); // remove text non ascii
//   //    // $quest['soal'] = str_replace("  ", "", $quest['soal']);
//   //    // $quest['soal'] = $this->non_ascii($quest['soal']);

//   //    $params = [
//   //      "quest" => $this->e(bin2hex($quest['soal'])),
//   //      "quest_a" => $this->e(bin2hex($quest['a'])),
//   //      "quest_b" => $this->e(bin2hex($quest['b'])),
//   //      "quest_c" => $this->e(bin2hex($quest['c'])),
//   //      "quest_d" => $this->e(bin2hex($quest['d'])),
//   //      "answer_key" => strtoupper($this->e($quest['knc_jawaban'])),
//   //      "image" => $quest['gambar'],
//   //      "created" => (date("Y", strtotime($quest['tanggal'])) > 2000 ? strtotime($quest['tanggal']):time()),
//   //      "status" => (strtolower($quest['aktif']) == "y" ? 1:0),
//   //      "bu" => $quest['bu'],
//   //      "area" => $quest['areas'],
//   //      "cat" => $quest['cat'],
//   //    ];

//   //    if (!empty($params['bu'])&& !empty($params['area']) && !empty($params['cat'])) {

//   //      $insert = $this->DB->insertTB("db_quest", $params);

//   //      echo $i.". ".$params['quest']."<br>";
//   //      echo $i.". D | ".bin2hex($params['quest'])."<br>";
//   //      echo $i.". O | ".$quest['soal']."<br>";
//   //      echo "<hr>";
//   //      $i++;
//   //    }
//   //  }

//   //  //  // select
//   //  //  // a.id_user id,
//   //  //  // b.username username,
//   //  //  // b.nama name,
//   //  //  // count(a.id_user) total,
//   //  //  // (select count(c.score) from tbl_nilai c where c.score>=90 and c.id_user=a.id_user) success,
//   //  //  // (select count(c.score) from tbl_nilai c where c.score<90 and c.id_user=a.id_user) failed
//   //  //  // from tbl_nilai as a
//   //  //  // inner join tbl_user as b
//   //  //  // on a.id_user = b.id_user
//   //  //  // group by a.id_user;
//   // }

//   // public function t()
//   // {

//   //   $bu = $this->DB->query("
//   //     SELECT
//   //     b.name bu,
//   //     c.name area
//   //     FROM 
//   //     db_bu b,
//   //     db_area c
//   //     WHERE
//   //     b.id = c.bu
//   //     ORDER BY b.name, c.name ASC
//   //   ");

//   //   $this->printJson($bu);

//   //   // foreach ($bu as $_bu) {
//   //   //   $dataTable = [
//   //   //     "id" => strtoupper($this->randString(5)),
//   //   //     "name" => $this->e($this->non_ascii($_bu['cat'])),
//   //   //     "bu" => $this->e($this->non_ascii($_bu['bu'])),
//   //   //     "area" => $this->e($this->non_ascii($_bu['area'])),
//   //   //   ];

//   //   //   // var_dump($dataTable);
//   //   //   // $this->DB->insertTB("db_cat", $dataTable);
//   //   // }
//   // }

//   // public function migrate()
//   // {
    
//   //   $quests = $this->DB->query("SELECT * FROM training_test_old.tbl_soal");
//   //   $i=0;
//   //   foreach ($quests as $quest) {
//   //     $params = [
//   //       "quest" => $this->e(bin2hex(strip_tags(str_replace("â€¦", "?", $quest['soal'])))),
//   //       "quest_a" => $this->e(bin2hex(strip_tags(str_replace("â€¦", "?", $quest['a'])))),
//   //       "quest_b" => $this->e(bin2hex(strip_tags(str_replace("â€¦", "?", $quest['b'])))),
//   //       "quest_c" => $this->e(bin2hex(strip_tags(str_replace("â€¦", "?", $quest['c'])))),
//   //       "quest_d" => $this->e(bin2hex(strip_tags(str_replace("â€¦", "?", $quest['d'])))),
//   //       "answer_key" => strtoupper($this->e($quest['knc_jawaban'])),
//   //       "image" => $quest['gambar'],
//   //       "created" => (date("Y", strtotime($quest['tanggal'])) > 2000 ? strtotime($quest['tanggal']):time()),
//   //       "status" => (strtolower($quest['aktif']) == "y" ? 1:0),
//   //       "bu" => $quest['bu'],
//   //       "area" => $quest['areas'],
//   //       "cat" => $quest['cat'],
//   //     ];

//   //     if (!empty($params['bu'])&& !empty($params['area']) && !empty($params['cat'])) {

//   //       $insert = $this->DB->insertTB("db_quest", $params);
        
//   //       if (strlen($params['image']) >=1) {
//   //         $getimg_ = "http://10.83.41.111/e-training.test/foto/".$params['image'];
//   //         $path = "assets/img/exam/".$params['image'];
//   //         if (!file_exists($path)) {
//   //           $downloaded = file_get_contents($getimg_);
//   //           file_put_contents($path, $downloaded);
//   //         }
//   //       }

//   //       echo $i.". ".$params['quest']."<br>";
//   //       echo $i.". O | ".hex2bin(strip_tags($this->e($params['quest'])))."<br>";
//   //       echo "<hr>";
//   //       $i++;
//   //     }
//   //   }
//   // }
// }