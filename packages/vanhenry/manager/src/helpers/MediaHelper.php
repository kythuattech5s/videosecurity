<?php 

namespace vanhenry\manager\helpers;

use Illuminate\Support\Facades\Cache as Cache;

use FCHelper;

use DB;

use App;

class MediaHelper

{

	public static function buildParameter($params){

		$url_parts['query'] = http_build_query($params);

  		return preg_replace('/&page=\d+/', '', request()->url()."?". $url_parts["query"]);

	}

	public static function getParameter(){

	  $url = parse_url($_SERVER['REQUEST_URI']);

	  if(array_key_exists("query", $url)){

	    parse_str($url['query'], $params);

	    return $params;  

	  }

	  else return array();

	}

	public static function getLinkForDir($dir){

	  $prs = static::getParameter();

	  if(array_key_exists("folder", $prs)){

	    $prs["folder"].=",".$dir;

	  }

	  else{

	    $prs["folder"]=$dir;

	  }

	  return static::buildParameter($prs);

	}

}

?>