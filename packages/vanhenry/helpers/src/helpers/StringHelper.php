<?php 

namespace vanhenry\helpers\helpers;

class StringHelper{



	public static function isNull($input){

		if(isset($input)){

			return is_string($input) && strlen(trim($input))==0;

		}

		return true;

	}

	public static function normal($input){

		if(isset($input)){

			return trim(strtolower($input));

		}

		return "";

	}

	public static function count($input){

		return count(explode(' ', $input));

	}

}