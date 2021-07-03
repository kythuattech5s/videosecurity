<?php 
namespace vanhenry\manager\controller;
use Illuminate\Http\Request;
use vanhenry\manager\helpers\DetailTableHelper;
use Illuminate\Support\Facades\Cache as Cache;
use DB;
use View;
use App;
use FCHelper;
use Carbon\Carbon as Carbon;
use vanhenry\helpers\helpers\StringHelper as StringHelper;
use vanhenry\helpers\helpers\JsonHelper as JsonHelper;
use Illuminate\Http\RedirectResponse as Redirect;
use Illuminate\Database\Eloquent\Collection as Collection;
use vanhenry\manager\helpers\CT as CT;
use vanhenry\manager\helpers\ModelHelper;
use vanhenry\manager\middleware\HUserAuthenticate;
use Illuminate\Support\Facades\Auth;
use vanhenry\manager\model\HGroupModule;
use vanhenry\manager\model\HGroupUser;
use vanhenry\manager\model\HModule;
use vanhenry\manager\model\HRole;
use Hash;
use Illuminate\Support\Facades\Schema;
use PDF;
use vanhenry\manager\synKiotViet as synKiotViet;
use App\Models\{Deal, Combo, Promotion, flashSale, Product};
use vanhenry\helpers\helpers\SettingHelper;

class Admin extends BaseAdminController
{
	use TableTrait,SearchTrait,EditPermissionTrait,EditTrait,InsertTrait,ViewTrait,ImportTrait,JBIGTrait,MenuTrait,PromotionTrait,DealTrait,MarketingTrait,VoucherTrait,StatisticTrait,AuctionTrait,ExportTrait,FlashSaleTrait,ComboTrait,OrderTrait,PreOrderTrait, AnTrait,SapoTrait,RakutenTrait;
	public function __construct() {
		parent::__construct();
    	$this->middleware('h_users',['except'=>['synQuantityFromKiot','termService','viewDetail','getRecursive']]);
    }
    private function curlDataGet($url){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_TIMEOUT, 20);
		$result = curl_exec($ch);
		curl_close($ch);
		return $result;
    }
	public function index(){
		// return view('vh::index');
		$gaViewKey = SettingHelper::getSetting('ga_view_key');
		return view('vh::dashboard', compact('gaViewKey'));
	}
	public function dashboard(Request $request)
	{
		$gaViewKey = SettingHelper::getSetting('ga_view_key');
		return view('vh::dashboard', compact('gaViewKey'));
	}
	public function termService(){
		return view('vh::other.rule');
	}
	public function changeLanguage($lang){
		if(StringHelper::isNull($lang))
		{
			$lang = 'vi';
		}
		CT::setLanguage($lang);
		return redirect()->back();
	}
	public function changePass(Request $request){
		if($request->isMethod('post')){
			$post = $request->post();
			$data['password'] = bcrypt($post['newpass']);
			$oldpass = $post['currentpass'];
			$id = $this->retreiveUser('id');
			$password = $this->retreiveUser('password');
			if(Hash::check($oldpass, $password))
			{
				$ret = ModelHelper::update('h_users',$id,$data);
			 return	JsonHelper::echoJson($ret,trans('db::edit')." ".trans('db::success'));
			}
			else{
			 return	JsonHelper::echoJson(150,trans('db::wong_current_pass'));
			}
		}
		else{
		 return	JsonHelper::echoJson(100,trans('db::missing_field'));
		}
	}
	public function __call($method,$args){
		switch ($method) {
			case 'getDataTable':
				switch (count($args)) {
					case 4:
						return call_user_func_array(array($this, 'getDataTableThreeArg'), $args);
					case 2:
						return call_user_func_array(array($this, 'getDataTableTwoArg'), $args);
				}
				break;
		}
	}
	public function view404(){
		return view('vh::404');
	}
	public function noPermission(){
		return view('vh::no_permission');
	}
	private function redirect404(){
		return redirect()->route('404');
	}
	private function getDataTableTwoArg($table,$data){
		$tableData= $data['tableData'];
		$tableDetailData= $data['tableDetailData'];
		$rpp =$tableData['rpp_admin'];
		$rpp = StringHelper::isNull($rpp)?10:$rpp;
		$query = DB::table($table);
		return $this->getDataTableThreeArg($query,$tableDetailData,$rpp,$table);
	}
	private function getDataTableThreeArg($query,$tableDetailData,$rpp,$table){
		$fieldSelect = $this->getFieldSelectTable($table,$tableDetailData);
		$ctrash = $tableDetailData->filter(function ($value, $key) {
		    return $value->name=="trash";
		});
		/*Nếu có bảng dịch thì lấy thêm dữ liệu của bảng này*/
		$transTable = FCHelper::getTranslationTable($table);
		if ($transTable == null) {
			$query = $query->select($fieldSelect);
		}
		else{
			$langChoose = FCHelper::langChooseOfTable($table);
			$query = $query->select($fieldSelect)->join($transTable->table_map, "$table.id", '=', "$transTable->table_map.map_id")->where("$transTable->table_map.language_code", $langChoose);
		}
		if($ctrash->count()>0){
			$query =$query->whereRaw("($table.trash <> 1 or $table.trash is null)");
		}
		return $query->orderBy("$table.id",'desc')->paginate($rpp);
	}
	/**
	 * Danh sách trường active hiển thị của từng bảng
	 */
	private function getFieldSelectTable($table,$tableDetailData,$force = false){
		if(!$tableDetailData instanceof Collection){
			$tableDetailData = new Collection($tableDetailData);
		}
		$fieldSelect = Cache::remember(CT::$KEY_CACHE_QUERY.$table, CT::$TIME_CACHE_QUERY, function() use($tableDetailData,$force) {
			$filterShow = DetailTableHelper::filterDataShow($tableDetailData,$force);
			$fieldSelect = array();
			$filterShow = $filterShow->filter(function($v, $k){
				return !\Str::startsWith($v->name, 'pivot_');
			});
			foreach ($filterShow as $key => $value) {
				array_push($fieldSelect, $value->parent_name.'.'.$value->name);
			}
			return $fieldSelect;
		});
		return $fieldSelect;
	}
	public function getData(Request $request,$table){
		$inputs = $request->input();
		$arr = DB::table($table)->select(array('id', 'name as text'))->where('name','like','%'.$inputs['q'].'%')->get();
		$obj= new \stdClass();
		$obj->results = $arr;
		echo json_encode($obj);
	}
	public function getDataPivot()
	{
		$inputs = request()->input();
		if (!\Support::checkStr($inputs['origin_table'])) {
			return;
		}
		$perpage = 10;
		$page = $inputs['page'];
		$target_table = request()->table;
		/*nếu muốn lấy field # name thì phải name_field as text*/
		$results = \DB::table($target_table)->select('id', 'name as text');
		$transTable = FCHelper::getTranslationTable($target_table);
		$langChoose = FCHelper::langChooseOfTable($inputs['origin_table']);
		if ($transTable != null) {
			$results->join($transTable->table_map, 'id', '=', 'map_id');
		}
		if (\Support::checkStr($inputs['q'])) {
			$results->where('name', 'like', '%'.$inputs['q'].'%')->where('language_code', $langChoose);
		}
		$results = $results->where('act', 1)->groupBy('id')->paginate($perpage);
		$arr = ['results' => $results->getCollection(), 'pagination' => ['more' => $perpage * $page < $results->total()]];
		return response()->json($arr);
	}
	public function getRecursive(Request $request,$table){
		$inputs = $request->input();
		$defaultData = $inputs["data"];
		$arrKey = json_decode($defaultData,true);
		$data = FCHelper::er($arrKey,'data');
		$config = FCHelper::er($arrKey,'config');
		$isAjax = $config["ajax"];
		$table= $data["table"];
		$dataMapDefault= $data["default"];
		$arrData = DetailTableHelper::recursiveDataTable($data);
    	DetailTableHelper::printOptionRecursiveData($arrData,0,$dataMapDefault,array(),array());
	}
	public function delete(Request $request,$table){
		$inputs = $request->input();
		if(@$inputs['id']){
			DB::beginTransaction();
          	try {
				$x = \Event::dispatch('vanhenry.manager.delete.predelete', array($table,$inputs['id']));
				if(count($x)>0){
					foreach ($x as $kx => $vx) {
						if(!$vx['status']){		
							return JsonHelper::echoJson(100,trans('db::delete')." ".trans('db::fail'));
						}
					}
				}
				$ret = ModelHelper::delete($inputs,$table);
				\Event::dispatch('vanhenry.manager.delete.success', array($table,$inputs['id']));
				DB::commit();
				if($ret == 200){
					$this->deletePivot([$inputs['id']], $table);
					$this->deleteTranslation([$inputs['id']], $table);
					\DB::table('v_routes')->where(['map_id' => $inputs['id'], 'table' => $table])->delete();
					return	JsonHelper::echoJson($ret,trans('db::delete')." ".trans('db::success'));
				}
				else{
					return	JsonHelper::echoJson($ret,trans('db::missing_field'));	
				}
	        } catch (\Exception $e) {
	          	DB::rollBack();
	          return	JsonHelper::echoJson($ret,trans('db::missing_field'));
	        }
		}
		else{
		 return	JsonHelper::echoJson(100,trans('db::missing_field'));	
		}
	}
	public function deletePivot($ids, $table)
	{
		/*list field of table*/
		$tableDetailData = self::__getListDetailTable($table);
		$pivots = $tableDetailData->filter(function($v, $k){
			return \Str::before($v->name, '_') == 'pivot';
		});
		foreach ($pivots as $key => $value) {
			$defaultData = json_decode($value->default_data, true);
			if (!is_array($defaultData)) {
				continue;
			}
			$pivot_table = $defaultData['pivot_table'];
			$target_field = $defaultData['target_field'];
			$origin_field = $defaultData['origin_field'];
			if($defaultData['target_table'] === "combo"){}
			\DB::table($pivot_table)->whereIn($origin_field, $ids)->delete();
		}
	}
	public function deleteTranslation($mapIds, $table)
	{
		$transTable = FCHelper::getTranslationTable($table);
		if ($transTable != null) {
			\DB::table($transTable->table_map)->whereIn('map_id', $mapIds)->delete();
		}
	}
	public function trash(Request $request,$table){
		$ret = ModelHelper::trash($request->input(),$table);
		if($ret == 200){
		 return	JsonHelper::echoJson($ret,trans('db::trash')." ".trans('db::success'));
		}
		else{
		 return	JsonHelper::echoJson($ret,trans('db::missing_field'));	
		}
	}
	public function backtrash(Request $request,$table){
		$ret = ModelHelper::trash($request->input(),$table,0);
		if($ret == 200){
		 return	JsonHelper::echoJson($ret,trans('db::restore')." ".trans('db::success'));
		}
		else{
		 return	JsonHelper::echoJson($ret,trans('db::missing_field'));	
		}
	}
	public function deleteAll(Request $request, $table){
		$inputs = $request->input();
		if(@$inputs['id']){
			$id = json_decode($inputs['id'],true);
			$id = $id ==null?array():$id;
			$x = \Event::dispatch('vanhenry.manager.delete.predelete', array($table,$id));
			if(count($x)>0){
				foreach ($x as $kx => $vx) {
					if(!$vx['status']){		
					 return	JsonHelper::echoJson(100,trans('db::delete')." ".trans('db::fail'));
						return;
					}
				}
			}
			$ret = DB::table($table)->whereIn('id',$id)->delete();
			$this->deletePivot($id, $table);
			$this->deleteTranslation($id, $table);
			\DB::table('v_routes')->whereIn('map_id',$id)->where('table', $table)->delete();
			\Event::dispatch('vanhenry.manager.delete.success', array($table,$id));
			return	JsonHelper::echoJson(200,$ret>0?trans('db::delete')." ".trans('db::success'):trans('db::delete')." ".trans('db::fail'));
		}
		else{
		 return	JsonHelper::echoJson(100,trans('db::missing_field'));	
		}
	}
	public function getCrypt(Request $request){
		$post = $request->input();
		$pass = $post["pass"];
		echo bcrypt($pass);
		return;
	}
	public function editableAjax(Request $request,$table){
		if($request->isMethod('post')){
			$post = $request->input();
			$id = $post['id'];
			$prop = isset($post["prop"])?$post["prop"]:0;
			$propid = isset($post["prop_id"])?$post["prop_id"]:0;
			unset($post['id']);
			unset($post['_token']);
			unset($post['prop']);
			unset($post['prop_id']);
			if($prop==1){
				$table_meta = $table."_metas";
				if(Schema::hasTable($table_meta)){
					$count = count(DB::table($table_meta)->where('source_id',$id)->where('prop_id',$propid)->get());
					$meta_value = count($post)>0?array_values($post)[0]:"";
					if($count>0){
						$ret = DB::table($table_meta)->where('source_id',$id)->where('prop_id',$propid)->where("meta_key","")->update(array("meta_value"=>$meta_value));
					}
					else{
						$tableData = self::__getListTable()[$table];
						$lang = $tableData->lang;
						$lang = explode(",", $lang);
						$arrInsert = array(array('source_id' => $id, 'prop_id' => $propid,"meta_key"=>"","meta_value"=>$meta_value));
						foreach ($lang as $k => $v) {
							array_push($arrInsert, array('source_id' => $id, 'prop_id' => $propid,"meta_key"=>$v."_","meta_value"=>$meta_value));
						}
						$ret = DB::table($table_meta)->insert(
						    $arrInsert
						);
					}
					$ret = $ret?200:100;
				}
				else{
					$ret = 150;
				}
			}
			else{
				$transTable = FCHelper::getTranslationTable($table);
				if ($transTable != null) {
					/*lấy all field của bảng dịch*/
					$fields = \DB::select('SELECT `COLUMN_NAME` FROM `INFORMATION_SCHEMA`.`COLUMNS` WHERE `TABLE_SCHEMA`= database() AND `TABLE_NAME`="'.$transTable->table_map.'"');
					$arrField = [];
					foreach ($fields as $key => $field) {
						$arrField[] = $field->COLUMN_NAME;
					}
					$locales = \Config::get('app.locales', []);
					$langChoose = FCHelper::langChooseOfTable($table);
					foreach ($post as $key => $value) {
						if (in_array($key, $arrField) && array_key_exists($langChoose, $locales)) {
							if ($key == 'slug') {
								$value = generateSlugWithLanguage($table, $value, $langChoose, $id);
							}
							\DB::table($transTable->table_map)->where(['map_id' => $id, 'language_code' => $langChoose])->update([$key => $value]);
							unset($post[$key]);
							$ret = 200;	
						}
					}
				}
				if (count($post) > 0) {
					$ret = ModelHelper::update($table,$id,$post);
				}
				\Event::dispatch('vanhenry.manager.update_normal.success', array($table,$post,[],$id));
			}
		 return	JsonHelper::echoJson($ret,trans('db::edit')." ".trans('db::success'));
		}
		else{
		 return	JsonHelper::echoJson(100,trans('db::missing_field'));
		}
	}
	function crawlProduct(){
		return view('vh::crawl_product');
	}
	function crawlExcute(){
		$get = \Request::input();
		if(empty($get['link']) || strlen(trim($get['link'])) == 0) return redirect()->back()->with('typeNotify', 'error')->with('messageNotify', 'Bạn phải nhập link sản phẩm trên digikey');
		$linkCrawl = $get['link'];
		$responses = $this->curl($linkCrawl);
		if(!isset($responses['response'])) return redirect()->back()->with('typeNotify', 'error')->with('messageNotify', 'Không thể lấy dữ liệu');
		$str = $responses['response'];
		include_once app_path('Helpers/Simple_html_dom.php');
		$html = str_get_html($str);
		
		$nameProduct = $html->find('#reportPartNumber', 0);
		if(!$nameProduct) return redirect()->back()->with('typeNotify', 'error')->with('messageNotify', 'Không thể lấy tên sản phẩm');
		$data['name'] = trim($nameProduct->plaintext);
		$data['en_name'] = trim($nameProduct->plaintext);
		$data['code'] = trim($nameProduct->plaintext);
		// check sp với mã sp nếu chưa exists in table pro thì mới câu tiếp
		$prosIsExists = \DB::table('products')->where('code', $data['code'])->get();
		if(count($prosIsExists) > 0) return redirect()->back()->with('typeNotify', 'error')->with('messageNotify', 'Sản phẩm với mã sản phẩm "'.$data['code'].'" đã tồn tại');
		$data['slug'] = \Str::slug($data['name']);
		$data['storage'] = isset($get['storage']) ? (int)$get['storage'] : '';
		$data['price'] = isset($get['price']) ? (int)$get['price'] : '';
		if(isset($get['parent']) && count($get['parent']) > 0){
			$data['parent'] = implode(',', $get['parent']);
		}
		$overview = $html->find('#product-overview', 0);
		if($overview){
			$removeThisElement = $overview->find('.copyBtnTd');
			if($removeThisElement){
				foreach($removeThisElement as $item){
					$item->outertext = '';
					$item->innertext = '';
				}
			}
			// $quantity = $overview->find('tbody tr', 1)->find('#dkQty', 0);
			// if($quantity != null){
			// 	$data['storage'] = preg_replace('/,|\./', '', trim($quantity->plaintext));
			// }
			$trs = $quantity = $overview->find('tbody tr');
			foreach($trs != null ? $trs : [] as $k => $tr){
				$th = $tr->find('th', 0);
				if($th == null) continue;
				switch ($th->plaintext) {
					case 'Manufacturer':
						if($th->next_sibling() != null){
							$data['producer'] = trim($th->next_sibling()->plaintext);
						}
						break;
					case 'Manufacturer Part Number':
						if($th->next_sibling() != null){
							$data['manufactory_code'] = trim($th->next_sibling()->plaintext);
						}
						break;
					// case 'Description':
					// 	if($th->next_sibling() != null){
					// 		$data['description'] = trim($th->next_sibling()->plaintext);
					// 	}
					// 	break;
					case 'Manufacturer Standard Lead Time':
						if($th->next_sibling() != null){
							$data['order_time'] = trim($th->next_sibling()->plaintext);
						}
						break;
					case 'Detailed Description':
						if($th->next_sibling() != null){
							$data['description'] = trim($th->next_sibling()->plaintext);
						}
						break;
				}
			}
		}
		
		$document = $html->find('.product-details-documents-media table', 0);
		if($document){
			$data['content'] = $data['en_content'] = $document->outertext; // data crawl tài liệu
		}
		$properties = $html->find('#product-attribute-table', 0);
		$data['content2'] = '';
		$data['en_content2'] = '';
		if($properties){
			$properties = str_get_html($properties->outertext);
			$removeTr = $properties->find('tr');
			if($removeTr){
				foreach ($removeTr as $key => $value) {
					if($key <= 4 && $key != 3){ // xóa 4 dòng thuộc tính đầu tiên vì tự nhập những thuộc tính đó
						$value->outertext = '';
						$value->innertext = '';
					}
				}
			}
			$removeThisElement = $properties->find('.attributes-td-checkbox');
			if($removeThisElement){
				foreach($removeThisElement as $item){
					$item->outertext = '';
					$item->innertext = '';
				}
			}
			$properties->load($properties->save());
			$tt = $properties->find('tr');
			if($tt){
				foreach($tt as $k => $v){
					$th = $v->find('th', 0);
					$td = $v->find('td', 0);
					if($th){
						$name = trim($th->plaintext);
						if($td){
							$content = $td->plaintext;
						}
						else $content = '';
						$content = trim($content);
						$id = \DB::table('product_properties')->insertGetId([
							'name' => $name,
							'en_name' => $name,
							'act' => 1,
							'created_at' => new \DateTime(),
							'updated_at' => new \DateTime()
						]);
						$property_id = \DB::table('properties')->insertGetId([
							'property_id' => $id,
							'name' => $name,
							'en_name' => $name,
							'content' => $content,
							'en_content' => $content,
							'created_at' => new \DateTime(),
							'updated_at' => new \DateTime()
						]);
						$data['content2'] .= $property_id.',';
						// $data['en_content2'] .= $id.',';
					}
				}
			}
		}
		$data['content2'] = trim($data['content2']); // data thuộc tính sp crawl
		// $data['en_content2'] = trim($data['en_content2']); // data thuộc tính sp crawl
		
		$data['img'] = '';
		$linkImg = $html->find('.product-photo-large', 0);
		if($linkImg){
			$linkImg = str_replace('//', 'https://', $linkImg->getAttribute('href'));
			$extImg = substr($linkImg, strrpos($linkImg, '.'));
			$imgs = $this->curl($linkImg);
			if(!isset($imgs['err'])){
				$medisIns['name'] = time().$extImg;
				$imgPath = 'public/uploads/';
				file_put_contents($imgPath.$medisIns['name'], $imgs['response']);
				file_put_contents($imgPath.'thumbs/def/'.$medisIns['name'], $imgs['response']);
				$medisIns['created_at'] = new \Datetime();
				$medisIns['parent'] = 0;
				$medisIns['is_file'] = 1;
				$medisIns['path'] = $imgPath;
				$medisIns['file_name'] = $medisIns['name'];
				$medisIns['extra'] = $this->fileExtra('public/uploads/'.$medisIns['name'], $extImg, $medisIns['name']);
				$medisIns['updated_at'] = new \DateTime();
				$medisIns['trash'] = 0;
				$imgIns = new \stdClass();
				$imgIns->id = \DB::table('media')->insertGetId($medisIns);
				$imgIns->name = $medisIns['name'];
				$imgIns->title = $medisIns['name'];
				$imgIns->caption = $medisIns['name'];
				$imgIns->alt = $medisIns['name'];
				$imgIns->description = $medisIns['name'];
				$imgIns->created_at = new \DateTime();
				$imgIns->is_file = 1;
				$imgIns->parent = 0;
				$imgIns->path = $imgPath;
				$imgIns->file_name = $medisIns['name'];
				$imgIns->extra = $medisIns['extra'];
				$imgIns->updated_at = new \DateTime();
				$imgIns->trash = 0;
				$data['img'] = json_encode($imgIns);
			}
		}
		$numberAddLink = $this->addNumberUrlDuplicate($data['slug'], $data['slug'], 'v_routes', $i = 1);
		$data['slug'] = $numberAddLink > 1 ? $data['slug'].'-'.$numberAddLink : $data['slug'];
		$productId = \DB::table('products')->insertGetId($data);
		// insert v_routes
		$routesIns['name'] = $data['name'];
		$routesIns['en_name'] = $data['en_name'];
		$routesIns['controller'] = 'products.view';
		$routesIns['link'] = $data['slug'];
		$routesIns['table_map'] = 'products';
		$routesIns['tag_id'] = $productId;
		$routesIns['created_at'] = new \DateTime();
		$routesIns['updated_at'] = new \DateTime();
		$routesIns['s_title'] = $data['name'];
		$routesIns['s_des'] = $data['name'];
		$routesIns['s_key'] = $data['name'];
		$routesIns['is_static'] = 0;
		$routesIns['en_s_title'] = $data['name'];
		$routesIns['en_s_des'] = $data['name'];
		$routesIns['en_s_key'] = $data['name'];
		\DB::table('v_routes')->insert($routesIns);
		
		return redirect()->back()->with('typeNotify', 'success')->with('messageNotify', 'Lấy thông tin sản phẩm thành công');
	}
	public function fileExtra($filePath, $ext, $fileName)
    {
        $obj = new \stdClass();
        $obj->extension = $ext;
        $obj->size = \File::size($filePath);
        $obj->isfile = 1;
        $obj->dir = $filePath;
        $obj->width = getimagesize($filePath)[0];
        $obj->height = getimagesize($filePath)[1];
        $obj->thumb = 'public/uploads/thumbs/def/'.$fileName;
        return json_encode($obj);
    }
	function curl($url){
		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1
		));
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$err = curl_error($curl); // return string or ''
		curl_close($curl);
		if ($err) {
			return ['status' => $status, 'err' => $err];
		} else {
			return ['status' => $status, 'response' => $response];
		}
	}
	function addNumberUrlDuplicate($linkRoot, $link, $table, $i = 1){
		$results = \DB::table('v_routes')->where('link', $link)->first();
		if(!$results) return $i;
		$newLink = $linkRoot.'-'.++$i;
		return $this->addNumberUrlDuplicate($linkRoot, $newLink, $table, $i);
	}
	function addProperty(){
		$post = \Request::input();
		$obj = new \stdClass();
		if(empty($post['json'])){
			$obj->code = 100;
			$obj->message = 'Bạn chưa tích chọn thuộc tính nào';
			return response()->json($obj);
		}
		$data = json_decode($post['json'], true);
		if($data == null){
			$obj->code = 120;
			$obj->message = 'Bạn chưa tích chọn thuộc tính nào';
			return response()->json($obj);
		}
		$ids = '';
		foreach($data as $k => $v){
			$dataIns['property_id'] = $v['property_id'];
			$dataIns['name'] = $v['property_name'];
			$dataIns['en_name'] = $v['property_name_en'];
			$dataIns['content'] = $v['property_content'];
			$dataIns['en_content'] = $v['property_content_en'];
			$dataIns['created_at'] = new \DateTime();
			$dataIns['updated_at'] = new \DateTime();
			$id = \DB::table('properties')->insertGetId($dataIns);
			$ids .= ($ids != '' ? ',' : '').$id;
		}
		$obj->code = 200;
		$obj->message = 'Thêm thuộc tính thành công';
		$obj->id = $ids;
		return response()->json($obj);
	}
	function updateStatusOrderProduct(){
		$inputs = \Request::input();
		if(count($inputs) == 0){
			echo $this->echoJsonData(100, 'Thiếu thông tin dữ liệu');
			return;
		}
		$proId = $inputs['pro_id'];
		$orderId = $inputs['order_id'];
		$status = $inputs['status'];
		$orders = \DB::table('orders')->where('id', $orderId)->get();
		$details = json_decode($orders[0]->detail, true);
		if(count($details) == 0){
			echo $this->echoJsonData(110, 'Không có sản phẩm nào trong đơn hàng này');
			return;
		}
		foreach($details as $k => $v){
			if($v['id'] == $proId){
				$details[$k]['status'] = $status;
			}
		}
		\DB::table('orders')->where('id', $orderId)->update(['detail' => json_encode($details)]);
		echo $this->echoJsonData(200, 'Cập nhật trạng thái thành công');
	}
	function echoJsonData($code, $message, $data = ''){
		$obj = new \stdClass();
		$obj->code= $code;
		$obj->message = $message;
		$obj->data= $data;
		return json_encode($obj);
	}
	function exportPdfOrderDetail($orderId = false){
		if(!$orderId){
			echo $this->echoJsonData(10, 'Thiếu thông tin id đơn hàng');
			return;
		}
		$orders = \DB::table('orders')->where('id', $orderId)->get();
		if(count($orders) == 0){
			echo $this->echoJsonData(20, 'Không tìm thấy thông tin đơn hàng');
			return;
		}
		$pdf = PDF::loadView('vh::pdf_view', ['dataItem' => $orders[0]]);
    	$content = $pdf->download()->getOriginalContent();
    	file_put_contents('public/pdfs/order-'.$orderId.'.pdf', $content);
    	try{
    		\App\Helpers\Icgidi::sendMailToUserAttachments($orders[0]->cmail, 'ICDIGI - Gửi quý khách hàng file PDF thông tin đơn hàng đã đặt trên '.url('/'), 'Kính chào quý khách. Cảm ơn quý khách đã tin tưởng và đặt hàng trên icdigi.com<br>Chúng tôi xin gửi quý khách thông tin đơn hàng ở file PDF đính kèm.<br>Cảm ơn quý khách.', 'public/pdfs/order-'.$orderId.'.pdf');
    	}
    	catch(Exeption $ex){}
    	\DB::table('orders')->where('id', $orderId)->update(['send_mail' => 1]);
        echo $this->echoJsonData(200, 'Gửi thành công');
	}
	public function synQuantityFromKiot(){
		$cronStatus = file_get_contents('cron_status.txt');
		if($cronStatus == 1) return;
		file_put_contents('cron_status.txt', 1);
		synKiotViet::synQuantityFromKiot();
		file_put_contents('cron_status.txt', 0);
	}
	public function synQuantityToKiot($products, $type){
		synKiotViet::synQuantityToKiot($products, $type);
	}
	function testdom($orderId = 65){
		$orders = \DB::table('orders')->where('id', $orderId)->get();
		$pdf = PDF::loadView('vh::pdf_view', ['dataItem' => $orders[0]]);
    	$content = $pdf->download()->getOriginalContent();
    	file_put_contents('public/pdfs/order-'.$orderId.'.pdf', $content);
	}
	function testdom2(){
		echo '<pre>'; var_dump(__LINE__);die(); echo '</pre>';
	}
	function testPdf(){
		$pdf = PDF::loadHTML('<h1>Thanh Niên An</h1>');
    	return $pdf->stream();
	}
	function searchProductModal(Request $request){
		$saleName = $request->sale_name;
		$saleId = $request->sale_id;
		$saleInstance = null;
		/*item mà user đã chọn trước đó*/
		$chooseStorage = json_decode($request->choose_storage, true);
		$chooseStorage = !is_array($chooseStorage) ? [] : $chooseStorage;
		switch ($saleName) {
			case 'deal':
				$saleInstance = Deal::where('id', $saleId)->first();
				break;
			case 'combo':
				# code...
				break;
			case 'promotion':
				# code...
				break;
			case 'flash_sale':
				# code...
				break;
		}
		if ($saleInstance == null) {
			return \Support::json(['code' => 100, 'message' => 'Không tồn tại loại khuyến mãi']);
		}
		$categoryId = $request->category;
		$type = $request->type;
		$keyword = $request->keyword;
		$productChooses = Product::whereDoesntHave('flashSale', function($q) use($saleInstance){
    		$q->where('start_at', '<=', $saleInstance->expired_at)->where('expired_at', '>=', $saleInstance->start_at);
    	})
    	->whereDoesntHave('deal', function($q) use($saleInstance){ // sp deal chính
    		$q->where('start_at', '<=', $saleInstance->expired_at)->where('expired_at', '>=', $saleInstance->start_at);
    		if ($saleInstance instanceof Deal) {
    			$q->where('deals.id', $saleInstance->id);
    		}
    	})
    	->whereDoesntHave('getDealSub', function($q) use($saleInstance){ // sp deal đi kèm
    		$q->where('start_at', '<=', $saleInstance->expired_at)->where('expired_at', '>=', $saleInstance->start_at);
    		if ($saleInstance instanceof Deal) {
    			$q->where('deals.id', $saleInstance->id);
    		}
    	})
    	->whereDoesntHave('combo', function($q) use($saleInstance){ // sp deal chính
    		$q->where('start_at', '<=', $saleInstance->expired_at)->where('expired_at', '>=', $saleInstance->start_at);
    		if ($saleInstance instanceof Combo) {
    			$q->where('combos.id', $saleInstance->id);
    		}
    	})
    	->where(function($q) use($categoryId, $type, $keyword){
    		if ($categoryId != null) {
    			$q->whereHas('category', function($q2) use($categoryId){
    				$q2->where('id', $categoryId);
    			});
    		}
    		if ($type == 1 && $keyword != null && trim($keyword) != '') {
    			$q->where('name', 'like', '%'.$keyword.'%');
    		}
    		elseif($type == 2 && $keyword != null && trim($keyword) != ''){
    			$q->where('code', 'like', '%'.$keyword.'%');
    		}
    	})
    	->orderBy('id', 'desc')
    	->get();
    	return \Support::json([
    		'code' => 200,
    		'html' => view('vh::view.deal.item_product', compact('productChooses', 'chooseStorage'))->render(),
    	]);
	}
	public function chooseProductModal(Request $request)
	{
		$chooses = $request->input('check-single-product');
		$sale_name = $request->sale_name;
		$sale_id = $request->sale_id;
		if (count($chooses) == 0) {
			return \Support::json(['code' => 100, 'message' => 'Chọn ít nhất 1 sản phẩm']);
		}
		switch ($sale_name) {
			case 'deal':
				$result = $this->connectDealToProductMain($chooses, $sale_id);
				break;
			case 'deal-sub':
				$result = $this->connectDealToProductSub($chooses, $sale_id);
				break;
			case 'combo':
				$result = $this->connectComboToProduct($chooses, $sale_id);
				break;
			case 'promotion':
				$result = $this->connectPromotionToProduct($chooses, $sale_id);
				break;
			case 'flash_sale':
				$result = $this->connectFlashSaleToProduct($chooses, $sale_id);
				break;
		}
		return $result;
	}
}
?>