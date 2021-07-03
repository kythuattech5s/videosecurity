<?php 

namespace vanhenry\manager\controller;

use vanhenry\manager\model\HGroupUser;

use vanhenry\manager\model\HGroupModule;

use vanhenry\manager\model\HRole;

trait EditPermissionTrait{

	private function _editpermissiontrait_fetchGroupUser($id){

		$arrs = HGroupUser::select("id")->where("parent",$id)->where("act",1)->get();

		$ret  = array();

		array_push($ret, $id);

		foreach ($arrs as $key => $value) {

			$x = $this->_editpermissiontrait_fetchGroupUser($value["id"]);

			$ret = array_merge($ret,$x);

		}

		return $ret;

	}

	private function _editpermissiontrait_getAllGroupUser(){

		$groupUserId = \Auth::guard("h_users")->user()->group;

		$t = $this->_editpermissiontrait_fetchGroupUser($groupUserId);

		$groupUsers = HGroupUser::select("id","name")->where("act",1)->whereIn("id",$t)->get();

		return $groupUsers;

	}

	private function _editpermissiontrait_getAllGroupModule(){

		$groupUserId = \Auth::guard("h_users")->user()->group;

		return HGroupModule::where("parent",0)->where("act",1)->get();



	}

	public function edit_permis($table,$tableData,$id){



		$tableData = collect($tableData);

		$groupUserId = \Auth::guard("h_users")->user()->group;

		$groupUsers= $this->_editpermissiontrait_getAllGroupUser();

		$groupModules= $this->_editpermissiontrait_getAllGroupModule();

		

		if(request()->isMethod("post")){

			$inputs = request()->input();

			$groupUserSelected = (int)$inputs["group_user"];

		}

		else{

			$groupUserSelected = $groupUserId;

		}

		$tmpChecked = \DB::select("select a.id from h_modules a, h_roles b where b.group_user_id= :gid and a.parent = b.group_module_id and (b.role & a.code )>0 group by a.id",["gid"=>$groupUserSelected]);

		$arrChecked = array();

		foreach ($tmpChecked as $key => $value) {

			array_push($arrChecked, $value->id);

		}

		return view('vh::edit.view_permis',compact("tableData","groupUsers","groupModules","groupUserId","arrChecked","groupUserSelected"));

	}

	public function do_assign($table){

		if(request()->isMethod("post")){

			$inputs = request()->input();

			$code = isset($inputs["code"])?$inputs["code"]:"[]";

			$group_user = (int)(isset($inputs["group_user"])?$inputs["group_user"]:0);

			$currentGroupUserId = \Auth::guard("h_users")->user()->group;

			$roles = HRole::select("group_user_id","group_module_id","role")->where("group_user_id",$currentGroupUserId)->get();

			$keyRoles = $roles->keyBy("group_module_id");

			//check group user

			if($group_user == -1){

				return redirect($this->admincp."/edit/".$table."/0")->with("errors","Vui lòng chọn nhóm người dùng!");	

			}

			if($currentGroupUserId === $group_user)

			{

				return redirect($this->admincp."/edit/".$table."/0")->with("errors","Bạn không thể phân quyền cho chính mình!");

			}

			$t = $this->_editpermissiontrait_fetchGroupUser($currentGroupUserId);

			if(!in_array($group_user, $t)){

				return redirect($this->admincp."/edit/".$table."/0")->with("errors","Bạn không thể thực hiện hành động này!");	

			}

			echo "<pre>";



			// check quyen

			$code = json_decode($code,true);

			$ins = array();

			foreach ($code as $key => $value) {

				$gid = (int)$value["groupid"];

				$role = (int)$value["code"];

				$dbrole = @($keyRoles->get($gid))?$keyRoles->get($gid)->role:0;

				$role = $role>$dbrole?$dbrole:$role;

				$tmp = array("group_user_id"=>$group_user,"group_module_id"=>$gid,"role"=>$role);

				array_push($ins, $tmp);

			}

			\DB::table($table)->where("group_user_id",$group_user)->delete();

			$ret = \DB::table($table)->insert($ins);

			if($ret){

				\Event::dispatch('vanhenry.manager.doassign.success', array($table,$group_user));

				return redirect($this->admincp."/edit/".$table."/0")->with("errors","Cập nhật thành công!");	

			}

			else{

				return redirect($this->admincp."/edit/".$table."/0")->with("errors","Cập nhật thất bại!");		

			}

		}

	}

}