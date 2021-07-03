<?php 
namespace vanhenry\slug;
use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Http\Controllers\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Cache as Cache;
use DB;
use vanhenry\helper\StringHelper as StringHelper;
use vanhenry\slug\models\VDetailTable;
use vanhenry\slug\models\VTable;
use \Carbon\Carbon;
class HController extends BaseController
{
    //use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    protected static $listVConfigs=array();
    public function __construct(){
        parent::__construct();
        $this->initVConfigs();
    }
    protected function initVConfigs(){
    	if(count(static::$listVConfigs) ==0 ){
    		if(!Cache::has('hcontroller_v_config')){
    			$tmp = DB::table('v_configs')->where('act',1)->get();
    			$out= array();
    			foreach ($tmp as $key => $value) {
    				if(array_key_exists('name', $value) && array_key_exists('value', $value)){
    					$out[$value->name] = $value->value;	
    				}
    			}
    			Cache::put('hcontroller_v_config',$out,60);
    		}
    		static::$listVConfigs = Cache::get('hcontroller_v_config',array());
    	}
    }
    protected function getRoutesItem($slug){
    	$listRoutes = DB::table('v_routes')->where('link',$slug)->get();
    	if(count($listRoutes)>0){
    		return $listRoutes[0];
    	}
    	return 0;
    }
    public function slugOneLevel($lv1){
        $slug = request()->segment(1);
        $itemRoutes = $this->getRoutesItem($slug);
        if($itemRoutes===0){
          abort(404);
      }
      else{
        if($itemRoutes->is_static==1){
            $controller = $itemRoutes->controller;
            $controller = explode("@", $controller);
            if(count($controller)>1){
                $className = $controller[0];
                $fnc = $controller[1];
                $obj = new $className();
                return $obj->$fnc($itemRoutes);
            }
        }
        else{
            $listInfoColumn = $this->getInfoColumn($itemRoutes->table_map);
            $arrFields = $this->getAllKeySelect($listInfoColumn);
            $itemTable = $this->getInfoTable($itemRoutes->table_map);
            $currentItem = $this->getDetailTable($itemRoutes->table_map,$arrFields,$itemRoutes->tag_id);
            $this->updateCountViewTable($arrFields,$currentItem);
            if($itemTable->is_exception){
                $controller = $itemRoutes->controller;
                $controller = explode("@", $controller);
                if(count($controller)>1){
                    $className = $controller[0];
                    $fnc = $controller[1];
                    $obj = new $className();
                    return $obj->$fnc($itemRoutes,$currentItem,$itemTable);
                }
            }
            else{
                if($itemTable->is_category){
                    $className = "\App\Http\Controllers\\".studly_case($itemTable->table_map)."Controller";
                    $subget = false;
                    if(class_exists($className)){
                        $subthis = unserialize(
                            sprintf(
                                'O:%d:"%s"%s',
                                strlen($className),
                                $className,
                                strstr(strstr(serialize($this), '"'), ':')
                            )
                        );
                        $subget =  $subthis->needGetListItems($currentItem);
                        if($subget){
                            $fnc =  $itemTable->table_map."_slugOneLevel";
                            $listItems = $subthis->$fnc($currentItem,$itemTable);                                
                        }
                    }
                    if(!$subget){
                        $listItems = $this->getAllChilds($currentItem->id,$itemTable);    
                    }
                    $data =array("itemRoutes"=>$itemRoutes,"listInfoColumn"=>$listInfoColumn,"itemTable"=>$itemTable,"currentItem"=>$currentItem,"listItems"=>$listItems);    
                }
                else{
                 $data =array("itemRoutes"=>$itemRoutes,"listInfoColumn"=>$listInfoColumn,"itemTable"=>$itemTable,"currentItem"=>$currentItem);  
             }
            return view($this->theme.$itemRoutes->controller,$data);
        }
    }
}
}	
protected function updateCountViewTable($arrFields,$currentItem){
    if($currentItem instanceof \Illuminate\Database\Eloquent\Model){
        if(in_array("count", $arrFields)){
            $currentItem->count+=1;
            $currentItem->save();
        }
    }
}
protected function hasCustomGetListItems(){
    return false;
}
public function slugTwoLevel($lv1,$lv2){
   $configLink = array_get(static::$listVConfigs,"C_LINK",1);
   if($configLink==1){
      abort(404);
  }
  else{
      $this->slugOneLevel($lv2);
  }
}
protected function getAllChilds($id,$itemTable){
    $tableChild = $itemTable->table_child;
    $recordPerPageFrontend = $itemTable->rpp_frontend;
    $recordPerPageFrontend = isset($recordPerPageFrontend)?$recordPerPageFrontend:10;
    $listInfoChilds = $this->getInfoColumn($tableChild,array('name'=>'parent'));
    if(count($listInfoChilds)>0){
        $type = strtolower(trim($listInfoChilds[0]->type_show));
        
        // if(strpos($type, "multi_select")===0){
        if(strpos($type, "multiselect")===0){
            $params = request()->input();
            $q = DB::table($tableChild)->where(array('act'=>1))->whereRaw("(trash <> 1 or trash is null)")->whereRaw("find_in_set(?,parent)>0",array($id));
            if(isset($params['ord'])){
                if($params['ord']=="newest"){
                    $q = $q->orderBy("id","desc");
                }
                else if($params["ord"]=="oldest")
                {
                    $q = $q->orderBy("id","asc");   
                }
                else if($params['ord']=="mostview"){
                    $q = $q->orderBy("count","desc");
                }
                else{
                    $q=$q->orderBy('ord','asc')->orderBy('id','desc');
                }
            }
            else{
                $q=$q->orderBy('ord','asc')->orderBy('id','desc');
            }
            return $q->paginate($recordPerPageFrontend)->appends(request()->except(['page','_token']));;
        }
        else if(strpos($type, "select")===0){
            $q=DB::table($tableChild)->where(array('act'=>1,'parent'=>$id))->whereRaw("(trash <> 1 or trash is null)");
             $params = request()->input();
            if(isset($params['ord'])){
                if($params['ord']=="newest"){
                    $q = $q->orderBy("id","desc");
                }
                else if($params["ord"]=="oldest")
                {
                    $q = $q->orderBy("id","asc");   
                }
                else if($params['ord']=="mostview"){
                    $q = $q->orderBy("count","desc");
                }
                else{
                    $q=$q->orderBy('ord','asc')->orderBy('id','desc');
                }
            }
            else{
                $q=$q->orderBy('ord','asc')->orderBy('id','desc');
            }
            return $q->paginate($recordPerPageFrontend)->appends(request()->except(['page','_token']));;
        }
    }
    return array();
}
protected function getAllKeySelect($listInfoColumn){
    $str = array();
    $total = count($listInfoColumn);
    for($i=0;$i< $total;$i++){
        $item = $listInfoColumn[$i];
        array_push($str, $item->name);
    }
    return $str;
}
protected function getInfoTable($table,$where =""){
    $defaultWhere = array('table_map'=>$table,'act'=>1);
    if(is_array($where)){
        $defaultWhere = array_merge($defaultWhere,$where);
    }
    $listInfoColumn  = VTable::where($defaultWhere)->remember(1440)->orderBy('ord','ASC')->get();
    if(count($listInfoColumn)>0){
        return $listInfoColumn[0];
    }
    else{
        abort(404);
    }
}
protected function getInfoColumn($table,$where=""){
    $defaultWhere = array('parent_name'=>$table,'act'=>1);
    if(is_array($where)){
        $defaultWhere = array_merge($defaultWhere,$where);
    }
    $listInfoColumn  = VDetailTable::where($defaultWhere)->remember(1440)->orderBy('ord','ASC')->get();
    if(count($listInfoColumn)>0){
        return $listInfoColumn;
    }
    else{
        abort(404);
    }
}
protected function getDetailTable($table,$strKey,$id){
    $selects = [];
    foreach ($strKey as $k => $key) {
        if(strpos($key, '_out_')!==0){
            array_push($selects, $key);
        }
    }
    $m = \vanhenry\manager\GlobalHelper::getModel($table);
    if($m===FALSE){
        $listItems = DB::table($table)->select($selects)->where(array('act'=>1,'id'=>$id))->whereRaw("(trash <> 1 or trash is null)")->get($strKey);
        if(count($listItems)>0){
            return $listItems[0];
        }
        else{
            abort(404);
        }    
    }
    else{
        $item = $m->select($selects)->where(array('act'=>1,'id'=>$id))->whereRaw("(trash <> 1 or trash is null)")->get()->first();
        if(isset($item)){
            return $item;
        }
        else{
            abort(404);
        }
    }
}
}
?>