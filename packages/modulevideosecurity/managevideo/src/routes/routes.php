<?php
	Route::group(['prefix'=>'tvs-video','middleware' => 'web','namespace'=>'modulevideosecurity\managevideo\Controllers'],function(){
		Route::get('/playlist/{playlist}','VideoSecurityController@playVideo')->name('tvs-video.playlist');
		Route::get('/key/{key}','VideoSecurityController@key')->name('tvs-video.key');
		Route::get('/auto-convert-tvs','VideoSecurityController@autoConvertTvs');
	});
?>