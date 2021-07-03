<?php
namespace vanhenry\slug;
use Illuminate\Pagination\UrlWindow;
class HWindowUrl extends UrlWindow {
	public function get($onEachSide = 1)
    {
        return $this->getFullSlider($onEachSide);
    }
    public function getFullSlider($onEachSide){
    	
    	return [
            'first'  => $this->customFirstPage(),
            'slider' => $this->customeSlidePage($onEachSide),
            'last'   => $this->customLastPage() ,
        ];
    }
    protected function customLastPage(){
    	$lastPage = $this->lastPage();
    	$currentPage = $this->currentPage();
    	$last = array();
    	if($currentPage<$lastPage-1 && $currentPage>=1){
    		$last["&raquo;"] = $this->paginator->url($currentPage+1);	
    	}
    	if($currentPage<$lastPage && $currentPage>=1){
    		$last["Last"] = $this->paginator->url($this->lastPage());
    	}
    	return $last;
    }
    protected function customeSlidePage($onEachSide){
    	$lastPage = $this->lastPage();
    	$currentPage = $this->currentPage();

    	$to = $currentPage +$onEachSide>$lastPage?$lastPage:$currentPage+$onEachSide;
    	$from = $currentPage-$onEachSide>0?$currentPage-$onEachSide:1;
    	return $this->paginator->getUrlRange(
            $from,
            $to
        );
    }
    protected function customFirstPage(){
    	$lastPage = $this->lastPage();
    	$currentPage = $this->currentPage();
    	if($this->hasPages()){
    		if($currentPage>1 && $currentPage<=$lastPage){
    			$first["First"] = $this->paginator->url(1);
    			$first["&laquo;"] = $this->paginator->url($this->currentPage()-1);	
    			return $first;
    		}
    		
    	}

    	return null;
    }

}