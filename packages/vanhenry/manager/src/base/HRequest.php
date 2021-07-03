<?php
namespace vanhenry\manager\base;
class HRequest extends \Illuminate\Http\Request {
    public function post($key =null,$default =null){
    	$input = $this->getInputSource()->all();
        return data_get($input, $key, $default);
    }
    public function query($key = null, $default = null)
    {
        $input = $this->query->all();
        return data_get($input, $key, $default);
    }
}
?>