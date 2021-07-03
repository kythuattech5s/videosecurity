<?php 
namespace vanhenry\helpers\helpers;
use App\Menu;
use vanhenry\helpers\helpers\FCHelper;
class MenuHelper{
	public static function getListMenuItem($parent = 0,$group = 0){
		if(class_exists('App\Menu')){
			return Menu::where('parent',$parent)->where("group_id",$group)->where("act",1)->orderBy('ord','ASC')->remember(60)->get()->toArray();
		}
		return array();
	}
	private static function getListMenuItemPrivate($listMenus,$levelStart,$classHtml,$group,$recur=0){
		$default = array(
			'classul'=>'',
			'classli'=>'',
			'classa'=>''
			);
		$classHtml = array_merge($default,$classHtml);
		$strRet = "";
		if(count($listMenus)>0){
		$strRet.= "<ul class='ullv".$levelStart." ".$classHtml["classul"]."'>";
			foreach ($listMenus as $itemMenu) {
				$strRet.= "<li class='lilv".$levelStart." ".$classHtml["classli"]."'>";
				$strRet.=FCHelper::ep($itemMenu,'prefix_name',1);
				$strRet.= "<a class='".$classHtml['classa']." ".FCHelper::ep($itemMenu,'clazz',1)."' href='".FCHelper::showStaticLink(FCHelper::ep($itemMenu,'link',1))."' title='".FCHelper::ep($itemMenu,'name',1)."'>".FCHelper::ep($itemMenu,'middle_name',1).FCHelper::ep($itemMenu,'name',1,false)."</a>";
				$strRet.=FCHelper::ep($itemMenu,'suffix_name',1);
				if($recur==1){
					$strRet.=static::getListMenuItemPrivate(static::getListMenuItem(FCHelper::ep($itemMenu,'id',0),$group),$levelStart+1,$classHtml,$group,$recur);	
				}
				$strRet.= "</li>";
			}
			$strRet.= "</ul>";
		}
		return $strRet;
	}
	public static function getMenu($levelStart,$classHtml=array(),$parent=0,$group=0,$recur=0){
		return static::getListMenuItemPrivate(static::getListMenuItem($parent,$group),$levelStart,$classHtml,$group,$recur);
	}
	public static function showMenu($levelStart,$classHtml,$parent=0,$group =0,$recur=0){
		echo static::getMenu($levelStart,$classHtml,$parent,$group,$recur);
	}
	public static function getListMenuSearch($parent = 0,$group = 0){
		if(class_exists('App\Menu')){
			return Menu::where('parent',$parent)->where("group_id",$group)->where("act",1)->orderBy('ord','ASC')->remember(60)->get()->toArray();
		}
		return array();
	}
	public static function getMenuFooter($parent = 0,$group = 2){
		if(class_exists('App\Menu')){
			return Menu::where('parent',$parent)->where("group_id",$group)->where("act",1)->orderBy('ord','ASC')->take(2)->remember(60)->get();
		}
		return collect();
	}
}