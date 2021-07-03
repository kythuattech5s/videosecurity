<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\NewsTag;
use App\Models\NewsCategory;
use App\Models\News;

class NewsTagController extends Controller
{
    public function view($request, $route, $link)
    {
    	$currentItem = NewsTag::slug($link)->act()->ord()->first();
    	if ($currentItem == null) {
    		abort(404);
    	}
    	$listItems = $currentItem->news()->ord()->paginate(8);
        $listNewsCate = NewsCategory::act()->with(['news'])->ord()->get();
    	return view('news_tags.view', compact('currentItem', 'listItems'));
    }
}
