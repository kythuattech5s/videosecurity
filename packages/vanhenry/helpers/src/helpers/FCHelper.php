<?php 
namespace vanhenry\helpers\helpers;
use DB;
use Illuminate\Support\Facades\Cache as Cache;
use App;
use vanhenry\manager\helpers\CT;
class FCHelper{
	public static function ep($item,$key,$checklang=1,$isAdmin = true){
		$isAdmin = self::isAdminUI();
		if(isset($item) && isset($key)){
			if(is_array($item) ){
				$locale = \App::getLocale();
				$localekey = $locale."_".$key;
				if(array_key_exists($localekey, $item)){
					return static::getRawData($localekey,$item[$localekey]);
				}
				else if(array_key_exists($key, $item)){
					return static::getRawData($key,$item[$key]);
				}
				
			}
			else if(is_object($item) ){
				if(is_subclass_of($item,"Illuminate\Database\Eloquent\Model")){
					return static::ep($item->getAttributes(),$key,$checklang,false);
				}
				else if($item instanceof \Illuminate\Support\Collection){
					return static::ep($item->toArray(),$key,$checklang,false);
				}
				else if(is_object($item)){
					$item = (array)$item;
					return static::ep($item,$key,$checklang,false);
				}
				else{
					//return $item->$key;
				}
			}
			return $key;
		}
		return "";
	}
	public static function getTailLink(){
		$seg = request()->segment(1,"");
        $isAdmin = $seg == "esystem";
        if(!$isAdmin){
        	$locale = \App::getLocale();
        	if ($locale == 'vi') {
        		return '';
        	}
        	return "?lang=".$locale;
        }
        return "";
	}
	public static function showStaticLink($url){
		if($url=="#"){
			$url = "";
		}
		if($url==""){
			return $url;
		}
		return $url.self::getTailLink();
	}
	public static function isAdminUI(){
		$seg = request()->segment(1,"");
        $isAdmin = $seg == "esystem";
        return $isAdmin;
	}
	private static function getRawData($key,$value){
		return $value;
	}
	public static function isJson($string) {
	 json_decode($string);
	 return (json_last_error() == JSON_ERROR_NONE);
	}
	public static function ec($item,$key,$checklang){
		echo FCHelper::ep($item,$key,$checklang);
	}
	public static function objectToArray($data){
		$data=array_map(function($item){
		    return (array) $item;
		},$data);
		return $data;
	}
	public static function normalValue($str){
		return trim(strtolower($str));
	}
	public static function er($item,$key,$normal=0){
		if(!isset($item) || (is_array($item) && count($item)==0) ) return "";
		if($item instanceof \Illuminate\Database\Eloquent\Model){
			$item = $item->toArray();
		}
		if(is_object($item)){
			$item = (array) $item;
			return static::er($item,$key);
		}
		else if(is_array($item)){
			$locale = \App::getLocale();
			$fkey = $locale."_".$key;
			$finalkey = $key;
			if(array_key_exists($fkey, $item)){
				$finalkey = $fkey;
			}
			if(array_key_exists($key, $item)){
				switch ($key) {
					case 'content':
					case 'short_content':
						return html_entity_decode($item[$finalkey]);
					case 'price':
					case 'subtotal':
						return number_format($item[$finalkey],0,",",".");
					case 'slug':
						return FCHelper::showStaticLink($item[$finalkey]);
					case 'created_at':
					case 'updated_at':
					 	return \Carbon\Carbon::parse($item[$finalkey])->format('d/m/Y h:i A');
				 	case 'birthday':
				 		return \Carbon\Carbon::parse($item[$finalkey])->format('d/m/Y');
					default:
						return $normal!=0?static::normalValue($item[$finalkey]):$item[$finalkey];
				}
				
			}
			return $key;
		}
		return $key;
	}
	public static function eimg($data,$def="admin/images/noimage.png",$folder=""){
		if($def==""){
			$def="admin/images/noimage.png";
		}

		if (strpos($data, 'http://') === 0 || strpos($data, 'https://') === 0) {
			return $data;
		}

		if(!isset($data)){
			return $def;
		}
		if(is_array($data)){
			$json = $data;
		}
		else if(is_string($data)){
			$json = json_decode($data,true);
		}
		if(is_array($json) && array_key_exists("path", $json)){
			if(strpos($json["path"],'public') == 0){
				$json["path"] = $json["path"];
			}
			else{
				$json["path"] = 'public/'.$json["path"];
			}
			$img  = $json["path"].$json["file_name"];
			$def2 = $img;
			if($folder!="" && $folder!="-1"){
				$img = $json["path"]."thumbs/".$folder."/".$json["file_name"];
				
			}
			if(isset($json["has_file"]) && $json['has_file']==0){
				return $json["file_name"];
			}
			if(file_exists($img)){
				return $img;
			}
			if(file_exists($def2)){
				return $def2;
			}
		}
		return $def;
	}
	


	public static function eimg1($item,$key,$def="admin/images/noimage.png",$folder=""){
		if(!isset($item->$key)){
			return $def;
		}
		return static::eimg($item->$key,$def,$folder);
	}
	public static function eimg2($item,$key,$folder=""){
		$def ="admin/images/noimage.png";
		if(!isset($item->$key)){
			return $def;
		}
		return static::eimg($item->$key,$def,$folder);
	}
	public static function aimg($item,$key,$field,$def="admin/images/noimage.png"){
		if(!isset($item->$key)){
			return $def;
		}
		$data= $item->$key;
		return static::aimg2($data,$field,$def);
	}
	public static function aimg2($data,$field,$def="admin/images/noimage.png"){
		if(!is_array($data)){
			$json = json_decode($data,true);
			$json = @$json ?$json:array();	
		}
		else{
			$json = $data;
		}
		
		if(is_array($json) && array_key_exists($field, $json)){
			$val =  $json[$field];
			if(strlen($val)==0 && isset($item->name)){
				$name= $item->name;
				return $name;
			}
			else
				return $val;
		}
		return $def;
	}
	public static function gallery($item,$key){
		if(!isset($item->$key)){
			return [];
		}
		$data= $item->$key;
		$json = json_decode($data,true);
		return $json;
	}
	public static function getTranslationTable($tableMap)
	{
		return \vanhenry\manager\model\VTable::where('act', 1)->where('translation_of', $tableMap)->first();
	}
	public static function recursivePivotPrint($results,$values,$parent = 0,$level=0){
		if(array_key_exists($parent, $results) && count($results[$parent])>0){
			foreach ($results[$parent] as $k => $item) {
				if($item['parent']==$parent){
					$str = '<li class="clazzli clazzli-%s %s" data-level="%s">
						<label>
							%s
							<input %s type="checkbox" value="%s"/>
							%s
						</label>
						<span class="expand">-</span>
					';
					$checked = (is_array($values) && in_array($item['id'], $values))?' checked ':'';
					$str = sprintf($str,$item['id'],'level-'.$level.' '.($checked==''?'':'choose'),$level,str_repeat("&nbsp;&nbsp;&nbsp;&nbsp;", $level).($level>0?"└----":''),$checked,$item['id'], $item['name']);
					echo $str;
					$nextlevel = $level +1;
					self::recursivePivotPrint($results,$values,$item['id'],$nextlevel);
					echo '</li>';
				}
			}
		}
	}
	public static function pivotPrint($results,$values){
		foreach ($results as $k => $item) {
			$keys = array_keys($item);

			$str = '<li class="clazzli clazzli-%s">
				<label>
					<input %s type="checkbox" value="%s"/>
					%s
				</label>';
			$checked = (is_array($values) && in_array($item['id'], $values))?' checked ':'';
			$str = sprintf($str,$item['id'],$checked,$item['id'], isset($keys[1]) ? $item[$keys[1]] : $item['name']);
			echo $str.'</li>';
		}
	}
	public static function viewRecursivePivotPrint($results, $parent = 0, $level = 0)
	{
		if(array_key_exists($parent, $results) && count($results[$parent])>0){
			foreach ($results[$parent] as $k => $item) {
				if($item['parent']==$parent){
					echo '<p class="select" data-id="'.$item['id'].'">'.($level>0?"└--- ":'').$item['name'].'</p>';
					$nextlevel = $level +1;
					self::viewRecursivePivotPrint($results,$item['id'],$nextlevel);
				}
			}
		}
	}
	public static function viewPivotPrint($results)
	{
		foreach ($results as $k => $item) {
			echo '<p class="select" data-id="'.$item['id'].'">'.$item['name'].'</p>';
		}
	}
	public static function getDataPivot($pivotTable, $originField, $targetTable, $targetField, $columns, $originId = null)
	{
		$transTableOfTarget = self::getTranslationTable($targetTable);
		foreach ($columns as $key => &$column) {
			$column = 'a.'.$column;
		}
		if ($transTableOfTarget != null) {
			$sql = "SELECT ".implode(',', $columns)." from $targetTable a inner join $transTableOfTarget->name b on a.id = b.map_id";
			if ($originId != null) {
				$sql .= " inner join $pivotTable c on a.id = c.$targetField where c.$originField = $originId";
			}
			else{
				$sql .= " where 1 = 1";
			}
			$sql .= " and a.act = 1 and b.language_code = 'vi'";
		}
		else{
			$sql = "SELECT ".implode(',', $columns)." from $targetTable a";
			if ($originId != null) {
				$sql .= " inner join $pivotTable c on a.id = c.$targetField where 1 = 1";
				$sql .= " and c.$originField = $originId";
			}
			else{
				$sql .= ' where 1 = 1';
			}
			$sql .= ' and a.act = 1';
		}
		$sql .= ' group by a.id';
		if (in_array('a.parent', $columns)) {
			$sql .= ' order by a.parent';
			$data = \DB::select($sql);
			$arr = [];
			foreach ($data as $key => $value) {
				$arr[] = (array)$value;
			}
			// echo '<pre>'; var_dump($arr); die(); echo '</pre>';
			$arr = self::groupBy($arr, 'parent');
			// echo '<pre>'; var_dump($arr); die(); echo '</pre>';
		}
		else{
			$sql .= ' order by a.id desc';
			$data = \DB::select($sql);
			$arr = [];
			foreach ($data as $key => $value) {
				$arr[] = (array)$value;
			}
		}
		return $arr;
	}

	public static function getDataPivotAttribute()
	{	
		$arr = \App\Models\AttributeValue::select('v.id','v.name','a.name as nameParent')
		->from('attribute_values as v')
		->join('attributes as a','a.id','=','v.attribute_id')
		->join('category_attributes as ca','ca.attribute_id','=','a.id')
		->join('product_categories as pc','pc.id','=','ca.category_id')->groupBy('v.id')->get();
		return $arr;
	}

	public static function groupBy($array, $key) {
	    $return = array();
	    foreach($array as $val) {
	    	if(array_key_exists($key, $val)){
	        	$return[empty($val[$key]) ? 0 : $val[$key]][] = $val;
	    	}
	    }
	    return $return;
	}
	public static function getRealValuePuts($dataItem, $pivotTable, $originField, $targetField)
	{
		$sql = vsprintf('SELECT a.%s from %s a where a.%s = %d', [$targetField, $pivotTable, $originField, $dataItem->id]);
		$valueChooses = \DB::select($sql);
		$arr = [];
		foreach ($valueChooses as $key => $value) {
			$arr[] = $value->$targetField;
		}
		return $arr;
	}
	public static function generateSlugWithLanguage($slugWithLang, $localeCode, $mapId = null){
		$num = 2;
		do {
			$e = \DB::table('v_routes')->select($localeCode.'_link')->where(function($q) use($slugWithLang, $localeCode, $mapId){
				$q->where([$localeCode.'_link' => $slugWithLang]);
				if ($mapId != null) {
					$q->where('map_id', '!=', $mapId);
				}
			})->first();
			if ($e != null) {
				$slugWithLang .= '-'.$num;
			}
			else {
				return $slugWithLang;
			}
		} while (true);
	}
	public static function generateSlug($tableMap, $slug, $id = null){
		$num = 2;
		do {
			$e = \DB::table($tableMap)->select('id')->where(function($q) use($id, $slug){
				$q->where(['vi_link' => $slug]);
				if ($id != null) {
					$q->where('id', '!=', $id);
				}
			})->first();
			if ($e != null) {
				$slug .= '-'.$num;
			}
			else {
				return $slug;
			}
		} while (true);
	}
	public static function getItemTransShow($transTable, $itemMain, $localeCode, $field)
	{
		return \DB::table($transTable->table_map)->select($field.' as '.$field.'_'.$localeCode)->where(['map_id' => $itemMain->id, 'language_code' => $localeCode])->first();
	}
	public static function langChooseOfTable($table)
	{
		$langChoose = \Session::get('_table_lang');
		if (!is_array($langChoose) || !array_key_exists($table, $langChoose)) {
			$langChoose = \Config::get('app.locale_origin');	
		}
		else{
			$langChoose = $langChoose[$table];
		}
		return $langChoose;
	}
	public static function filterData($transTable, $data)
	{
		/*lấy all field của bảng dịch*/
		$fields = \DB::select('SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`= database() AND `TABLE_NAME`="'.$transTable->table_map.'"');
		$arrField = [];
		foreach ($fields as $key => $field) {
			$arrField[] = $field->COLUMN_NAME;
		}
		/*Tách data của bảng gốc và bảng dịch ra*/
		$originData = [];
		$transData = [];
		foreach ($data as $key => $value) {
			if (in_array($key, $arrField)) {
				$transData[$key] = $value;
			}
			else{
				$originData[$key] = $value;
			}
		}
		return [$originData, $transData];
	}
	// lấy ra số lượng đơn hàng thành công của sản phẩm
	public static function qtyOrder($productId, $objStartDate, $objEndDate)
	{
		return \App\Models\Order::from('orders as o')
			->where('o.status', \OrderHelper::STATUS_DELIVERED)
			->join('order_details as od', 'od.order_id', '=', 'o.id')
			->where('od.product_id', $productId)
			->count();
	}
	public static function getDistrict($provinceId){
		$html = '<option disabled selected>Quận huyện</option>';
		$provinces = \DB::select("SELECT * FROM `provinces` WHERE `id` = $provinceId ORDER BY `id` DESC");
		if(count($provinces) == 0){
			return $html;
		}
		$provinceRealId = $provinces[0]->province_id;
		$districts = \DB::select("SELECT * FROM `districts` WHERE  `province_id` = $provinceRealId ORDER BY `id` DESC");
		foreach ($districts as $district) {
			$html .= '<option value="'.$district->id.'">'.$district->name.'</option>';
		}
		return $html;
	}
}