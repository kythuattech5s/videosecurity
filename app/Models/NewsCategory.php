<?php

namespace App\Models;
use App\Models\BaseModel;

class NewsCategory extends BaseModel
{
    public function news(){
    	return $this->belongsToMany('App\Models\News', 'news_news_category', 'news_category_id', 'news_id');
    }
}
