<?php 
namespace vanhenry\manager\controller;
use vanhenry\helpers\helpers\StringHelper as StringHelper;
use vanhenry\helpers\JsonHelper as JsonHelper;
use Illuminate\Support\Facades\Cache as Cache;
use DB;
use Carbon\Carbon;
use vanhenry\helpers\CT as CT;
use Illuminate\Support\Collection ;
use \Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use vanhenry\manager\model\VTable;
use vanhenry\manager\model\VDetailTable;
use Illuminate\Support\Facades\Schema;
use App\Models\{Combo,Voucher,Promotion,FlashSale,Product};
use vanhenry\manager\model\TableProperty;
use FCHelper;
trait InsertTrait{
	public function insert($table){
		$tableDetailData = self::__getListDetailTable($table);
		$data['dataItem'] = array();
		$tableData = self::__getListTable()[$table];
		$data['tableData'] = new Collection($tableData);
		$addDetailData = $this->_edittrait_getTableEditProperties($data['tableData']->get("id"),$tableData);
		$tableDetailData = collect($tableDetailData);
		$tableDetailData = $tableDetailData->merge($addDetailData);
		$tableDetailData = $this->__groupByRegion($tableDetailData);
		$tmpTableDetailData= array();
		foreach ($tableDetailData as $key => $value) {
			$tmpTableDetailData[$key] = $this->__groupByGroup($value)->toArray();
		}
		if($table == "combos"){
			$data['groupControl'] = $this->__getInfoGroup();
			$data['tableDetailData'] = $tmpTableDetailData;
			$data['actionType']="insert";
			return view('vh::view.combo.create',$data);
		}
		if($table == "vouchers"){
			$data['groupControl'] = $this->__getInfoGroup();
			$data['tableDetailData'] = $tmpTableDetailData;
			$data['actionType']="insert";
			$data['categories'] = \App\Models\ProductCategory::all();
			$data['productsAll'] = \App\Models\Product::orderBy('id','DESC')->get()->take(10);
			return view('vh::view.voucher.create',$data);
		}
		elseif($table == "promotions"){
			$data['groupControl'] = $this->__getInfoGroup();
			$data['tableDetailData'] = $tmpTableDetailData;
			$data['actionType']="insert";
			return view('vh::view.promotion.create',$data);
		}
		elseif($table == "deals"){
			$data['groupControl'] = $this->__getInfoGroup();
			$data['tableDetailData'] = $tmpTableDetailData;
			$data['actionType']="insert";
			return view('vh::view.deal.create',$data);
		}
		else{
			$data['groupControl'] = $this->__getInfoGroup();
			$data['tableDetailData'] = $tmpTableDetailData;
			$data['actionType']="insert";
			return view('vh::edit.view'.$data['tableData']->get("type_show"),$data);
		}
	}
	public function store(Request $request,$table){
		$ret = $this->__insert($request, $table);
		$returnurl = $request->get('returnurl');
		$returnurl = isset($returnurl) && trim($returnurl)!="" ? base64_decode($returnurl):$this->admincp;
		return Redirect::to($returnurl);
	}
	public function storeAjax(Request $request,$table){
		$data = $request->post();
		$ret = $this->__insert($request, $table);
		switch ($ret) {
			case 100:
				return JsonHelper::echoJson(100,"Thiếu thông tin dữ liệu");
				break;
			case 200:
				return JsonHelper::echoJson(200,"Thêm mới thành công");
				break;
			default:
				return JsonHelper::echoJson(150,"Thêm mới không thành công");	
				break;
		}
	}
	public function viewDetail($table,$id){

		$tableDetailData = self::__getListDetailTable($table);
		$dataItem =DB::table($table)->where('id',$id)->get();
		if($table == 'vouchers'){
			$dataItem =DB::table($table)->where('vouchers.id',$id)->get();
		}
		if($table == 'promotions'){
			$dataItem =DB::table($table)->where('promotions.id',$id)->get();
		}
		if(count($dataItem)>0){
			$data['dataItem'] = $dataItem[0];
			$tableData = self::__getListTable()[$table];
			$data['tableData'] = new Collection($tableData);
			$tableDetailData = $this->__groupByRegion($tableDetailData);
			$data['transTable'] = \FCHelper::getTranslationTable($table);
			$tmpTableDetailData= array();
			foreach ($tableDetailData as $key => $value) {
				$tmpTableDetailData[$key] = $this->__groupByGroup($value)->toArray();
			}
			$data['groupControl'] = $this->__getInfoGroup();
			$data['tableDetailData'] = $tmpTableDetailData;
			$data['actionType']="copy";
			if($table == 'flash_sales'){
				$data['categories'] = \App\Models\ProductCategory::all();
				$data['timeslot'] = \App\Models\FlashSaleTimeSlot::find($data['dataItem']->time_slot);
				$data['products'] = \App\Models\Product::select('f.*','p.name','p.slug','p.img','p.code','p.id as productid','p.price_old','p.price as price_origin')
				->from('products as p')
				->join('flash_sale_products as f','f.product_id','=','p.id')
				->where('f.flash_sale_id',$data['dataItem']->id)
				->where('p.act',1)
				->get();
				$productHasInFlashSaleId = $data['products']->pluck('product_id');
				$data['productsAll'] = $this->removeProductAlreadyInAnotherPromotion($table,$id);
				return view('vh::view.flashsale.detail',$data);
			}
			elseif($table == 'promotions'){
				$data['categories'] = \App\Models\ProductCategory::all();
				$flashSaleId = \App\Models\FlashSaleProduct::pluck('product_id');
				$ComboId = \App\Models\ComboProduct::where('combo_id',$data['dataItem']->id)->pluck('product_id');
				$DealProductMainId = \App\Models\DealProductMain::pluck('product_id');
				$DealProductSubId = \App\Models\DealProductSub::pluck('product_id');
				$promotionId = \App\Models\ProductPromotion::pluck('product_id');
				$data['products'] = \App\Models\Product::select('pp.*','p.name','p.img','p.code','p.price_old','p.price as price_origin')
				->from('products as p')
				->join('product_promotion as pp','pp.product_id','=','p.id')
				->join('product_product_category as ppc','p.id','=','ppc.product_id')
				->join('product_categories as pc','pc.id','=','ppc.product_category_id')
				->where('pp.promotion_id',$data['dataItem']->id)
				->where('p.act',1)
				->groupBy('p.id')
				->get();
				
				$data['productsAll'] = $this->removeProductAlreadyInAnotherPromotion($table,$id);
				return view('vh::view.promotion.detail',$data);
			}
			elseif($table == 'combos'){
				$data['categories'] = \App\Models\ProductCategory::all();
				$flashSaleId = \App\Models\FlashSaleProduct::pluck('product_id');
				$ComboId = \App\Models\ComboProduct::where('combo_id',$data['dataItem']->id)->pluck('product_id');
				$DealProductMainId = \App\Models\DealProductMain::pluck('product_id');
				$DealProductSubId = \App\Models\DealProductSub::pluck('product_id');
				$data['products'] = \App\Models\Product::select('cp.*','b.name as name_pc','p.name','p.img','p.code','p.price_old','p.price as price_origin')
				->from('products as p')
				->join('combo_product as cp','cp.product_id','=','p.id')
				->join('product_product_category as ppc','p.id','=','ppc.product_id')
				->join('product_categories as pc','pc.id','=','ppc.product_category_id')
				->join('brands as b','b.id','=','p.brand_id')				
				->where('cp.combo_id',$data['dataItem']->id)
				->where('p.act',1)
				->where('cp.act',1)
				->groupBy('p.id')
				->get();

				$data['productsAll'] = $this->removeProductAlreadyInAnotherPromotion($table,$id);
				return view('vh::view.combo.detail',$data);
			}
			
			elseif($table == 'vouchers'){

				$data['categories'] = \App\Models\ProductCategory::all();
				
				$productVoucherId = \App\Models\VoucherProduct::where('voucher_id',$data['dataItem']->id)->pluck('product_id');

				$data['products'] = \App\Models\Product::select('pv.*','p.name','p.img','p.code','p.price_old','p.price as price_origin')
				->from('products as p')
				->join('product_voucher as pv','pv.product_id','=','p.id')
				->join('product_product_category as ppc','p.id','=','ppc.product_id')
				->join('product_categories as pc','pc.id','=','ppc.product_category_id')
				->where('pv.voucher_id',$data['dataItem']->id)
				->where('p.act',1)
				->groupBy('p.id')
				->get();
				
				$data['productsAll'] = $this->removeProductAlreadyInAnotherPromotion($table,$id);
				return view('vh::view.voucher.detail',$data);
			}
			else{
				return view('vh::edit.view_detail',$data);
			}
		}
		else{
			return redirect($this->admincp.'/view/'.$table);
		}
	}

	public function removeProductAlreadyInAnotherPromotion($table,$id){
		$saleInstance = null;
		switch ($table) {
			case 'vouchers':
				$saleInstance = Voucher::where('id', $id)->first();
				$productInPromotionID = \App\Models\VoucherProduct::where('voucher_id',$id)->pluck('product_id');
				break;
			case 'combos':
				$saleInstance = Combo::where('id', $id)->first();
				$productInPromotionID = \App\Models\ComboProduct::where('combo_id',$id)->pluck('product_id');
				break;
			case 'promotions':
				$saleInstance = Promotion::where('id', $id)->first();
				$productInPromotionID = \App\Models\ProductPromotion::where('promotion_id',$id)->pluck('product_id');
				break;
			case 'flash_sales':
				$saleInstance = FlashSale::where('id', $id)->first();
				$productInPromotionID = \App\Models\FlashSaleProduct::where('flash_sale_id',$id)->pluck('product_id');
				break;
		}
		if ($saleInstance == null) {
			return \Support::json(['code' => 100, 'message' => 'Không tồn tại loại khuyến mãi']);
		}
		$productChooses = Product::whereDoesntHave('flashSale', function($q) use($saleInstance){
    		$q->where('start_at', '<', $saleInstance->start_at)->where('expired_at', '>', $saleInstance->expired_at);
    		if ($saleInstance instanceof FlashSale) {
    			$q->where('flash_sales.id', $saleInstance->id);
    		}
    	})
    	->whereDoesntHave('deal', function($q) use($saleInstance){ // sp deal chính
    		$q->where(function($q2) use($saleInstance){
    			$q2->where('start_at', '<', $saleInstance->start_at)->where('expired_at', '>', $saleInstance->expired_at);
			});
    		if ($saleInstance instanceof Deal) {
    			$q->where('deals.id', $saleInstance->id);
    		}
    	})
    	->whereDoesntHave('getDealSub', function($q) use($saleInstance){ // sp deal đi kèm
    		$q->where(function($q2) use($saleInstance){
    			$q2->where('start_at', '<', $saleInstance->start_at)->where('expired_at', '>', $saleInstance->expired_at);
			});
    		if ($saleInstance instanceof Deal) {
    			$q->where('deals.id', $saleInstance->id);
    		}
    	})
    	->whereDoesntHave('combo', function($q) use($saleInstance){ // sp deal chính
    		$q->where(function($q2) use($saleInstance){
    			$q2->where('start_at', '<', $saleInstance->start_at)->where('expired_at', '>', $saleInstance->expired_at);
			});
    		if ($saleInstance instanceof Combo) {
    			$q->where('combos.id', $saleInstance->id);
    		}
    	})
    	->orderBy('id', 'desc')
    	->take(10)
    	->whereNotIn('id',$productInPromotionID)
    	->where('act',1)
    	->get();
    	return $productChooses;
	}

	public function copy($table,$id){
		$tableDetailData = self::__getListDetailTable($table);
		$transTable = \FCHelper::getTranslationTable($table);
		if ($transTable == null) {
			$dataItem =DB::table($table)->where('id',$id)->get();	
		}
		else{
			$langChoose = FCHelper::langChooseOfTable($table);
			$dataItem =DB::table($table)->join($transTable->table_map.' as t', 't.map_id', '=', $table.'.id')->where('language_code', $langChoose)->where('id',$id)->get();		
		}
		if(count($dataItem)>0){
			$data['dataItem'] = $dataItem[0];
			$tableData = self::__getListTable()[$table];
			$data['tableData'] = new Collection($tableData);
			$tableDetailData = $this->__groupByRegion($tableDetailData);
			$tmpTableDetailData= array();
			foreach ($tableDetailData as $key => $value) {
				$tmpTableDetailData[$key] = $this->__groupByGroup($value)->toArray();
			}
			$data['groupControl'] = $this->__getInfoGroup();
			$data['tableDetailData'] = $tmpTableDetailData;
			$data['actionType']="copy";
			
			if($table == "combos"){
				return view('vh::view.combo.copyView',$data);
			}elseif($table == 'vouchers'){
				$data['categories'] = \App\Models\ProductCategory::all();
				
				$productVoucherId = \App\Models\VoucherProduct::where('voucher_id',$data['dataItem']->id)->pluck('product_id');

				$data['products'] = \App\Models\Product::select('pv.*','p.name','p.img','p.code','p.price_old','p.price as price_origin')
				->from('products as p')
				->join('product_voucher as pv','pv.product_id','=','p.id')
				->join('product_product_category as ppc','p.id','=','ppc.product_id')
				->join('product_categories as pc','pc.id','=','ppc.product_category_id')
				->where('pv.voucher_id',$data['dataItem']->id)
				->where('p.act',1)
				->groupBy('p.id')
				->get();
				
				$data['productsAll'] = \App\Models\Product::orderBy('id','DESC')
				->whereNotIn('id',$productVoucherId)
				->where('act',1)
				->get()
				->take(10);
				return view('vh::view.voucher.copyView',$data);
			}
			else{
				return view('vh::edit.view1',$data);
			}
		}
		else{
			return redirect($this->admincp.'/view/'.$table);
		}
	}
	private function _inserttrait_getPropertiesNormal($table,$post){
		$tablename  =$table->table_map;
		$table_meta = $tablename."_metas";
		$ret = array();
		if(Schema::hasTable($table_meta)){
			$tableData =new Collection(self::__getListTable()[$tablename]);
			$addDetailData = TableProperty::where("act",1)->where("parent",$tableData->get("id"))->orderBy("ord")->get()->toArray();
			$arrAdd = collect($addDetailData)->implode("name", ",");
			$arrAdd = explode(",", $arrAdd);
			$lang = $table->lang;
			$lang = explode(",", $lang);
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
		}
		return array("rawpost"=>$post,"properties"=>$ret);
	}
	public function __insert(Request $request, $table){

		if($request->isMethod('post')){
			$table = VTable::where('table_map',$table)->take(1)->get()->get(0);
			$data = $request->post();
			if(isset($data['_token']))
			{
				unset($data['_token']);
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
			$x= \Event::dispatch('vanhenry.manager.insert.preinsert', array($table,$data,0));
			if(count($x)>0){
				foreach ($x as $kx => $vx) {
					if(!$vx['status']){		
						return $vx["code"];
					}
				}
			}
			$_d = $this->_inserttrait_getPropertiesNormal($table,$data);
			$data = $_d["rawpost"];
			foreach ($data as $key => $value) {
				if(is_array($value)){
					$data[$key]= implode(',', $value);
				}
			}
			$tech5s_controller = $data['tech5s_controller'];
			unset($data['tech5s_controller']);
			$transTable = \FCHelper::getTranslationTable($table->table_map);
			/*nếu table có bảng dịch thì insert bảng dịch*/
			if ($transTable != null) {
				/*Tách dữ liệu bảng dịch nếu có*/
				[$data, $transData] = FCHelper::filterData($transTable, $data);
			}
			$pivots = [];
			foreach ($data as $key => $value) {
				if (strpos($key, 'pivot_') === 0) {
					$pivots[$key] = $value;
					unset($data[$key]);
				}
			}
			if(isset($data['slug'])){
				if (strlen($data['slug']) == 0) {
					$slug = \Str::slug($data['name'], '_');
				}
				else{
					$slug = $data['slug'];
				}
				$data['slug'] = FCHelper::generateSlug('v_routes', $slug);
				$_id = DB::table($table->table_map)->insertGetId($data);
				if($_id<=0){
					DB::rollBack();
					return 150;
				}
				$dataRoutes = array(
					'controller'=>$tech5s_controller,
					'vi_link'=>$data['slug'],
					'table'=>$table->table_map,
					'vi_name'=>isset($data['name'])?$data['name']:"",
					'map_id'=>$_id,
					'updated_at'=>new \DateTime(),
					'created_at'=>new \DateTime(),
					'is_static'=>0,
				);
				$ret = DB::table('v_routes')->insert($dataRoutes);
			}
			else{
				$_id = DB::table($table->table_map)->insertGetId($data);
			}
			\DB::beginTransaction();

			if($_id >0){
				$this->__insertPivots($_id, $pivots, $table->table_map);
				/*insert translation table*/
				if (isset($transData)) {
					$t = $this->__insertTranslationTable($table, $transTable, $transData, $_id);
					if ($t == false) {
						\DB::rollback();
						return 150;
					}
				}
				\DB::commit();
				
				$dataTableProperties  = $_d["properties"];
				$dataTableProperties["id"] = $_id;
				$this->_edittrait_updatePropertiesNormal($table,$dataTableProperties);
				//update out reference table
				$this->_updateOutRefernce($table->table_map,$outs,$_id);
				\Event::dispatch('vanhenry.manager.insert.success', array($table,$data,$injects,$_id));
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
	private function __insertTranslationTable($table, $transTable, $transData, $map_id)
	{	
		/*danh sách các ngôn ngữ website đang sử dụng*/
		$locales = \Config::get('app.locales', []);
		$transData['map_id'] = $map_id;
		$insRoutes = [];
		foreach ($locales as $localeCode => $value) {
			if (isset($transData['slug'])) {
				if (strlen($transData['slug']) == 0) {
					$slugWithLang = \Str::slug($transData['name'], '_');
				}
				else{
					$slugWithLang = $transData['slug'];
				}
				$transData['slug'] = FCHelper::generateSlugWithLanguage($slugWithLang, $localeCode, $map_id);
			}
			$transData['language_code'] = $localeCode;
			if (isset($transData['seo_title']) && $transData['seo_title'] == '') {
				$transData['seo_title'] = $transData['name'];
			}
			if (isset($transData['seo_key']) && $transData['seo_key'] == '') {
				$transData['seo_title'] = $transData['name'];
			}
			if (isset($transData['seo_des']) && $transData['seo_des'] == '') {
				$transData['seo_title'] = $transData['name'];
			}
			$ins = \DB::table($transTable->table_map)->insert($transData);
			if ($ins == false) {
				return false;
			}
			if(isset($transData['slug'])){
				$insRoutes[$localeCode.'_name'] = $transData['name'];
				$insRoutes[$localeCode.'_link'] = $transData['slug'];
				$insRoutes[$localeCode.'_seo_title'] = $transData['seo_title'];
				$insRoutes[$localeCode.'_seo_key'] = $transData['seo_key'];
				$insRoutes[$localeCode.'_seo_des'] = $transData['seo_des'];
			}
		}
		if (isset($insRoutes)) {
			if(count($insRoutes) !== 0){
			$insRoutes['controller'] = $table->controller;
			$insRoutes['table'] = $table->table_map;
			$insRoutes['map_id'] = $map_id;
			$insRoutes['is_static'] = 0;
			$insRoutes['created_at'] = new \DateTime;
			$insRoutes['updated_at'] = new \DateTime;
			\DB::table('v_routes')->insert($insRoutes);
			}
		}
		return true;
	}
	private function __insertPivots($itemId, $pivots, $table)
	{
		foreach ($pivots as $key => $pivot) {
			$vdetail = VDetailTable::where(['parent_name' => $table, 'name' => $key])->first();
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
				\DB::table($pivot_table)->insert([
					$origin_field => $itemId,
					$target_field => $value,
					'created_at' => new \DateTime,
					'updated_at' => new \DateTime,
				]);
			}
		}
	}
	private function _updateOutRefernce($table,$outs,$id){
		foreach ($outs as $k => $out) {
			if(is_array($out)){
				$map = VDetailTable::where("parent_name",$table)->where("name",$k)->first();
				if($map!=null){
					$tableRef = $map->more_note;
					$tableMap = $table."_".$tableRef;
					if(!\Schema::hasTable($tableMap)){
						$tableMap = $tableRef."_".$table;
					}
					if(\Schema::hasTable($tableMap)){
						\DB::table($tableMap)->where(\Str::singular($table)."_id",$id)->delete();
						foreach ($out as $o) {
							\DB::table($tableMap)->insert([\Str::singular($table)."_id"=>$id,\Str::singular($tableRef)."_id"=>$o]);
						}
					}
				}
			}
			else{
				$map = VDetailTable::where("parent_name",$table)->where("name",$k)->first();
				if($map!=null){
					$tableRef = $map->more_note;
					$tableMap = $table."_".$tableRef;
					if(!\Schema::hasTable($tableMap)){
						$tableMap = $tableRef."_".$table;
					}
					if(\Schema::hasTable($tableMap)){
						\DB::table($tableMap)->where(\Str::singular($table)."_id",$id)->delete();
						\DB::table($tableMap)->insert([\Str::singular($table)."_id"=>$id,\Str::singular($tableRef)."_id"=>$out]);
					}
				}
			}
		}
	}
	public function updateRefer($table){
		if(request()->isMethod("post")){
			$inputs = request()->input();
			$data = $inputs["data"];
			$form = $inputs["form"];
			parse_str($form,$form);
			$json = json_decode($data,true);
			$from = $json["from"];
			$to = $json["to"];
			$cv = $json["data"];
			$dtcv = array();
			foreach ($cv as $key => $values) {
				foreach($values as $k =>$v){
					if(array_key_exists($k,$form)){
						$dtcv[$v] = $form[$k];
					}
				}
			}
			$idto = array_key_exists($from, $form)?$form[$from]:0;
			$ret = \DB::table($table)->where($to,$idto)->update($dtcv);
			return response()->json(["message"=>"Cập nhật"]);
		}
	}
}
?>

