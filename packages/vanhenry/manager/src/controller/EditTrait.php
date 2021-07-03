<?php 
namespace vanhenry\manager\controller;
use vanhenry\helpers\helpers\StringHelper as StringHelper;
use vanhenry\helpers\JsonHelper as JsonHelper;
use Illuminate\Support\Facades\Cache as Cache;
use DB;
use Carbon\Carbon;
use vanhenry\helpers\CT as CT;
use Illuminate\Support\Collection as Collection;
use \Illuminate\Http\Request as Request;
use Illuminate\Support\Facades\Redirect;
use vanhenry\manager\model\VTable;
use vanhenry\manager\model\Config;
use vanhenry\manager\model\VDetailTable;
use vanhenry\manager\helpers\GlobalHelper;
use vanhenry\manager\helpers\DetailTableHelper;
use vanhenry\manager\model\TableProperty;
use Mail;
use Illuminate\Support\Facades\Schema;
use FCHelper;
use App\Models\Deal;
use App\Models\ProductCategory;
use App\Models\Product;
use App\Models\{Province,District,Ward};

trait EditTrait{
	private function _edittrait_getTableEditProperties($table_id,$tableData){
		$tmp =  TableProperty::where("act",1)->where("parent",$table_id)->orderBy("ord")->get()->toArray();
		
		$lang = FCHelper::ep($tableData,"lang");
		$lang = explode(",", $lang);
		$ret = array();
		foreach ($tmp as $key => $value) {
			$value["is_prop"] = 1;
			$t = (object)$value;
			array_push($ret, $t);
			foreach ($lang as $k => $v) {
				$value["name"] = $v."_".$value["name"];
				$value["note"] = $value["note"]." (".strtoupper($v).")";
				array_push($ret, (object)$value);
			}
		}
		return $ret;
	}
	private function _edittrait_getDataFromTableProperties($table,$detaildata,$dataitem){
		$table_meta = $table."_metas";
		if(Schema::hasTable($table_meta)){
			$detaildata = collect($detaildata);
			$inProp = $detaildata->implode('id', ',');
			$inProp = explode(",", $inProp);
			$arrProp = DB::table($table_meta)->whereIn("prop_id",$inProp)->where("source_id",$dataitem->id)->get();
			$listData = array();
			foreach ($arrProp as $key => $value) {
				$desired_object = $detaildata->filter(function($item) use($value) {
					return $item->id == $value->prop_id;
				})->first();
				$_k = $value->meta_key.$desired_object->name;
				$_v =  $value->meta_value;
				$dataitem->$_k = $_v;
			}
		}
		return $dataitem;
	}
	public function edit($table,$id){
		$tableData = self::__getListTable()[$table];
		$type = $tableData->type_show;
		$fnc = 'edit'.$type;
		if(!method_exists($this,$fnc)){
			$fnc= 'edit_normal';
		}
		if($table == 'combos'){
			$fnc= 'edit_combo';
		}
		if($table == 'promotions'){
			$fnc= 'edit_promotion';
		}
		if($table == 'deals'){
			$fnc= 'edit_deal';
		}
		return $this->$fnc($table,$tableData,$id);	
	}
	public function edit_order_pcb($table,$tableData,$id){
		$tableDetailData = self::__getListDetailTable($table);
		$dataItem =DB::table($table)->where('id',$id)->get();
		if(count($dataItem)>0){
			$data['tableData'] = new Collection($tableData);
			$addDetailData = $this->_edittrait_getTableEditProperties($data['tableData']->get("id"),$tableData);
			$data['dataItem'] = $this->_edittrait_getDataFromTableProperties($table,$addDetailData,$dataItem[0]);
			$tableDetailData = collect($tableDetailData);
			$tableDetailData = $tableDetailData->merge($addDetailData);
			$tableDetailData = $this->__groupByRegion($tableDetailData);
			$tmpTableDetailData= array();
			foreach ($tableDetailData as $key => $value) {
				$tmpTableDetailData[$key] = $this->__groupByGroup($value)->toArray();
			}
			$data['groupControl'] = $this->__getInfoGroup();
			$data['tableDetailData'] = $tmpTableDetailData;
			$data['actionType']="edit";
			return view('vh::edit.view_order_pcb',$data);
		}
		else{
			return redirect($this->admincp.'/view/'.$table);
		}
	}
	public function edit_promotion($table,$tableData,$id){
		$tableDetailData = self::__getListDetailTable($table);
		$transTable = \FCHelper::getTranslationTable($tableData->table_map);
		/*nếu table không có bảng dịch*/
		if ($transTable == null) {
			$dataItem = DB::table($table)->where('id',$id)->get();
		}
		else{
			$langChoose = FCHelper::langChooseOfTable($tableData->table_map);
			$dataItem = DB::table($table)->join($transTable->table_map, 'id', '=', 'map_id')->where(['id' => $id, 'language_code' => $langChoose])->get();	
		}

		if(count($dataItem)>0){
			$data['tableData'] = new Collection($tableData);
			$addDetailData = $this->_edittrait_getTableEditProperties($data['tableData']->get("id"),$tableData);
			$data['dataItem'] = $this->_edittrait_getDataFromTableProperties($table,$addDetailData,$dataItem[0]);
			$tableDetailData = collect($tableDetailData);
			$tableDetailData = $tableDetailData->merge($addDetailData);
			$tableDetailData = $this->__groupByRegion($tableDetailData);
			$tmpTableDetailData= array();
			foreach ($tableDetailData as $key => $value) {
				$tmpTableDetailData[$key] = $this->__groupByGroup($value)->toArray();
			}
			$data['groupControl'] = $this->__getInfoGroup();
			$data['tableDetailData'] = $tmpTableDetailData;
			$data['actionType']="edit";
			return view('vh::view.promotion.editView',$data);
		}
		else{
			return redirect($this->admincp.'/view/'.$table);
		}
	}

	public function edit_combo($table,$tableData,$id){
		$tableDetailData = self::__getListDetailTable($table);
		$transTable = \FCHelper::getTranslationTable($tableData->table_map);
		/*nếu table không có bảng dịch*/
		if ($transTable == null) {
			$dataItem = DB::table($table)->where('id',$id)->get();
		}
		else{
			$langChoose = FCHelper::langChooseOfTable($tableData->table_map);
			$dataItem = DB::table($table)->join($transTable->table_map, 'id', '=', 'map_id')->where(['id' => $id, 'language_code' => $langChoose])->get();	
		}

		if(count($dataItem)>0){
			$data['tableData'] = new Collection($tableData);
			$addDetailData = $this->_edittrait_getTableEditProperties($data['tableData']->get("id"),$tableData);
			$data['dataItem'] = $this->_edittrait_getDataFromTableProperties($table,$addDetailData,$dataItem[0]);
			$tableDetailData = collect($tableDetailData);
			$tableDetailData = $tableDetailData->merge($addDetailData);
			$tableDetailData = $this->__groupByRegion($tableDetailData);
			$tmpTableDetailData= array();
			foreach ($tableDetailData as $key => $value) {
				$tmpTableDetailData[$key] = $this->__groupByGroup($value)->toArray();
			}
			$data['groupControl'] = $this->__getInfoGroup();
			$data['tableDetailData'] = $tmpTableDetailData;
			$data['actionType']="edit";
			return view('vh::view.combo.editView',$data);
		}
		else{
			return redirect($this->admincp.'/view/'.$table);
		}
	}
	public function edit_deal($table,$tableData,$id){
		$deal = Deal::where('id', $id)->first();
		if (new \DateTime < new \DateTime($deal->start_at)) {
			$status = 'coming';
		}
		elseif(new \DateTime >= new \DateTime($deal->start_at) && new \DateTime <= new \DateTime($deal->expired_at)){
			$status = 'ongoing';
		}
		elseif(new \DateTime > new \DateTime($deal->expired_at)){
			$status = 'end';
		}

		$dealProductMains = $deal->dealProductMain()->withPivot('act')->where('products.act', 1)->get();
    	$dealProductMainsActive = $dealProductMains->filter(function($v, $k){
    		return $v->pivot->act == 1;
    	});
    	$dealProductSubs = $deal->getDealProducts()->get();

		$step = null;
    	if ($status == 'coming' && $dealProductMains->count() == 0) {
    		$step = 2;
    	}
    	elseif($status == 'coming' && $dealProductSubs->count() == 0){
    		$step = 3;
    	}

    	$categories = ProductCategory::where('act', 1)->get();
    	$productChooses = Product::whereDoesntHave('flashSale', function($q) use($deal){
    		$q->where('start_at', '<=', $deal->expired_at)->where('expired_at', '>=', $deal->start_at);
    	})
    	->whereDoesntHave('deal', function($q) use($deal){ // sp deal chính
    		$q->where('start_at', '<=', $deal->expired_at)->where('expired_at', '>=', $deal->start_at);
    	})
    	->whereDoesntHave('getDealSub', function($q) use($deal){ // sp deal đi kèm
    		$q->where('start_at', '<=', $deal->expired_at)->where('expired_at', '>=', $deal->start_at);
    	})
    	->whereDoesntHave('combo', function($q) use($deal){ // sp deal chính
    		$q->where('start_at', '<=', $deal->expired_at)->where('expired_at', '>=', $deal->start_at);
    	})
    	->orderBy('id', 'desc')
    	->take(10)
    	->get();
    	return view('vh::view.deal.deal_product', compact('deal', 'dealProductMains', 'dealProductSubs', 'categories', 'productChooses', 'status', 'step', 'dealProductMainsActive'));
	}

	public function edit_normal($table,$tableData,$id){
		$tableDetailData = self::__getListDetailTable($table);
		$transTable = \FCHelper::getTranslationTable($tableData->table_map);
		/*nếu table không có bảng dịch*/
		if ($transTable == null) {
			$dataItem = DB::table($table)->where('id',$id)->get();
		}
		else{
			$langChoose = FCHelper::langChooseOfTable($tableData->table_map);
			$dataItem = DB::table($table)->join($transTable->table_map, 'id', '=', 'map_id')->where(['id' => $id, 'language_code' => $langChoose])->get();	
		}

		if(count($dataItem)>0){
			$data['tableData'] = new Collection($tableData);
			$addDetailData = $this->_edittrait_getTableEditProperties($data['tableData']->get("id"),$tableData);
			$data['dataItem'] = $this->_edittrait_getDataFromTableProperties($table,$addDetailData,$dataItem[0]);
			$tableDetailData = collect($tableDetailData);
			$tableDetailData = $tableDetailData->merge($addDetailData);
			$tableDetailData = $this->__groupByRegion($tableDetailData);
			$tmpTableDetailData= array();
			foreach ($tableDetailData as $key => $value) {
				$tmpTableDetailData[$key] = $this->__groupByGroup($value)->toArray();
			}
			$data['groupControl'] = $this->__getInfoGroup();
			$data['tableDetailData'] = $tmpTableDetailData;
			$data['actionType']="edit";
			return view('vh::edit.view'.$tableData->type_show,$data);
		}
		else{
			return redirect($this->admincp.'/view/'.$table);
		}
	}
	public function edit_order($table,$tableData,$id){
		$tableDetailData = self::__getListDetailTable($table);
		$dataItem =DB::table($table)->where('id',$id)->get();
		if(count($dataItem)>0){
			$data['tableData'] = new Collection($tableData);
			$addDetailData = $this->_edittrait_getTableEditProperties($data['tableData']->get("id"),$tableData);
			$data['dataItem'] = $this->_edittrait_getDataFromTableProperties($table,$addDetailData,$dataItem[0]);
			$tableDetailData = collect($tableDetailData);
			$tableDetailData = $tableDetailData->merge($addDetailData);
			$tableDetailData = $this->__groupByRegion($tableDetailData);
			$tmpTableDetailData= array();
			foreach ($tableDetailData as $key => $value) {
				$tmpTableDetailData[$key] = $this->__groupByGroup($value)->toArray();
			}
			$data['groupControl'] = $this->__getInfoGroup();
			$data['tableDetailData'] = $tmpTableDetailData;
			$data['actionType']="edit";
			return view('vh::edit.view_order',$data);
		}
		else{
			return redirect($this->admincp.'/view/'.$table);
		}
	}

	public function edit_config($table,$tableData,$id){
		$regions = $this->getConfigRegions($table);
		$data['tableData'] = collect($tableData);
		$data['listRegions'] = $regions;
		$data['listConfigs'] = collect(\DB::table($table)->where("act",1)->orderBy("ord","asc")->get());
		$data['actionType'] = 'edit';
		$data['table']=new VDetailTable;
		return view('vh::edit.view_config',$data);
	}
	
	public function save(Request $request,$table,$id){
		$table = VTable::where('table_map',$table)->take(1)->get();
		$fnc = "__update_normal";
		if($table->count()>0){
			$fnc = "__update".$table->get(0)->type_show;
			if(!method_exists($this, $fnc)){
				$fnc = "__update_normal";
			}
			if($table->get(0)->table_map == "user_question"){
				$this->sendMail($request->all());
			}	
			if($table->get(0)->table_map == "promotions"){
				$checkTime = $this->checkTimeEdit($request);
				if($checkTime !== true){
					return redirect()->back()->withErrors(['time'=>$checkTime]);
				}
			}
			if($table->get(0)->table_map == "combos"){
				$checkTime = $this->checkTimeEditCombo($request);
				if($checkTime !== true){
					return redirect()->back()->withErrors(['time'=>$checkTime]);
				}
			}
			$ret = $this->$fnc($request,$table->get(0),$id);
		}
		$returnurl = $request->get('returnurl');
		$returnurl = isset($returnurl) && trim($returnurl)!="" ? base64_decode($returnurl):$this->admincp;
		return Redirect::to($returnurl);
	}
	


	private function sendMail(array $data)
	{	$user = \App\Models\User::findOrFail($data['user_id']);
		$content = [
			'question' => $data['content'],
			'replied' => $data['replied'],
		];
		Mail::to($user->email)->send(new \App\Notifications\SendMailQuestion(
			$content,
			$view="mail.question_rep",
			$subject="Trả lời câu hỏi",
		));
		return response()->json([
			'code'=>200,
			'message'=> 'Câu hỏi đã được gửi',
		]);
	}

	public function update(Request $request,$table,$id){
		$ret = $this->__update($request,$table,$id);
		$table = VTable::where('table_map',$table)->take(1)->get();
		$fnc = "__update_normal";
		$ret = 0;
		if($table->count()>0){
			$fnc = "__update".$table->get(0)->type_show;
			if(!method_exists($this, $fnc)){
				$fnc = "__update_normal";
			}
			$ret = $this->$fnc($request,$table->get(0),$id);
		}
		switch ($ret) {
			case 100:
				return JsonHelper::echoJson(100,"Thiếu thông tin dữ liệu");
			break;
			case 200:
				return JsonHelper::echoJson(200,"Cập nhật thành công");
			break;
			default:
				return JsonHelper::echoJson(150,"Cập nhật không thành công");	
			break;
		}
	}
	public function addAllToParent(Request $request,$table){
		$ret = 100;
		if($request->isMethod('post')){
			$post = $request->post();
			$arrId= json_decode($post['groupid']);
			$parent= $post['parent'];
			$type= $post['type'];
			$ret = $this->__addToParent($table,$arrId,$parent,strtolower($type));
		}
		switch ($ret) {
			case 100:
				return JsonHelper::echoJson(100,"Thiếu thông tin dữ liệu");
			break;
			case 200:
				return JsonHelper::echoJson(300,"Cập nhật thành công");
			break;
			default:
				return JsonHelper::echoJson(150,"Cập nhật không thành công");	
			break;
		}
	}
	public function removeFromParent(Request $request,$table){
		$ret = 100;
		if($request->isMethod('post')){
			$post = $request->post();
			$arrId= json_decode($post['groupid']);
			$parent= $post['parent'];
			$type= $post['type'];
			$ret =$this->__removeFromParent($table,$arrId,$parent,strtolower($type));
		}
		switch ($ret) {
			case 100:
				return JsonHelper::echoJson(100,"Thiếu thông tin dữ liệu");
			break;
			case 200:
				return JsonHelper::echoJson(300,"Cập nhật thành công");
			break;
			default:
				return JsonHelper::echoJson(150,"Cập nhật không thành công");	
			break;
		}
	}
	private function __removeFromParent($table,$arrId,$parent,$type){
		if($type=='select'){
			$ret = DB::table($table)->whereIn('id',$arrId)->update(array('parent'=>NULL));
		}
		elseif($type=='multiselect'){
			$arr = DB::table($table)->whereIn('id',$arrId)->get();
			foreach ($arr as $key => $value) {
				$tmp = $value->parent;
				$tmp = explode(',',$tmp);
				if(!in_array($parent, $tmp)){
					unset($tmp[$parent]);
					DB::table($table)->where('id',$value->id)->update(array('parent'=>implode(',', $tmp)));
				}
			}
		}
		else{
			return 150;
		}
		\Event::dispatch('vanhenry.manager.removefromparent.success', array($table,$parent,$arrId));
		return 200;
	}
	private function __addToParent($table,$arrId,$parent,$type){
		if($type=='select'){
			$ret = DB::table($table)->whereIn('id',$arrId)->update(array('parent'=>$parent));
		}
		elseif($type=='multiselect'){
			$arr = DB::table($table)->whereIn('id',$arrId)->get();
			foreach ($arr as $key => $value) {
				$tmp = $value->parent;
				$tmp = explode(',',$tmp);
				if(!in_array($parent, $tmp)){
					array_push($tmp, $parent);
					DB::table($table)->where('id',$value->id)->update(array('parent'=>implode(',', $tmp)));
				}
			}
		}
		else{
			return 150;
		}
		\Event::dispatch('vanhenry.manager.addtoparent.success', array($table,$parent,$arrId));
		return 200;
	}
	private function _edittrait_updatePropertiesNormal($table,$post){
		$tablename  =$table->table_map;
		$table_meta = $tablename."_metas";
		if(Schema::hasTable($table_meta)){
			$tableData =new Collection(self::__getListTable()[$tablename]);
			$addDetailData = TableProperty::where("act",1)->where("parent",$tableData->get("id"))->orderBy("ord")->get()->toArray();
			$arrAdd = collect($addDetailData)->implode("name", ",");
			$arrAdd = explode(",", $arrAdd);
			$lang = $table->lang;
			$lang = explode(",", $lang);
			$ret = array();
			foreach ($post as $key => $value) {
				
				if(strpos($key, "_")==2){
					$l = substr($key, 0,2);
					if(in_array($l, $lang)){
						$ret[$key] = $value;
						unset($post[$key]);
						continue;
					}
				}
				if(in_array($key, $arrAdd)){
					$ret[$key] = $value;
					unset($post[$key]);
					continue;
				}
			}
			$total = array();
			foreach ($ret as $key => $value) {
				$_key = $key;
				if(strpos($key, "_")==2){
					$_key = substr($key, 2+1);
				}
				if(array_key_exists($_key, $total)){
					continue;
				}
				$tmp = array();
				if(array_key_exists($_key, $ret)){
					$tmp["vi"]= $ret[$_key];
				}
				foreach ($lang as $k => $v) {
					if(array_key_exists($v."_".$key, $ret)){
						$tmp[$v]= $ret[$v."_".$key];
					}
				}
				$total[$_key] = $tmp;
			}
			DB::table($table_meta)->where("source_id",$post["id"])->delete();
			$arrInsert = array();
			foreach ($addDetailData as $key => $value) {
				if(array_key_exists($value["name"], $total)){
					foreach ($total[$value["name"]] as $k => $v) {
						$tmp = array("source_id"=>$post["id"],"meta_key"=>$k=="vi"?"":$k."_","meta_value"=>($v==$value["name"]||$v==$k."_".$value["name"])?"":$v,"prop_id"=>$value["id"]);
						array_push($arrInsert, $tmp);
					}
				}
			}
			DB::table($table_meta)->insert($arrInsert);
		}
		return $post;
	}
	private function __update_normal(Request $request,$table,$id){
		if($request->isMethod('post')){
			$data = $request->post();
			if(isset($data['_token']))
			{
				unset($data['_token']);
			}
			$x = \Event::dispatch('vanhenry.manager.update_normal.preupdate', array($table,$data,$id));
			if(count($x)>0){
				foreach ($x as $kx => $vx) {
					if(!$vx['status']){		
						return $vx["code"];
					}
				}
			}
			$injects = array();
			foreach ($data as $key => $value) {
				if(strpos($key, "_inject_")===0){
					$injects[$key] = $value;
					unset($data[$key]);
				}
			}
			$outs = array();
			foreach ($data as $key => $value) {
				if(strpos($key, "_out_")===0){
					$outs[$key] = $value;
					unset($data[$key]);
				}
			}


			$data = $this->_edittrait_updatePropertiesNormal($table,$data);
			foreach ($data as $key => $value) {
				if(is_array($value)){
					$data[$key]= implode(',', $value);
				}
			}
			$tech5s_controller = $data['tech5s_controller'];
			unset($data['tech5s_controller']);
			if(isset($data['slug'])){
				$_arrSlug = DB::table('v_routes')->where(array('table'=>$table->table_map,'map_id'=>$id))->get();
				if(count($_arrSlug)>0){
					if($_arrSlug[0]->vi_link == $data['slug']){
						
					}
					else{
						$data['slug'] = FCHelper::generateSlug('v_routes', $data['slug']);
						$ret = DB::table('v_routes')->where('id',$_arrSlug[0]->id)->update(array('updated_at'=>new \DateTime(),'vi_link'=>$data['slug']));
					}
				}
				else {
					$dataRoutes = array(
						'controller'=>$tech5s_controller,
						'vi_link'=>$data['slug'],
						'table'=>$table->table_map,
						'vi_name'=>isset($data['name'])?$data['name']:"",
						'map_id'=>$data['id'],
						'updated_at'=>new \DateTime(),
						'created_at'=>new \DateTime(),
						'is_static'=>0,
					);
					$ret = DB::table('v_routes')->insert($dataRoutes);
				}
			}
			/*update bảng dịch nếu có*/

			$data = $this->__updateTranslationTable($table, $data, $id);

			if(isset($data['updated_at'])){
				$data["updated_at"]=new \DateTime();
			}
			$pivots = [];
			foreach ($data as $key => $value) {
				if (strpos($key, 'pivot_') === 0) {
					$pivots[$key] = $value;
					unset($data[$key]);
				}
			}
			if($table->table_map == 'combos' ){
				$data['start_at'] = date('Y-m-d H:i:s',strtotime($data['start_at']));
				$data['expired_at'] = date('Y-m-d H:i:s',strtotime($data['expired_at']));
				unset($data['action']);
			}
			$ret = DB::table($table->table_map)->where('id',$id)->update($data);
			if($ret >=0){
				$this->__updatePivots($data['id'], $pivots, $table->table_map);
				$this->_updateOutRefernce($table->table_map,$outs,$id);
				\Event::dispatch('vanhenry.manager.update_normal.success', array($table,$data,$injects,$id));
				return 200;
			}
			else{
				return 150;
			}
		}
		else{
			return 100;
		}
	}
	private function __updateTranslationTable($table, $data, $map_id , $langChoose = 'vi')
	{

		$transTable = \FCHelper::getTranslationTable($table->table_map);
		/*nếu table có bảng dịch thì update bảng dịch*/
		if ($transTable == null) {
			return $data;
		}
		/*Tách data của bảng gốc và bảng dịch ra*/
		[$originData, $transData] = FCHelper::filterData($transTable, $data);
		/*danh sách các ngôn ngữ website đang sử dụng*/
		$locales = \Config::get('app.locales', []);
		/*Ngôn ngữ đang thao tác với bảng*/
		$langChoose = FCHelper::langChooseOfTable($table->table_map);
		if (!array_key_exists($langChoose, $locales)) {
			return $originData;
		}
		/*update translation table*/
		$transDb = \DB::table($transTable->table_map)->where(['map_id' => $map_id, 'language_code' => $langChoose])->first();
		if ($transDb == null) {
			return $originData;
		}

		if (isset($transData['slug'])) {
			if (strlen($transData['slug']) == 0) {
				$slugWithLang = \Str::slug($transData['name'], '_');
			}
			else{
				$slugWithLang = $transData['slug'];
			}
			$transData['slug'] = FCHelper::generateSlugWithLanguage($slugWithLang, $langChoose, $map_id);
		// 	// update route
			if($table->controller !== null){
				$vRoute = \DB::table('v_routes')->where('table', $table->table_map)->where('map_id', $map_id)->first();
				$insOrUpVroute = [
					$langChoose.'_name' => $data['name'],
					$langChoose.'_link' => $data['slug'],
					$langChoose.'_seo_title' => $data['seo_title'] ?? '',
					$langChoose.'_seo_key' => $data['seo_key'] ?? '',
					$langChoose.'_seo_des' => $data['seo_des'] ?? '',
					'updated_at' => new \DateTime,
				];
				if ($vRoute == null) {
					$insOrUpVroute['controller'] = $table->controller;
					$insOrUpVroute['table'] = $table->table_map;
					$insOrUpVroute['map_id'] = $map_id;
					$insOrUpVroute['is_static'] = 0;
					\DB::table('v_routes')->insert($insOrUpVroute);
				}
				else{
					\DB::table('v_routes')->where('table', $table->table_map)->where('map_id', $map_id)->update($insOrUpVroute);
				}
			}

			return $data;
		}
		\DB::table($transTable->table_map)->where(['map_id' => $map_id, 'language_code' => $langChoose])->update($transData);
		return $originData;
	}
	private function __updatePivots($itemId, $pivots, $table)
	{
		foreach ($pivots as $key => $pivot) {
			$vdetail = VDetailTable::where(['name' => $key, 'parent_name' => $table])->first();
			if ($vdetail == null) {
				continue;
			}
			$defaultData = json_decode($vdetail->default_data, true);
			if (!is_array($defaultData)) {
				continue;
			}
			$pivot_table = $defaultData['pivot_table'];
			$target_field = $defaultData['target_field'];
			$origin_field = $defaultData['origin_field'];
			$pivotValues = array_filter(explode(',', $pivot));
			foreach ($pivotValues as $value) {
				$pivotDb = \DB::table($pivot_table)->where([$origin_field => $itemId, $target_field => $value])->first();
				if ($pivotDb == null) {
					\DB::table($pivot_table)->insert([
						$origin_field => $itemId,
						$target_field => $value,
						'created_at' => new \DateTime,
						'updated_at' => new \DateTime,
					]);
				}
				else{
					\DB::table($pivot_table)->where([$origin_field => $itemId, $target_field => $value])->update(['updated_at' => new \DateTime]);
				}
			}
			/*xóa các bản ghi trong bảng pivot không tồn tại trong list user đã chọn*/
			if (count($pivotValues) > 0) {
				\DB::table($pivot_table)->where($origin_field, $itemId)->whereNotIn($target_field, $pivotValues)->delete();
			}
			else{
				\DB::table($pivot_table)->where($origin_field, $itemId)->delete();
			}
		}
	}
	private function __update_config($request,$table,$id){
		if($request->isMethod('post')){
			$data = $request->post();
			if(isset($data['_token']))
			{
				unset($data['_token']);
			}
			$pureDataKey = $this->__groupConfigs($data);
			$tableData = self::__getListTable()[$table->table_map];
			$multilang = json_decode($tableData->default_data,true);
			$multilang = isset($multilang)?$multilang:array();
			$localTable = $table->table_map;
			$ret = DB::transaction(function () use ($pureDataKey,$data,$multilang,$localTable){
				foreach ($pureDataKey as $key => $value) {
					$c = GlobalHelper::getModel($localTable);
					$_realkey = substr($value,3);
					$c1= $c->find(strtoupper($_realkey));
					foreach ($multilang as $klang => $vlang) {
						$_reallang = $klang."_value";
						if(array_key_exists($klang."_".$_realkey, $data)){
							$tmp = $data[$klang."_".$_realkey];
							$c1->$_reallang =is_array($tmp)?implode(",", $tmp):$tmp;
						}
						else if(array_key_exists("vi_".$_realkey, $data)){
							$tmp = $data["vi_".$_realkey];
							$c1->$_reallang =is_array($tmp)?implode(",", $tmp):$tmp;
						}
					}
					$r = $c1->save();
				}
				return 200;
			});
			if($ret ==200){
				\Event::dispatch('vanhenry.manager.update_config.success', array($table,$data,$id));
			}
			return $ret;
		}
		else{
			return 100;
		}
	}
	private function  __groupConfigs($data){
		$pureDataKey = array();
		foreach ($data as $key => $value) {
			if(\Str::startsWith($key,'vi_')){
				array_push($pureDataKey,$key);
			}
		}
		return $pureDataKey;
	}
	private function __update_menu($request,$table,$id){
		if($request->isMethod('post')){
			$post = $request->post();
			$data = json_decode($post['data']);
			DB::table($table)->where('id',$id)->delete();
		}
	}

	public function showAttribute(Request $request){
		if(isset($request->product_cateogory_id)){
			$attribute_ids = \App\Models\CategoryAttribute::whereIn('product_category_id',$request->product_cateogory_id)->groupBy('attribute_id')->pluck('attribute_id');
			$attributes = \App\Models\Attribute::whereIn('id',$attribute_ids)->with('values')->get();

			$valueInProduct = \App\Models\ProductAttributeValue::where('product_id',$request->product_id)->groupBy('attribute_value_id')->pluck('attribute_value_id');
		}else{
			$attributes = [];
			$valueInProduct = [];
		}

		return response()->json(['html'=>view('vh::path.fetch_attribute',compact('attributes','valueInProduct'))->render()]);
	}

	public function getDistricts(Request $request,$route,$link){
		$districts = \Support::showDistricts($request->province_id);
		return response()->json($districts);
	}

	public function getWards(Request $request,$route,$link){
		$wards = \Support::showWards($request->district_id);
		return response()->json($wards);
	}
}
?>