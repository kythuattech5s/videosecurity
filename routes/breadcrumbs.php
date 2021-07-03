<?php
Breadcrumbs::for('home', function ($trail) {
	$trail->push('Trang chủ', VRoute::get('home'));
});
Breadcrumbs::for('allnews', function ($trail) {
	$trail->parent('home');
    $trail->push('Tin tức');
});
Breadcrumbs::for('products', function ($trail) {
	$trail->parent('home');
    $trail->push('Tất cả sản phẩm');
});
Breadcrumbs::for('cart', function ($trail) {
	$trail->parent('home');
    $trail->push(trans('fdb::cart'),VRoute::get('cart'));
});
Breadcrumbs::for('payment', function ($trail) {
	$trail->parent('home');
    $trail->push('Thanh toán');
});
Breadcrumbs::for('contact', function ($trail) {
	$trail->parent('home');
    $trail->push('Liên hệ');
});
Breadcrumbs::for('introduce', function ($trail) {
	$trail->parent('home');
    $trail->push('Giới thiệu');
});
Breadcrumbs::for('page', function ($trail,$currentItem) {
	$trail->parent('home');
	$trail->push($currentItem->name);
});
Breadcrumbs::for('checkout', function ($trail) {
	$trail->parent('cart');
    $trail->push(trans('fdb::checkout'));
});
Breadcrumbs::for('order', function ($trail) {
	$trail->parent('home');
    $trail->push(trans('fdb::track_order'));
});
Breadcrumbs::for('news_category', function ($trail, $currentItem, $level = 0) {
	if ($level == 0) {
		$trail->parent('home');
	}
	if ($currentItem->parent > 0) {
		$parent = App\Models\NewsCategory::translation()->where('news_categories.id', $currentItem->parent)->first();
	    if ($parent != null) {
    		$trail->parent('news_category', $parent, $level += 1);
	    }	
	}
    $trail->push($currentItem->name, \Support::show($currentItem, 'slug'));
});
Breadcrumbs::for('news', function ($trail, $currentItem, $parent) {
    if ($parent == null) {
		$trail->parent('home');
   		$trail->push($currentItem->name, \Support::show($currentItem, 'slug'));
    }
    else{
    	$trail->parent('news_category', $parent);
    	$trail->push($currentItem->name, \Support::show($currentItem, 'slug'));
    }
});
Breadcrumbs::for('news_tag', function ($trail, $currentItem) {
	$trail->parent('home');
	$trail->push($currentItem->name);
});
Breadcrumbs::for('product_category', function ($trail, $currentItem, $level = 0) {
	if ($level == 0) {
		$trail->parent('home');
	}
	if ($currentItem->parent > 0) {
		$parent = App\Models\ProductCategory::where('product_categories.id', $currentItem->parent)->first();
	    if ($parent != null) {
    		$trail->parent('product_category', $parent, $level += 1);
	    }
	}
    $trail->push($currentItem->name, \Support::show($currentItem, 'slug'));
});
Breadcrumbs::for('product_filter', function ($trail) {
	$trail->parent('home');
	$trail->push(trans('fdb::product_filter'));
});
Breadcrumbs::for('product', function ($trail, $currentItem, $parent) {
    if ($parent == null) {
		$trail->parent('home');
   		$trail->push($currentItem->name, \Support::show($currentItem, 'slug'));
    }
    else{
    	$trail->parent('product_category', $parent);
    	$trail->push($currentItem->name, \Support::show($currentItem, 'slug'));
    }
});
Breadcrumbs::for('register', function ($trail) {
	$trail->parent('home');
    $trail->push(trans('fdb::register'));
});
Breadcrumbs::for('login', function ($trail) {
	$trail->parent('home');
    $trail->push(trans('fdb::login'));
});
Breadcrumbs::for('forgot_password', function ($trail) {
	$trail->parent('home');
    $trail->push(trans('fdb::forgot_password'));
});
Breadcrumbs::for('reset_password', function ($trail) {
	$trail->parent('home');
    $trail->push(trans('fdb::reset_password'));
});