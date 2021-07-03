<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NewsNewsCategory extends Model
{
    use HasFactory;
    protected $table = 'news_news_category';
    public function newsCategory()
    {
    	return $this->belongsTo('App\Models\NewsCategory', 'news_category_id', 'id');
    }
}
