<?php 
namespace vanhenry\manager\translator;
use Illuminate\Support\Facades\Cache as Cache;
use DB;
use Carbon\Carbon as Carbon;
use vanhenry\helpers\helpers\FCHelper as FCHelper;
use vanhenry\manager\CT;
class TranslatorAdapter{
	private $arrDB = array();
	public function __construct(){
		$this->arrDB = $this->getLanguageFromDB();
	}
	private function getLanguageFromDB(){
    	if (!Cache::has(CT::$KEY_LIST_LANGUAGE))
		{
			$cl= DB::table('v_languages')->where(array('act'=>1))->get();
			$ret = array();
			foreach ($cl as $key => $value) {
				$ret[$value->keyword] = $value;
			}
			$expiresAt = Carbon::now()->addMinutes(CT::$TIME_CACHE_LANGUAGE);
			Cache::put(CT::$KEY_LIST_LANGUAGE, $ret, $expiresAt);
		}
		return Cache::get(CT::$KEY_LIST_LANGUAGE);
    }
    public function getItem($key){
    	$key = strtoupper($key);
    	return FCHelper::er($this->arrDB,$key);
    }
    public function getItemValue($key,$locale='vi'){
    	$item = $this->getItem($key,$locale);
    	return FCHelper::er($item,$locale."_value");
    }
}
 ?>