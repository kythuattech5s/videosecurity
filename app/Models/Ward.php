<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseModel;

class Ward extends BaseModel
{
	use HasFactory;
	
	public static function getWards($district_id){
		$wards = self::where('district_id',$district_id['id'])->get();
		$output = '<option value="">'.trans('fdb::wards').'</option>';
		foreach ($wards as $ward){
			$output .='<option value="'.$ward->id.'">'.$ward->name.'</option>';
		}
		return $output;
	}
}
