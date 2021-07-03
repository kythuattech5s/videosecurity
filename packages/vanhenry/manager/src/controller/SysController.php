<?php
namespace vanhenry\manager\controller;
use DB;
use Illuminate\Http\Request;
class SysController extends BaseAdminController
{
    public function onOffMenu(){
        $get = request()->input();
        $status = isset($get['status'])?$get['status']:'off';
        \Session::put("menu_status",$status);
    }
    public function inserttable($not = 0){
    	$listTables = DB::select( DB::raw("select table_name name, (case when TABLE_COMMENT <='' then table_name else TABLE_COMMENT end) cmt from information_schema.tables where TABLE_SCHEMA = database()"));
    	$listTableNotInsert = DB::select(DB::raw("select table_name name, (case when TABLE_COMMENT <='' then table_name else TABLE_COMMENT end) cmt from information_schema.tables where TABLE_SCHEMA = database() and table_name COLLATE utf8_unicode_ci not in (select table_map from v_tables)"));
    	$listTableNotTranslations = [];
    	foreach ($listTables as $key => $value) {
    		if (\Str::afterLast($value->name, '_') != 'translations' && \Str::before($value->name, '_') != 'v' && \Str::before($value->name, '_') != 'h') {
    			$listTableNotTranslations[] = $value;
    		}
    	}
    	$listGroupModule = DB::table("h_group_modules")->where("parent",0)->where("act",1)->get();
    	$data["listTables"] = $listTables;
    	$data["listTableNotInsert"] = $listTableNotInsert;
    	$data["listTableNotTranslations"] = $listTableNotTranslations;
    	$data["listGroupModule"] = $listGroupModule;
    	$data["notify"] = $not;
        return view('vh::sys.insert_table',$data);
    }
    private function _sys_insertVTable($post){
    	$data["name"]= $post["name"];
		$data["table_map"]= $post["table_map"];
		$data["note"]= $post["note"];
		$data["table_parent"]= $post["table_parent"];
		$data["translation_of"]= $post["translation_of"];
		$data["table_child"]= $post["table_child"];
		$data["is_category"]= $post["is_category"];
		$data["controller"]= $post["controller"];
		$data["rpp_admin"]= $post["rpp_admin"];
		$data["rpp_frontend"]= $post["rpp_frontend"];
		$data["has_insert"]= $post["has_insert"];
		$data["has_update"]= $post["has_update"];
		$data["has_delete"]= $post["has_delete"];
		$data["has_copy"]= $post["has_copy"];
		$data["has_help"]= $post["has_help"];
		$data["has_quickpost"]= $post["has_quickpost"];
		$data["has_search"]= $post["has_search"];
		$data["type_show"]= $post["type_show"];
		$data["lang"]= "";
		$data["act"]= 1;
		$data["ord"]= time();
		$id = DB::table('v_tables')->insertGetId($data);
		return $id;
    }
    private function _sys_insertVDetailTable($idparent,$nameparent){
    	$listFields= DB::select(DB::raw("select column_name name,ordinal_position ord, is_nullable = 'YES' nullable,(case when column_comment is null or COLUMN_COMMENT = '' then COLUMN_NAME else COLUMN_COMMENT end) cmt,data_type type,(case when character_maximum_length is null or character_maximum_length >2000 then 0 else character_maximum_length end) len,column_key = 'PRI' prikey from information_schema.`COLUMNS` where table_name = '".$nameparent."' and TABLE_SCHEMA = database()"));
    	$arr = array();
    	foreach ($listFields as $key => $value) {
    		$tmp = array();
    		$value = (array)$value;
    		$tmp["name"] = $value["name"];
    		$tmp["require"] = $value["nullable"];
    		$tmp["length"] = $value["len"];
    		$tmp["parent"] = $idparent;
    		$tmp["parent_name"] = $nameparent;
    		$tmp["note"] = $value["cmt"];
    		$tmp["region"] = 1;
    		switch (strtolower($value["type"])) {
    			case 'int':
    				if ($value['name'] == 'parent') {
    					$typeshow = "SELECT";	
    					break;
    				}
    			case 'tinyint':
    				if($value["len"]==1 || $value['name'] == 'act'){
    					$typeshow = "CHECKBOX_BUTTON";
    					$tmp["region"] = 2;
    				}
    				else $typeshow = "TEXT";
    				break;
    			case "text":
    				if ($value['name'] == 'img' || strpos($value["name"], "img")!==FALSE ) {
    					$typeshow = "IMAGEV2";
    					$tmp["region"] = 2;
    				}
    				elseif($value['name'] == 'imgs'){
    					$typeshow = "GALLERY";	
    				}
    				elseif(strpos($value["name"], "content")!==FALSE ){
    					$typeshow = "EDITOR";
    				}
    				else{
    					$typeshow ="TEXTAREA";
    				}
    				break;
    			case "varchar":
    				if($value["name"]=="slug"){
    					$typeshow = "SLUG";
    				}
    				else $typeshow = "TEXT";
    				break;
    			case "datetime":
    				$typeshow = "DATETIME";
    				$tmp["region"] = 2;
    				break;
    			default:
    				if($value["prikey"]==1 && $value["name"]="id"){
		    			$typeshow = "PRIMARY_KEY";
		    		}
		    		else{
		    			$typeshow ="TEXT";	
		    		}
    				break;
    		}
    		$tmp["group"] = 3;
    		if (strpos($value['name'], 'seo_') === 0) {
    			$tmp["group"] = 4;
    		}
    		$tmp["type_show"] = $typeshow;
    		$tmp["created_at"] = date("Y-m-d H:i:s");
    		$tmp["updated_at"] = date("Y-m-d H:i:s");
    		$tmp["show"] = ($value["name"] == "name" || $value["name"] == "act")?1:0;
    		$tmp["editable"] = ($value["name"] == "name" || $value["name"] == "act")?1:0;
    		$tmp["simple_search"] = ($value["name"] == "name")?1:0;
    		$tmp["quickpost"] = 0;
    		$tmp["simple_sort"] = 0;
    		$tmp["advance_search"] = 0;
    		$tmp["help"]=1;
    		$tmp["ord"]=$value["ord"];
    		$tmp["act"]=1;
    		array_push($arr, $tmp);
    	}
    	return DB::table('v_detail_tables')->insert($arr);
    }
    private function _sys_insertGroupModule($post){
    	$data["name"] = $post["name"];
    	$data["note"] = $post["note"];
    	$data["link"] = "view/".$post["table_map"];
    	$data["parent"] = $post["group_module"];
    	$data["act"] = 1;
    	$data["created_at"] = date("Y-m-d H:i:s");
		$data["updated_at"] = date("Y-m-d H:i:s");
		$data["table_map"] = $post["table_map"];
		DB::table('h_group_modules')->insert($data);
    }
    public function doinserttable(Request $request){
    	if($request->isMethod('post')){
			$post = $request->post();
			$id = $this->_sys_insertVTable($post);
			$this->_sys_insertVDetailTable($id,$post["table_map"]);
			if ($post['translation_of'] == '') {
				$this->_sys_insertGroupModule($post);
			}
			return $this->inserttable($id>0?1:0);
		}
    }
    private function recurseRmdir($dir) {
        if(!file_exists($dir)) return;
      $files = scandir($dir);
      foreach ($files as $file) {
        if($file !="." && $file!=".."){
        (is_dir("$dir/$file")) ? $this->recurseRmdir("$dir/$file") : unlink("$dir/$file");
        }
      }
      rmdir($dir);
    }
    public function deleteCache(){
        // $dir = storage_path("framework/cache");
        // $this->recurseRmdir($dir);
        //  // \Artisan::call('cache:clear');
        \Cache::flush();
        return redirect()->back();
    }
    public function editRobot(){
        $file = base_path("robots.txt");
        if(request()->isMethod("post")){
            $content = request()->input("content","");
            file_put_contents($file, $content);
        }
        $content = "";
        if(file_exists($file)){
           $content = file_get_contents($file); 
        }
        return view("vh::other.robot",compact("content"));
    }
    public function editSitemap(){
        $listSitemaps = \DB::select("select created_at, `table` from v_routes where is_static <> 1 or is_static is null group by `table`");
        return view("vh::other.sitemap",compact("listSitemaps"));
    }
    public function updateSitemap(Request $request){
        $inputs = $request->input();
        $type = isset($inputs["type"])?$inputs["type"]:0 ;
        $table_map = isset($inputs["from"])?$inputs["from"]:0;
        $listSitemaps = \DB::select("select created_at, `table`, month(created_at)m,year(created_at) y from v_routes where is_static <> 1 or is_static is null group by month(created_at),year(created_at),`table`");
        if($type==1){
            $tmp = \DB::select("select created_at, `table`, month(created_at)m,year(created_at) y from v_routes where is_static <> 1 or is_static is null and `table`=:tb group by month(created_at),year(created_at),`table`",["tb"=>$table_map]);
            foreach ($tmp as $key => $sitemap) {
                $this->updateSitemapItem($sitemap->table,$sitemap->y,$sitemap->m);
            }
        }
        else if($type==2){
            $y = date("Y");
            $m = date("m");
            $m = strpos($m, "0")===0?substr($m, 1):$m;
            $this->updateSitemapItem($table_map,$y,$m);
        }
        else if($type==3){
            foreach ($listSitemaps as $key => $sitemap) {
                $this->updateSitemapItem($sitemap->table,$sitemap->y,$sitemap->m);
            }
            $this->updateSitemapStatic();
        }
        $html = \View::make('vh::more.template_sitemap', compact("listSitemaps"))->render();
        file_put_contents("sitemap.xml",$html);
        return redirect()->back()->with("status","Cập nhật thành công");
    }
    private function updateSitemapItem($table,$year,$month){
        $path = 'sitemap/'.$table;
        if(!file_exists($path)){
            mkdir($path);
        }
        $listItems = \DB::select("select vi_link, en_link, vi_name, en_name, created_at from v_routes WHERE `table` = :t and month(created_at) = :m and year(created_at) = :y",["t"=>$table,"y"=>$year,"m"=>$month]);
        $html = \View::make('vh::more.template_sitemap_item', compact("listItems"))->render();
        file_put_contents($path."/".$year."-".$month.".xml",$html);
    }
    private function updateSitemapStatic(){
        $listItems = \DB::select("select vi_link, en_link, vi_name, en_name, created_at from v_routes WHERE is_static=1 and in_sitemap = 1");
         $html = \View::make('vh::more.template_sitemap_item', compact("listItems"))->render();
          file_put_contents("sitemap/static.xml",$html);
    }
}