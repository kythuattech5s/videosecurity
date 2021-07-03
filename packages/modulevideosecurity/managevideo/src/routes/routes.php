<?php
	Route::group(['prefix'=>'tvs-video','middleware' => 'web','namespace'=>'modulevideosecurity\managevideo\Controllers'],function(){
		Route::get('/','VideoSecurityController@test');
	});
?>