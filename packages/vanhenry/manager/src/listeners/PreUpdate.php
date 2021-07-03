<?php namespace vanhenry\manager\listeners;
use App\ExamQuestion;
use vanhenry\manager\model\HGroupUser;
class PreUpdate{
    public function handle($table,$data,$_id){
    	if($table instanceof \vanhenry\manager\model\VTable){
    		$tblmap = $table->table_map;
    		if($tblmap=="h_users"){
                return $this->checkPermissionUpdateUser($data);
    		}
    	}
        return array("status"=>true);
    }
    private function checkPermissionUpdateUser($data){
        $groupUserId = \Auth::guard("h_users")->user()->group;
        $t = $this->fetchGroupUser($groupUserId);
        $gr = isset($data["group"])?$data["group"]:0;
        if(in_array($gr,$t)){
            return array("status"=>true);
        }
        else{
            return array("status"=>false,"code"=>150);
        }
    }
    private function fetchGroupUser($id){
        $arrs = HGroupUser::select("id")->where("parent",$id)->where("act",1)->get();
        $ret  = array();
        array_push($ret, $id);
        foreach ($arrs as $key => $value) {
            $x = $this->fetchGroupUser($value["id"]);
            $ret = array_merge($ret,$x);
        }
        return $ret;
    }
}