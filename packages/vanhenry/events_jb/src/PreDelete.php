<?php namespace vanhenry\events_jb;
use App\ExamQuestion;
class PreDelete{
    public function handle($table,$_id){
    	if($table instanceof \vanhenry\manager\model\VTable){
    		$tblmap = $table->table_map;
    	}
        else{
            $tblmap = $table;
        }
        
        return array("status"=>true); 
    }
   
}