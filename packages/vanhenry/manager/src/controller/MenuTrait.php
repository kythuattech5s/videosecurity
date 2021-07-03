<?php 
namespace vanhenry\manager\controller;
trait MenuTrait{
	/**Menu**/
	public function edit_menu($table,$tableData,$id){
		$dataItem =DB::table($table)->where('id',$id)->get();
		if(count($dataItem)>0){
			$data['tableData'] = collect($tableData);
			$tableDetailData = collect(self::__getListDetailTable($table));
			$data['tableDetailData'] = DetailTableHelper::filterDataShow($tableDetailData);
			$data['dataItem'] = $dataItem[0];
			$data['subItem'] = DB::table($table)->where('parent',$id)->get();
			$data['actionType'] = 'edit';
			return view('vh::edit.view_menu',$data);
		}	
		else{
			return redirect($this->admincp.'/view/'.$table);
		}
	}
	public function getDataMenu(Request $request,$table){
		if($request->isMethod('post')){
			$post = $request->post();
			$request->session()->put('_vh_admin_search_menu_'.$table,$post);
		}
		$post = $request->session()->get('_vh_admin_search_menu_'.$table);
		$post = isset($post)?$post:array('keyword'=>'');
		$q = DB::table($table)->select('id','name','slug');
		if(isset($post['keyword']) && strlen(trim($post['keyword']))>0){
			$q=$q->where('name', 'like', '%'.$post['keyword'].'%');
		}
		$arr = $q->paginate(5);
		return response()->json($arr);
		
		return response()->json(array());
		
	}
	public function getStaticMenu(Request $request){
		
	}
	/**Menu**/
}
?>