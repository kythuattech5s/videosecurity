<?php 
namespace vanhenry\manager\controller;
use vanhenry\manager\model\VConfigRegion;
use vanhenry\manager\model\Config;
use vanhenry\manager\model\VDetailTable;
use vanhenry\manager\model\TableProperty;
use Illuminate\Support\Facades\Schema;
use App\Models\FlashSaleTimeSlot;
use DB;
use FCHelper;
trait ViewTrait{
	public function view($table){
		$tableData = self::__getListTable()[$table];
		$tableDetailData = self::__getListDetailTable($table);
		$type = $tableData->type_show;
		$fnc = 'view'.$type;
		if($tableData->table_map == 'flash_sales'){
			$fnc = 'indexFlashSale';
		}
		elseif($tableData->table_map == "combos"){
			$fnc = 'indexCombo';
		}
		elseif($tableData->table_map == 'promotions'){
			$fnc = 'indexPromotion';	
		}
		elseif($tableData->table_map == 'vouchers'){
			$fnc = 'indexVoucher';	
		}
		elseif($tableData->table_map == 'deals'){
			$fnc = 'indexDeal';	
		}
		else{
			if(!method_exists($this,$fnc)){
				$fnc= 'view_normal';
			}
		}
		return $this->$fnc($table,$tableData,$tableDetailData);	
	}
	private function _getTableProperties($table_id){
		$tmp =  TableProperty::where("act",1)->where("parent",$table_id)->orderBy("ord")->get()->toArray();
		foreach ($tmp as $key => $value) {
			$value["is_prop"] = 1;
			$tmp[$key] = (object)$value;
		}
		return $tmp;
	}
	/*
	Lấy thông tin giá trị meta tương ứng với các bản ghi và các trường properties
	**/
	public function indexDeal($table,$tableData,$tableDetailData){
		$all=DB::table($table)->select('id')->orderBy('id','DESC')->pluck('id');
		$arrProduct = [];
		$arrProductSub = [];
		foreach($all as $deal_id){
			$product_id = DB::table('deal_product_mains')->where('deal_id',$deal_id)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->orderBy('id','DESC')->get();
			$arrProduct[] = $products;
		}
		foreach($all as $deal_id){
			$product_id = DB::table('deal_product_subs')->where('deal_id',$deal_id)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->orderBy('id','DESC')->get();
			$arrProductSub[] = $products;
		}

		$comming = DB::table($table)->where('start_at','>',new \DateTime)->paginate(10);

		$arrProductComing = [];
		foreach($comming as $date){
			$product_id = DB::table('deal_product_mains')->where('deal_id',$date->id)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->orderBy('id','DESC')->get();
			$arrProductComing[] = $products;
		}

		$arrProductSubComing = [];
		foreach($comming as $date){
			$product_id = DB::table('deal_product_subs')->where('deal_id',$date->id)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->orderBy('id','DESC')->get();
			$arrProductSubComing[] = $products;
		}

		$happenning = DB::table($table)->where('start_at','<',new \DateTime)->where('expired_at','>',new \DateTime)->paginate(10);

		$arrProductHappenning = [];
		foreach($happenning as $date){
			$product_id = DB::table('deal_product_mains')->where('deal_id',$date->id)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->orderBy('id','DESC')->get();
			$arrProductHappenning[] = $products;
		}

		$arrProductSubHappenning = [];
		foreach($happenning as $date){
			$product_id = DB::table('deal_product_subs')->where('deal_id',$date->id)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->orderBy('id','DESC')->get();
			$arrProductSubHappenning[] = $products;
		}

		$ending = DB::table($table)->where('start_at','<',new \DateTime)->where('expired_at','<',new \DateTime)->paginate(10);

		$arrProductEnding = [];
		foreach($ending as $date){
			$product_id = DB::table('deal_product_mains')->where('deal_id',$date->id)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->orderBy('id','DESC')->get();
			$arrProductEnding[] = $products;
		}

		$arrProductSubEnding = [];
		foreach($ending as $date){
			$product_id = DB::table('deal_product_subs')->where('deal_id',$date->id)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->orderBy('id','DESC')->get();
			$arrProductSubEnding[] = $products;
		}


		$data['tableData'] = collect($tableData);
		$data['tableDataComming'] = $comming;
		$data['tableDataHappending'] = $happenning;
		$data['tableDataEnding'] = $ending;
		$tmp = collect($tableDetailData);
		$addDetailData = $this->_getTableProperties($data['tableData']->get("id"));
		$merge = $tmp->merge($addDetailData);
		//Thông tin chi tiết bảng
		$data['tableDetailData'] = $tmp;
		$listData = $this->getDataTable($table,$data);
		$data['tableDetailData'] = $merge;
		$data['products'] = $arrProduct;
		$data['productsComing'] = $arrProductComing;
		$data['productsHappen'] = $arrProductHappenning;
		$data['productsEnd'] = $arrProductEnding;
		$data['productsSub'] = $arrProductSub;
		$data['productsSubComing'] = $arrProductSubComing;
		$data['productsSubHappen'] = $arrProductSubHappenning;
		$data['productsSubEnd'] = $arrProductSubEnding;
		$data["listData"] = $this->_getDataFromTableProperties($table,$addDetailData,$listData);
		$view_vouchers = 'deal.index';
		$view = \View::exists('vh::view.view'.$tableData->type_show)?'vh::view.'.$view_vouchers:'vh::view.view_normal';
		return view($view,$data);
	}
	public function indexVoucher($table,$tableData,$tableDetailData){
		$all=DB::table($table)->select('id')->orderBy('id','DESC')->pluck('id');
		$arrProduct = [];
		foreach($all as $voucherId){
			$product_id = DB::table('product_voucher')->where('voucher_id',$voucherId)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProduct[] = $products;
		}

		$langChoose = FCHelper::langChooseOfTable($tableData->table_map);
		$comming = DB::table($table)->where('start_at','>',new \DateTime)->orderBy('id','DESC')->paginate(10);

		$arrProductComing = [];
		foreach($comming as $voucher){
			$product_id = DB::table('product_voucher')->where('voucher_id',$voucher->id)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProductComing[] = $products;
		}

		$happenning = DB::table($table)->where('start_at','<',new \DateTime)->where('expired_at','>',new \DateTime)->orderBy('id','DESC')->paginate(10);

		$arrProductHappenning = [];
		foreach($happenning as $voucher){
			$product_id = DB::table('product_voucher')->where('voucher_id',$voucher->id)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProductHappenning[] = $products;
		}

		$ending = DB::table($table)->where('expired_at','<',new \DateTime)->orderBy('id','DESC')->paginate(10);

		$arrProductEnding = [];
		foreach($ending as $voucher){
			$product_id = DB::table('product_voucher')->where('voucher_id',$voucher->id)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProductEnding[] = $products;
		}


		$data['tableData'] = collect($tableData);
		$data['tableDataComming'] = $comming;
		$data['tableDataHappending'] = $happenning;
		$data['tableDataEnding'] = $ending;
		$tmp = collect($tableDetailData);
		$addDetailData = $this->_getTableProperties($data['tableData']->get("id"));
		$merge = $tmp->merge($addDetailData);
		//Thông tin chi tiết bảng
		$data['tableDetailData'] = $tmp;
		$listData = $this->getDataTable($table,$data);
		$data['tableDetailData'] = $merge;
		$data['products'] = $arrProduct;
		$data['productsComing'] = $arrProductComing;
		$data['productsHappen'] = $arrProductHappenning;
		$data['productsEnd'] = $arrProductEnding;
		$data["listData"] = $this->_getDataFromTableProperties($table,$addDetailData,$listData);
		$view_vouchers = 'voucher.index';
		$view = \View::exists('vh::view.view'.$tableData->type_show)?'vh::view.'.$view_vouchers:'vh::view.view_normal';
		return view($view,$data);
	}

	public function indexFlashSale($table,$tableData,$tableDetailData){
		$all=DB::table($table)->select('id')->orderBy('id','DESC')->pluck('id');
		$arrProduct = [];

		foreach($all as $flash_sale_id){
			$product_id = DB::table('flash_sale_products')->where('flash_sale_id',$flash_sale_id)->where('act',1)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProduct[] = $products;
		}

		$comming = DB::table($table)->where('start_at','>',new \DateTime)->orderBy('id','DESC')->paginate(10);
		$arrProductComing = [];
		foreach($comming as $flash_sale_id){
			$product_id = DB::table('flash_sale_products')->where('flash_sale_id',$flash_sale_id->id)->where('act',1)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProductComing[] = $products;
		}

		$happenning = DB::table($table)->where('start_at','<=',new \DateTime)->where('expired_at','>=',new \DateTime)->orderBy('id','DESC')->paginate(10);
		$arrProductHappenning = [];
		
		foreach($happenning as $flash_sale_id){
			$product_id = DB::table('flash_sale_products')->where('flash_sale_id',$flash_sale_id->id)->where('act',1)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProductHappenning[] = $products;
		}

		$ending = DB::table($table)->where('expired_at','<',new \DateTime)->orderBy('id','DESC')->paginate(10);
		$arrProductEnding = [];
		
		foreach($ending as $flash_sale_id){
			$product_id = DB::table('flash_sale_products')->where('flash_sale_id',$flash_sale_id->id)->where('act',1)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProductEnding[] = $products;
		}

		$data['time_slot'] = FlashSaleTimeSlot::all();
		$data['tableData'] = collect($tableData);
		$data['tableDataComming'] = $comming;
		$data['tableDataHappending'] = $happenning;
		$data['tableDataEnding'] = $ending;
		$tmp = collect($tableDetailData);
		$addDetailData = $this->_getTableProperties($data['tableData']->get("id"));
		$merge = $tmp->merge($addDetailData);
		//Thông tin chi tiết bảng
		$data['tableDetailData'] = $tmp;
		$listData = $this->getDataTable($table,$data);
		$data['tableDetailData'] = $merge;
		$data['products'] = $arrProduct;
		$data['productsComing'] = $arrProductComing;
		$data['productsHappen'] = $arrProductHappenning;
		$data['productsEnd'] = $arrProductEnding;
		$data["listData"] = $this->_getDataFromTableProperties($table,$addDetailData,$listData);
		$view_flash_sale = 'flashsale.index';
		$view = \View::exists('vh::view.view'.$tableData->type_show)?'vh::view.'.$view_flash_sale:'vh::view.view_normal';
		return view($view,$data);
	}

	public function indexCombo($table,$tableData,$tableDetailData){
		$all=DB::table($table)->select('id')->orderBy('id','DESC')->pluck('id');

		$arrProduct = [];
		foreach($all as $combo_id){
			$product_id = DB::table('combo_product')->where('combo_id',$combo_id)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProduct[] = $products;
		}

		$langChoose = FCHelper::langChooseOfTable($tableData->table_map);
		$comming = DB::table($table)->orderBy('id','DESC')->where('start_at','>',new \DateTime)->paginate(10);

		$arrProductComing = [];
		foreach($comming as $combo){
			$product_id = DB::table('combo_product')->where('combo_id',$combo->id)->where('act',1)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProductComing[] = $products;
		}

		$happenning = DB::table($table)->where('start_at','<=',new \DateTime)->where('expired_at','>=',new \DateTime)->orderBy('id','DESC')->paginate(10);

		$arrProductHappenning = [];
		foreach($happenning as $combo){
			$product_id = DB::table('combo_product')->where('combo_id',$combo->id)->where('act',1)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProductHappenning[] = $products;
		}

		$ending = DB::table($table)->where('expired_at','<',new \DateTime())->orderBy('id','DESC')->paginate(10);

		$arrProductEnding = [];
		foreach($ending as $combo){
			$product_id = DB::table('combo_product')->where('combo_id',$combo->id)->where('act',1)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProductEnding[] = $products;
		}


		$data['tableData'] = collect($tableData);
		$data['tableDataComming'] = $comming;
		$data['tableDataHappending'] = $happenning;
		$data['tableDataEnding'] = $ending;
		$tmp = collect($tableDetailData);
		$addDetailData = $this->_getTableProperties($data['tableData']->get("id"));
		$merge = $tmp->merge($addDetailData);
		//Thông tin chi tiết bảng
		$data['tableDetailData'] = $tmp;
		$listData = $this->getDataTable($table,$data);
		$data['tableDetailData'] = $merge;
		$data['products'] = $arrProduct;
		$data['productsComing'] = $arrProductComing;
		$data['productsHappen'] = $arrProductHappenning;
		$data['productsEnd'] = $arrProductEnding;
		$data["listData"] = $this->_getDataFromTableProperties($table,$addDetailData,$listData);
		$view_combo = 'combo.index';
		$view = \View::exists('vh::view.view'.$tableData->type_show)?'vh::view.'.$view_combo:'vh::view.view_normal';
		return view($view,$data);
	}

	public function indexPromotion($table,$tableData,$tableDetailData)
	{
		$all=DB::table($table)->select('id')->orderBy('id','DESC')->pluck('id');
		$arrProduct = [];
		foreach($all as $promotion_id){
			$product_id = DB::table('product_promotion')->where('promotion_id',$promotion_id)->where('act',1)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProduct[] = $products;
		}

		$langChoose = FCHelper::langChooseOfTable($tableData->table_map);

		$comming = DB::table($table)->where('promotions.start_at','>',new \DateTime)->orderBy('id','DESC')->paginate(10);

		$arrProductComing = [];
		foreach($comming as $promotion){
			$product_id = DB::table('product_promotion')->where('promotion_id',$promotion->id)->where('act',1)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProductComing[] = $products;
		}

		$happenning = DB::table($table)->join($transTable->table_map, 'promotions.id', '=', 'promotion_translations.map_id')->where('promotion_translations.language_code',$langChoose)->where('start_at','<=',new \DateTime)->where('expired_at','>=',new \DateTime)->orderBy('id','DESC')->paginate(10);

		$arrProductHappenning = [];
		foreach($happenning as $promotion){
			$product_id = DB::table('product_promotion')->where('promotion_id',$promotion->id)->where('act',1)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->get();
			$arrProductHappenning[] = $products;
		}

		$ending = DB::table($table)->join($transTable->table_map, 'promotions.id', '=', 'promotion_translations.map_id')->where('promotion_translations.language_code',$langChoose)->where('start_at','<',new \DateTime)->where('expired_at','<',new \DateTime)->orderBy('id','DESC')->paginate(10);

		$arrProductEnding = [];
		foreach($ending as $promotion){
			$product_id = DB::table('product_promotion')->where('promotion_id',$promotion->id)->where('act',1)->pluck('product_id');
			$products =  \App\Models\Product::select('img','slug','name')->whereIn('id',$product_id)->orderBy('id','DESC')->get();
			$arrProductEnding[] = $products;
		}


		$data['tableData'] = collect($tableData);
		$data['tableDataComming'] = $comming;
		$data['tableDataHappending'] = $happenning;
		$data['tableDataEnding'] = $ending;
		$tmp = collect($tableDetailData);
		$addDetailData = $this->_getTableProperties($data['tableData']->get("id"));
		$merge = $tmp->merge($addDetailData);
		//Thông tin chi tiết bảng
		$data['tableDetailData'] = $tmp;
		$listData = $this->getDataTable($table,$data);
		$data['tableDetailData'] = $merge;
		$data['products'] = $arrProduct;
		$data['productsComing'] = $arrProductComing;
		$data['productsHappen'] = $arrProductHappenning;
		$data['productsEnd'] = $arrProductEnding;
		$data["listData"] = $this->_getDataFromTableProperties($table,$addDetailData,$listData);
		$view_promotion = 'promotion.index';
		$view = \View::exists('vh::view.view'.$tableData->type_show)?'vh::view.'.$view_promotion:'vh::view.view_normal';
		return view($view,$data);
	}

	private function _getDataFromTableProperties($table,$detaildata,$listData){
		$table_meta = $table."_metas";
		if(Schema::hasTable($table_meta)){
			$detaildata = collect($detaildata);
			$inProp = $detaildata->implode('id', ',');
			$inProp = explode(",", $inProp);
			$inData = $listData->implode("id", ",");
			$inData = explode(",", $inData);
			$arrProp = DB::table($table_meta)->whereIn("prop_id",$inProp)->whereIn("source_id",$inData)->get();
			$collectProp = collect($arrProp)->groupBy("source_id");
			$tmpListData=$listData->getCollection()->keyBy("id");
			$detaildata = $detaildata->keyBy("id");
			foreach ($collectProp as $kprops => $props) {
				foreach ($props as $kprop => $prop) {
					$_tmp = $prop->meta_key.FCHelper::ep($detaildata->get($prop->prop_id),"name");
					$_out = $tmpListData->get($kprops);
					$_out->$_tmp = $prop->meta_value;
				}
			}
		}
		return $listData;
	}
	private function _view_trait_getListTrash($table,$data){
		$tableData= $data['tableData'];
		$tableDetailData= $data['tableDetailData'];
		$rpp =$tableData['rpp_admin'];
		$rpp = \vanhenry\helpers\helpers\StringHelper::isNull($rpp)?10:$rpp;
		$query = DB::table($table);
		$fieldSelect = $this->getFieldSelectTable($table,$tableDetailData);
		$query = $query->select($fieldSelect);
		return $query->where("trash",1)->orderBy('id','desc')->paginate($rpp);
	}
	public function trashview($table){
		$tableData = self::__getListTable()[$table];
		$tableDetailData = self::__getListDetailTable($table);
		$data['tableData'] = collect($tableData);
		$tmp = collect($tableDetailData);
		$addDetailData = $this->_getTableProperties($data['tableData']->get("id"));
		$merge = $tmp->merge($addDetailData);
		//Thông tin chi tiết bảng
		$data['tableDetailData'] = $tmp;
		$listData = $this->_view_trait_getListTrash($table,$data);
		$data['tableDetailData'] = $merge;
		$data["listData"] = $this->_getDataFromTableProperties($table,$addDetailData,$listData);
		return view('vh::view.viewtrash',$data);
	}
	public function view_normal($table,$tableData,$tableDetailData){
		//Thông tin bảng
		$data['tableData'] = collect($tableData);
		$tmp = collect($tableDetailData);
		$addDetailData = $this->_getTableProperties($data['tableData']->get("id"));
		$merge = $tmp->merge($addDetailData);
		//Thông tin chi tiết bảng
		$data['tableDetailData'] = $tmp;
		$listData = $this->getDataTable($table,$data);
		$data['tableDetailData'] = $merge;
		$data["listData"] = $this->_getDataFromTableProperties($table,$addDetailData,$listData);
		$view = \View::exists('vh::view.view'.$tableData->type_show)?'vh::view.view'.$tableData->type_show:'vh::view.view_normal';
		return view($view,$data);
	}
	public function view_user($userid){
		$table="total_orders";
		$tableData = self::__getListTable()[$table];
		$tableDetailData = self::__getListDetailTable($table);
		$type = $tableData->type_show;
		//Thông tin bảng
		$data['tableData'] = collect($tableData);
		$tmp = collect($tableDetailData);
		//Thông tin chi tiết bảng
		$data['tableDetailData'] = $tmp;
		$tableData= $data['tableData'];
		$tableDetailData= $data['tableDetailData'];
		$rpp =$tableData['rpp_admin'];
		$rpp = \vanhenry\helpers\helpers\StringHelper::isNull($rpp)?10:$rpp;
		$query = DB::table($table);
		$query = $query->where("user_id",$userid);
		$listData = $query->orderBy('id','desc')->paginate($rpp);
		$data["listData"] = $listData;
		$data["cuser"] = \App\User::find($userid);
		$view = 'vh::view.view_user';
		return view($view,$data);
	}
	/*#View Config*/
	public function view_config($table,$tableData,$tableDetailData){
		return redirect($this->admincp.'/edit/'.$table."/0");
	}
	private function getConfigRegions($table='configs'){
		$regions = VConfigRegion::where(array('act'=>1,'parent'=>'0'))->where("table",$table)->orderBy('ord','asc')->get();
		foreach ($regions as $key => $value) {
			$value->childs = VConfigRegion::where(array('act'=>1,'parent'=>$value->id))->orderBy('ord','asc')->get();
		}
		return $regions;
	}
	/*#View Config*/
	/*#View Menu*/
	public function view_menu($table,$tableData,$tableDetailData){
		$data['tableData'] = collect($tableData);
		$data['groupMenus'] = $this->getGroupMenus($tableData->table_parent);
		$data['menus'] = collect($this->generateMenu($tableData->table_map,0));
		return view('vh::view.view_menu',$data);
	}
	public function view_permis($table,$tableData,$tableDetailData){
		return redirect($this->admincp.'/edit/'.$table."/0");
	}
	private function generateMenu($tableName,$parent){
		$arr = \DB::table($tableName)->where('parent',$parent)->get();
		foreach ($arr as $key => $value) {
			$value -> childs = $this->generateMenu($tableName,$value->id);
		}
		return $arr;
	}
	private function getGroupMenus($nameParent){
		return \DB::table($nameParent)->where('act',1)->get();
	}
	/*#View Menu*/	
}
?>