<?php 
namespace vanhenry\slug;
class Slugger{
	public static function generateSlug($str,$sp){
	    $str = preg_replace("/(å|ä|à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
	    $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
	    $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
	    $str = preg_replace("/(ö|ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
	    $str = preg_replace("/(ü|ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
	    $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
	    $str = preg_replace("/(đ)/", 'd', $str);
	    $str = preg_replace("/(č|ç)/", 'c', $str);
	    $str = preg_replace("/(š,ş)/", 's', $str);
	    $str = preg_replace("/(ğ)/", 'g', $str);
	    $str = preg_replace("/(ž)/", 'z', $str);
	    $str = preg_replace("/(Ä|Å|À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
	    $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
	    $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
	    $str = preg_replace("/(Ö|Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
	    $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
	    $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
	    $str = preg_replace("/(Č|Ç)/", 'C', $str);
	    $str = preg_replace("/(Đ)/", 'D', $str);
	    $str = preg_replace("/(Š)/", 'S', $str);
	    $str = preg_replace("/(Ž)/", 'Z', $str);
	    $str = str_replace(" ", "-", str_replace("&*#39;", "", $str));
	    // $str = preg_replace("#\.(?!\s*html|php)#i", '-', $str);
	    $str = preg_replace('/[^A-Za-z0-9\-._]/', '-', $str); // Removes special chars.            
	    $str = preg_replace('/'.$sp.'+/', $sp, $str);            
	    $str = strtolower($str);            
	    return $str;
	}
	public static function generateSlug2($string){
		$string=htmlspecialchars_decode($string);
		$unicodes = array (
		"a" => "á|à|ạ|ả|ã|ă|ắ|ằ|ặ|ẳ|ẵ|â|ấ|ầ|ậ|ẩ|ẫ|Á|À|Ạ|Ả|Ã|Ă|Ắ|Ằ|Ặ|Ẳ|Ẵ|Â|Ấ|Ầ|Ậ|Ẩ|Ẫ",
		"o" => "ó|ò|ọ|ỏ|õ|ô|ố|ồ|ộ|ổ|ỗ|ơ|ớ|ờ|ợ|ở|ỡ|Ó|Ò|Ọ|Ỏ|Õ|Ô|Ố|Ồ|Ộ|Ổ|Ỗ|Ơ|Ớ|Ờ|Ợ|Ở|Ỡ",
		"e" => "é|è|ẹ|ẻ|ẽ|ê|ế|ề|ệ|ể|ễ|É|È|Ẹ|Ẻ|Ẽ|Ê|Ế|Ề|Ệ|Ể|Ễ",
		"u" => "ú|ù|ụ|ủ|ũ|ư|ứ|ừ|ự|ử|ữ|Ú|Ù|Ụ|Ủ|Ũ|Ư|Ứ|Ừ|Ự|Ử|Ữ",
		"i" => "í|ì|ị|ỉ|ĩ|Í|Ì|Ị|Ỉ|Ĩ",
		"y" => "ý|ỳ|ỵ|ỷ|ỹ|Ý|Ỳ|Ỵ|Ỷ|Ỹ",
		"d" => "đ|Đ",
		""  => "́|̀|̉|̃|̣",
		);
		foreach($unicodes as $ascii=>$unicode)
		{
		$string=preg_replace("/({$unicode})/miU",$ascii,$string);
		}
		$string=preg_replace('/[^a-zA-Z0-9]+/miU','-',$string);
		$string = preg_replace('/-+/', '-', $string);

		return $string;
	}
}


?>