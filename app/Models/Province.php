<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Province extends BaseModel
{
	use HasFactory;
	public function district()
	{
		return $this->hasMany('App\Models\District', 'province_id', 'province_id');
	} 
}