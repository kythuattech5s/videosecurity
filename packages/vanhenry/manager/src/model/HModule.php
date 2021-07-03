<?php

namespace vanhenry\manager\model;

use Illuminate\Database\Eloquent\Model;

class HModule extends Model
{
    public function hGroupModule(){
    	return $this->belongsTo('vanhenry\manager\model\HGroupModule','id','parent');
    }
}
