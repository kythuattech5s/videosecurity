<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class District extends BaseModel
{    
	use HasFactory;
	public function wards()
	{
		return $this->hasMany('App\Models\Ward', 'district_id', 'district_id');
	}
}
