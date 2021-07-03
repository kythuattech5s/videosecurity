<?php 
namespace vanhenry\manager\controller;
use vanhenry\helpers\helpers\StringHelper as StringHelper;
use Illuminate\Support\Facades\Cache as Cache;
use DB;
use Carbon\Carbon;
use vanhenry\helpers\CT as CT;
use Illuminate\Database\Eloquent\Collection as Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
trait SearchTrait{
	/**
	 * Tìm kiếm dữ liệu từ các giá trị form submit
	 * @param  Request $request
	 * @param  [type] $table
	 * @return [type]
	 */
	public function search(Request $request, $table){
		$tableData = static::__getListTable()[$table];
		$options['limit'] = $tableData->rpp_admin;
		$options['orderkey']="id";
		$options['ordervalue']="desc";
		$inputs = $request->input();
		$inputs = array_replace($options,$inputs);
		$clInputs = collect($inputs);
		$arrSearchMore = $this->filterKey($clInputs,'search')->toArray();
		$arrSearchRaw = $this->filterKey($clInputs,'raw')->toArray();
		$arrSearchControl = $this->filterKey($clInputs,'type')->toArray();
		$arrCondition = $this->getSearchArray($inputs,$arrSearchMore,$arrSearchControl);
		$arrConditionRaw = $this->getSearchArrayRaw($arrSearchRaw);
		$q = $this->makeQuery($table,$inputs,$arrConditionRaw,$arrCondition);
		$tableDetailData = self::__getListDetailTable($table);
		$data['tableData'] = new Collection($tableData);
		$data['dataSearch'] = $inputs;
		$data['dataReuse'] = $this->dataReuse($inputs);
		$data['tableDetailData'] =  new Collection($tableDetailData);
		$data['listData'] = $this->getDataTable($q,$data['tableDetailData'],$inputs['limit'],$table);
		if(!$request->isMethod('post')){
			if(array_key_exists("trash", $inputs)){
				return view('vh::view.viewtrash',$data);		
			}
			else{
				return view('vh::view.view_normal',$data);		
			}
		}
		else{
			return view('vh::view.view_normal',$data);				
		}
		
	}
	/**
	 * Lọc Array dựa vào key của mảng bắt đầu bằng kí tự $key
	 * @param  Array $clArray Mảng gốc
	 * @param  String $key     Từ khóa
	 * @return Array          Mảng đã lọc
	 */
	public function filterKey($clArray,$key){
		$ret = $clArray->filter(function ($v,$k) use ($key) {
				return \Str::startsWith($k, $key);
			});
		return $ret;
	}
	/**
	 * Gộp danh sách mảng post dữ liệu => Array điều kiện
	 * @param  Array $def     Mảng post mặc định
	 * @param  Array $more    Mảng search-xxx
	 * @param  Array $control Mảng type-xxx
	 * @param  Array $pivot   Mảng pivot-xxx
	 * @return Array          Mảng [id,1,absolute,PRIMARY_KEY]
	 */
	private function getSearchArray($def,$more,$control){
		$ret = array();
		foreach ($control as $key => $value) {
			
			$_key = substr($key, 5);
			$ctl = $control['type-'.$_key];
			$tmp= array();
			if(StringHelper::normal($ctl)=="datetime"){
				$tmp = array('key'=>$_key,'value'=>$def['from-'.$_key],'from'=>$def['from-'.$_key],'to'=>$def['to-'.$_key],'type_search'=>$more["search-".$_key],'control'=>$ctl);
			}
			else{
				$tmp = array('key'=>$_key,'value'=>$def[$_key],'type_search'=>$more["search-".$_key],'control'=>$ctl);
			}
			array_push($ret, $tmp);
		}
		return $ret;
	}
	private function getSearchArrayRaw($raw){
		$ret = array();
		foreach ($raw as $key => $value) {
			$_key = substr($key,4);
			$ret[$_key] = $value;
		}
		return $ret;
	}
	/**
	 * Tạo câu truy vấn dữ liệu từ mảng đầu vào
	 * @param  String $table Tên bảng
	 * @param  Array $raw   Giá trị ô tìm kiếm
	 * @param  Array $more  Array lấy từ hàm getSearchArray
	 * @return Query        Thực hiện truy vấn
	 */
	private function makeQuery($table,$inputs,$raw,$more){
		$q = DB::table($table);
		if(is_array($raw)){
			foreach ($raw as $key => $value) {
				$q = $q->where($key,'like',"%".$value."%");
			}
		}
		if(is_array($more)){
			foreach ($more as $key => $value) {
				$tcf = $value["control"]=="SELECT"?"TEXT":$value["control"];
				$fnc = 'catchTypeWhere'.$tcf;
				if(method_exists($this, $fnc)){
					$q = $this->$fnc($q,$value,$table);	
				}
				else{
					$fnc = 'catchTypeWhereBASE';
					$q = $this->$fnc($q,$value,$table);	
				}
				
			}
		}
		$q->orderBy($inputs['orderkey'],$inputs['ordervalue']);
		return $q;
	}
	private function catchTypeWhereTEXT($query,$value){
		switch ($value['type_search']) {
			default:
			case 'absolute':
				$query = $this->catchTypeWhereBASE($query,$value);
				break;
			case 'relative':
				$query = $query->where($value['key'],"like","%".$value['value']."%");
				$query = $query ->orWhere(function ($query) use ($value) {
	                $query->where($value['key'],"like","%".htmlentities($value['value'])."%");
	            });
				break;
		}
		return $query;
	}
	private function catchTypeWhereBASE($query,$value){
		$query = $query->where($value['key'],$value['value']);
		return $query;
	}
	private function catchTypeWhereEDITOR($query,$value){
		$query = $query->where($value['key'],"like","%".$value['value']."%");
		$query = $query ->orWhere(function ($query) use ($value){
	                $query->where($value['key'],"like","%".htmlentities($value['value'])."%");
	            });
		return $query;
	}
	private function catchTypeWhereTEXTAREA($query,$value){
		$query = $query->where($value['key'],"like","%".$value['value']."%");
		$query = $query ->orWhere(function ($query) use ($value) {
	                $query->where($value['key'],"like","%".htmlentities($value['value'])."%");
	            });
		return $query;
	}
	private function catchTypeWhereSELECT($query,$value){
		$query = $query->whereRaw('FIND_IN_SET('.$value['value'].','.$value['key'].') > 0');
		return $query;
	}
	private function catchTypeWhereDATETIME($query,$value,$table){
		$from  = \DateTime::createFromFormat('Y-m-d H:i:s', $value['from']);
		$to  = \DateTime::createFromFormat('Y-m-d H:i:s', $value['to']);
		$query = $query->whereBetween($table.'.'.$value['key'],[$from,$to]);
		return $query;
	}
	private function catchTypeWherePIVOT($query,$value,$table){
		$infoPivot = \DB::table('v_detail_tables')->where(['name' => $value['key'], 'parent_name' => $table])->first();
		$defaultData = json_decode($infoPivot->default_data, true);
		if (!is_array($defaultData)) {
			return $query;
		}
		$pivot_table = $defaultData['pivot_table'];
		$origin_field = $defaultData['origin_field'];
		$target_table = $defaultData['target_table'];
		$target_field = $defaultData['target_field'];
		$query->join($pivot_table, "$table.id", '=', "$pivot_table.$origin_field")->where("$pivot_table.$target_field", $value['value']);
		return $query;
	}
	private function dataReuse($inputs)
	{
		$keySearchs = \Arr::where($inputs, function($v, $k){
			return \Str::before($k, '-') == 'type';
		});
		$fieldWheres = [];
		foreach ($keySearchs as $key => $value) {
			$fieldWheres[str_replace('type-', '', $key)] = $value;
		}
		$inputHiddens = '';
		foreach ($fieldWheres as $k => $v) {
			$inputHiddens .= '<input name="'.('search-'.$k).'" type="hidden" value="'.$inputs['search-'.$k].'">';
			$inputHiddens .= '<input name="'.('type-'.$k).'" type="hidden" value="'.$inputs['type-'.$k].'">';
			if ($v == 'DATETIME') {
				$inputHiddens .= '<input name="'.('from-'.$k).'" type="hidden" value="'.$inputs['from-'.$k].'">';
				$inputHiddens .= '<input name="'.('to-'.$k).'" type="hidden" value="'.$inputs['to-'.$k].'">';
			}
			else{
				$inputHiddens .= '<input name="'.$k.'" type="hidden" value="'.$inputs[$k].'">';
			}
		}
		return $inputHiddens;
	}
}
?>