<?php 

namespace vanhenry\helpers\helpers;

use vanhenry\helpers\helpers\StringHelper;

class ArrayHelper{

	public static function containKey($arr,$key){

		if(!StringHelper::isNull($key)){

			foreach ($arr as $k => $v) {

				if(strpos($k, $key)!==FALSE){

					return true;

				}

			}

		}

		return false;

	}



	public static function filterKey($clArray,$key){

		$ret = $clArray->filter(function ($v,$k) use ($key) {

				return \Str::startsWith($k,$key);

			});

		return $ret;

	}

}