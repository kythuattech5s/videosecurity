<?php

namespace App\Models;

use App\Models\BaseModel;
use App\Models\NewsNewsTag;
use App\Models\NewsNewsCategory;

class News extends BaseModel
{
	protected $table = 'news';
    public function tags()
    {
    	return $this->belongsToMany('App\Models\NewsTag', 'news_news_tag', 'news_id', 'news_tag_id')->act()->ord();
    }

    public function pivot(){
    	return $this->hasMany('\App\Models\NewsNewsCategory', 'news_id', 'id');
    }
    
    public function category()
    {
    	return $this->belongsToMany('App\Models\NewsCategory');
    }
    
    public function getRelates()
    {
        $category = $this->category()->act()->first();
        if ($category == null) {
            return null;
        }
        return $category->news();
    }
    public function getRelatesCollection(){
        $relate = $this->getRelates();
        return $relate?$relate->act()->ord()->take(4)->get():collect();
    }
}
