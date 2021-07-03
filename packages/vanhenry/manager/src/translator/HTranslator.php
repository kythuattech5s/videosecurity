<?php 
namespace vanhenry\manager\translator;
use Illuminate\Translation\Translator as BaseTranslator;
use DB;
use Illuminate\Support\Facades\Cache as Cache;
use App;
use vanhenry\manager\CT;
use App\Models\Language;
use App\Models\VLanguage;

class HTranslator extends BaseTranslator{
    protected function getLine($namespace, $group, $locale, $item, array $replace)
    {
    	if ($namespace == 'fdb') {
    		return Language::get($group);
    	}
    	elseif ($namespace == 'db') {
			return VLanguage::get($group);
    	}
        else{
        	$this->load($namespace, $group, $locale);
	        $line = \Arr::get($this->loaded[$namespace][$group][$locale], $item);
	        if (is_string($line)) {
	            return $this->makeReplacements($line, $replace);
	        } elseif (is_array($line) && count($line) > 0) {
	            foreach ($line as $key => $value) {
	                $line[$key] = $this->makeReplacements($value, $replace);
	            }
	            return $line;
	        }
        }
    }
}
?>