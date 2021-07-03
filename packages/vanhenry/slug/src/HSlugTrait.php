<?php 
namespace vanhenry\slug;
use Illuminate\Support\Collection;
use DB;
/**
 * summary
 */
trait HSlugTrait
{
	private $arrConfigs= array();
	public function getSlugConfig(){
		$def = app('config')->get('hslug');
		if(property_exists($this, 'configs')){
			if(count($this->arrConfigs)==0){
				$this->arrConfigs= array_merge($def, $this->configs);
			}
			return $this->arrConfigs;
		}
		return $def;
	}
	public function getSlugSource(){
		$config = $this->getSlugConfig();
		return $config['build_from'];
	}
	public function getSlugDestination(){
		$config = $this->getSlugConfig();
		return $config['save_to'];
	}
	public function needSlugging(){
		$config = $this->getSlugConfig();
		$on_update = $config['on_update'];
		$save_to = $config['save_to'];
		//check tồn tại
		if (empty($this->attributes[$save_to])) {
            return true;
        }
        // Check chỉnh sửa
        if ($this->isDirty($save_to)) {
            return false;
        }
        return ($this->exists && $on_update);
	}
	public function generateSlug($source){
		$config = $this->getSlugConfig();
		$separator = $config['separator'];
		if(empty($source)){
			return "";
		}
		else{
			return Slugger::generateSlug($source,$separator);
		}

	}
	public function makeSlugUnique($slug){
		$config = $this->getSlugConfig();
		$save_to = $config['save_to'];
		$extension = $config['extension'];
		$separator = $config['separator'];
		$count = $this->countExistSlug($slug,$extension);
		if($count>0){
			$slug = $this->generateSuffix($slug,$extension,$separator,$count);
		}
		else{
			$slug .=$extension;
		}
		return $slug;
	}
	private function generateSuffix($slug,$extension,$separator,$count){
		
		$total = $count;
		$slugtmp = $slug;
		while($count > 0){
			$slugtmp = $slug.$separator.$total;	
			$count = $this->countExistSlug($slugtmp,$extension);
			$total +=$count;
		}
		return $slugtmp;
	}
	public function countExistSlug($slug,$extension){
		$instance = new static;
		// $query = $instance->where(function ($query) use($slug,$save_to) {
		// 	$query->where($save_to,'=',$slug)
		// });
		// $list = $query->lists($this->getKeyName(),$save_to);
		// $listT =  $list->all();  

		$slug = $slug.$extension;

		$listR = DB::table('v_routes')
			->where('link',$slug)->get();
			;
		$count = count($listR);
		return $count;
	}
	public function getSlug(){
		$config = $this->getSlugConfig();
		$save_to = $config['save_to'];
		return $this->getAttribute($save_to);
	}
	public function setSlug($slug){
		$config = $this->getSlugConfig();
		$save_to = $config['save_to'];
		return $this->setAttribute($save_to,$slug);
	}
	private function insertRoutes($slug){
		$config = $this->getSlugConfig();
		$name = $this->getAttribute($config['build_from']);
		DB::table('v_routes')->insert(
		    ['name' => $name,
		    'link' => $slug,
		     'table_map' => $this->getTable(),
		     'tag_id' => $this->getKey(),
		     'is_static' =>0,
		     'created_at' =>\DateTime(),
		     'updated_at' =>\DateTime(),
		     'controller'=>$this->getTable().".view"
		     ]
		);
	}
	public function sluggify($force =false){
		if ($this->fireModelEvent('slugging') === false) {
            return false;
        }
        if($force || $this->needSlugging()){
        	
        	$source =$this-> getSlugSource();
        	$source = $this->getAttribute($source);
        	$slug = $this->generateSlug($source);
        	$slug = $this->makeSlugUnique($slug);
        	$this->setSlug($slug);
        	
        	$this->fireModelEvent('slugged');
        }
        if ($this->fireModelEvent('slug_done') === false) {
            return false;
        }
	}
	public function afterSluggify(){
		$this->insertRoutes($this->getSlug());
	}
	public function removeSlug(){
		DB::table('v_routes')
			->where('link',$this->getSlug())->delete();
			;
	}
}

?>