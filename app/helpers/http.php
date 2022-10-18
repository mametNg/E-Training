<?php

/**
 * 
 */
use Curl\Curl;
use Controller\Controller;

class http extends Controller
{
	
	function __construct()
	{
		$this->config();
		$this->Curl = new Curl();
	}

	public function response($method="GET", $url=false, $params=[], $headers=[], $curl_options=[])
	{

		$this->Curl->setHeaders($headers);
		// $this->Curl->setOpt(CURLOPT_ENCODING, 'gzip');
		$this->Curl->setOpt(CURLOPT_POST, TRUE);
		$this->Curl->setOpt(CURLOPT_RETURNTRANSFER, true);
		$this->Curl->setOpt(CURLOPT_FOLLOWLOCATION, true);
		$this->Curl->setOpt(CURLOPT_AUTOREFERER, false);
		$this->Curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
		$this->Curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
		$this->Curl->setOpt(CURLOPT_BINARYTRANSFER, 1);
		// $this->Curl->setOpt(CURLOPT_RANGE, '0-500');
		 
		$this->Curl->setOpt(CURLOPT_CONNECTTIMEOUT, 0);
		$this->Curl->setOpt(CURLOPT_TIMEOUT, 600);
		$this->Curl->setOpt(CURLOPT_MAXREDIRS, 20);

		if (strtolower($method) == "get") {
			$this->Curl->get($url, $params);
		} else {
			$this->Curl->post($url, $params);
		}
		
		if ($this->Curl->error || empty($this->Curl->response) && !is_string($this->Curl->response)) {
			$data = [
				'status' => false,
				'code' => 400,
			];
		} else {
			if ($this->Curl->getInfo(CURLINFO_HTTP_CODE) == 200) {
				$data = [
					'status' => true,
					'code' => 200,
					'data' => $this->Curl->response,
					'responseHeaders' => $this->Curl->responseHeaders,
					'responseCookies' => $this->Curl->responseCookies,
				];
			} else {
				$data = [
					'status' => false,
					'code' => 404,
				];
			}
		}

		return $data;
	}

	public function request($method="GET", $url=false, $params=[], $headers=[])
	{
		return $this->response($method, $url, $params, $headers);
	}

	public function get($uri, $params=[], $headers=[])
    {
        return $this->request('GET', $uri, $params, $headers);
    }
}