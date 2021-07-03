<?php namespace vanhenry\manager\listeners;
use App\ExamQuestion;
use vanhenry\manager\model\HGroupUser;
use vanhenry\manager\model\HUser;
class PreDelete{
    public function handle($table,$_id){
		if($table=="h_users"){
            return $this->checkPermissionUpdateUser($_id);
		}
        return array("status"=>true);
    }
    private function checkPermissionUpdateUser($id){
        $groupUserId = \Auth::guard("h_users")->user()->group;
        $t = $this->fetchGroupUser($groupUserId);
        $user = HUser::find($id);
        if($user==null){
            return array("status"=>false,"code"=>150);
        }
        $gr = $user->group;
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