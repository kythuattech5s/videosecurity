<?php 
namespace vanhenry\manager\helpers;
use Session;
class CT
{
	public static $KEY_LIST_TABLE="_vh_admin_listTable";
	public static $KEY_LIST_DETAIL_TABLE="_vh_admin_listDetailTable";
	public static $KEY_LIST_LANGUAGE="_vh_admin_listLanguage";
	public static $TIME_CACHE_LANGUAGE=10;
	public static $KEY_CACHE_QUERY="_vh_admin_field_select_";
	public static $TIME_CACHE_QUERY= 10;
	public static $KEY_GET_ALL_TABLE="_vh_admin_allDataTable";
	public static $TIME_CACHE_GET_ALL_TABLE=1;
	public static $KEY_GET_ALL_SFIELD_TABLE="_vh_admin_allDataSomeFieldTable";
	public static $TIME_CACHE_GET_ALL_SFIELD_TABLE=1;
	public static $KEY_SESSION_USER_LOGIN = "_admin_logined";
	public static $KEY_SESSION_LANGUAGE = "_admin_language";
	public static $KEY_FRONTEND_SESSION_LANGUAGE = "frontend_language";
	public static function getLanguage($isAdmin=true){
		$lang = Session::get($isAdmin?CT::$KEY_SESSION_LANGUAGE:CT::$KEY_FRONTEND_SESSION_LANGUAGE, 'vi');
		return $lang =="vi"?"vi":$lang;
	}
	public static function setLanguage($lang,$isAdmin= true){
		Session::put($isAdmin?CT::$KEY_SESSION_LANGUAGE:CT::$KEY_FRONTEND_SESSION_LANGUAGE, $lang);
	}
}
?>