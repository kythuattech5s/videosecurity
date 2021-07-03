<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\News;
use App\Models\NewsCategory;
class NewsController extends Controller
{	
    public function view($request, $route, $link){
        $currentItem = News::slug($link)->act()->first();
        if ($currentItem == null) { abort(404); }
        $parent = $currentItem->category()->act()->first();
        $tags = $currentItem->tags()->act()->get();
        $newsRelateds = $currentItem->getRelatesCollection();
        return view('news.view',compact('currentItem','tags','newsRelateds','parent','tags'));
    }
}
