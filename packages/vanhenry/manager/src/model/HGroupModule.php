<?php

namespace vanhenry\manager\model;

use Illuminate\Database\Eloquent\Model;

class HGroupModule extends Model
{
    public function hModules(){
    	return $this->hasMany('vanhenry\manager\model\HModule','parent');
    }
    public function hGroupUsers(){
    	return $this->belongsToMany('vanhenry\manager\model\HGroupUser','h_roles','group_module_id','group_user_id');
    }
    public function hRoles(){
    	return $this->hasMany('vanhenry\manager\model\HRole','group_module_id');
    }
}
