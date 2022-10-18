<?php

/**
* 
*/
namespace ipJson;
use Controller\Controller;
use Curl\Curl;

class ipJson
{
	
	private static $ip;
	private static $Curl;
	private static $Controller;

	public $get;

	function __construct()
	{
		static::$Curl = new Curl();
		static::$Controller = new Controller();
		static::$Controller->config();
	}

	public function get($ip=false)
	{
		$response = $this->onError();
		static::$ip = (!$ip? static::$Controller->ip:$ip);
		
		static::$Curl->get("http://ip-api.com/json/". static::$ip ."?fields=status,message,continent,continentCode,country,countryCode,region,regionName,city,district,zip,lat,lon,timezone,offset,currency,isp,org,as,asname,reverse,mobile,proxy,hosting,query");

		$response = static::$Curl->response;
		if (isset($response->status) && $response->status !== "success" || !$response) {
			$response = $this->onError();
		}

		return json_decode(json_encode($response), TRUE);
	}

	public function onError()
	{
		$response = [
			"countryCode" => "",
			"country" => "",
			"continentCode" => "",
			"continent" => "",
			"lat" => "",
			"lon" => "",
			"city" => "",
			"regionName" => "",
			"region" => "",
			"district" => "",
			"timezone" => "",
			"zip" => "",
			"offset" => "",
			"currency" => "",
			"isp" => "",
			"org" => "",
			"as" => "",
			"asname" => "",
			"reverse" => "",
			"hosting" => "",
			"mobile" => "",
			"proxy" => "",
			"status" => "",
			"query" => "",
		];

		return $response;
	}

}