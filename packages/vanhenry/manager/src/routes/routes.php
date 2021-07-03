<?php
	$admincp = \Config::get('manager.admincp');
	Route::group(['prefix'=>$admincp,'middleware' => 'web','namespace'=>'vanhenry\manager\controller'],function(){
		Route::get('/','Admin@index');
		Route::get('table-lang/{table}/{locale}',array( 'uses'=>"Admin@tableLang"));
		Route::get('view/{table}',array( 'uses'=>"Admin@view"));
		Route::get('trashview/{table}',array( 'uses'=>"Admin@trashview"));
		Route::any('getData/{table}',array( 'uses'=>"Admin@getData"));
		Route::any('getDataPivot/{table}',array( 'uses'=>"Admin@getDataPivot"));
		// Route::any('getRecursive/{table}',array( 'uses'=>"Admin@getRecursive"));
		Route::any('getRecursive/{table}',["uses"=>"Admin@getRecursive"]);
		Route::match(['get', 'post'],'search/{table}',array( 'uses'=>"Admin@search"));
		Route::get('404',['as' => '404', 'uses' =>"Admin@view404"]);
		Route::get('no_permission',['as' => 'no_permission', 'uses' =>"Admin@noPermission"]);
		Route::post('delete/{table}',array( 'uses'=>"Admin@delete"));
		Route::post('trash/{table}',array( 'uses'=>"Admin@trash"));
		Route::post('backtrash/{table}',array( 'uses'=>"Admin@backtrash"));
		Route::post('deleteAll/{table}',array( 'uses'=>"Admin@deleteAll"));
		Route::get('viewdetail/{table}/{id}',array( 'uses'=>"Admin@viewDetail"));
		Route::get('edit/{table}/{id}',array( 'uses'=>"Admin@edit"));
		Route::post('edit/{table}/{id}',array( 'uses'=>"Admin@edit"));
		Route::get('insert/{table}',array( 'uses'=>"Admin@insert"));
		Route::get('copy/{table}/{id}',array( 'uses'=>"Admin@copy"));
		Route::post('update/{table}/{id}',array( 'uses'=>"Admin@update"));
		Route::post('save/{table}/{id}',array( 'uses'=>"Admin@save"));
		Route::post('store/{table}',array( 'uses'=>"Admin@store"));
		Route::post('storeAjax/{table}',array( 'uses'=>"Admin@storeAjax"));
		Route::post('addAllToParent/{table}',array( 'uses'=>"Admin@addAllToParent"));
		Route::post('removeFromParent/{table}',array( 'uses'=>"Admin@removeFromParent"));
		Route::post('editableajax/{table}',array( 'uses'=>"Admin@editableAjax"));
		//Menu
		Route::match(['get', 'post'],'getDataMenu/{table}',array( 'uses'=>"Admin@getDataMenu"));
		Route::match(['get', 'post'],'getStaticMenu',array( 'uses'=>"Admin@getStaticMenu"));
		Route::match(['get', 'post'],'export/{table}',array('uses'=>"Admin@exportOrder"));
		// cron product with link product from: https://www.digikey.com/
		Route::get('crawl-product', array('uses' => 'Admin@crawlProduct'));
		Route::get('crawl-excute', array('uses' => 'Admin@crawlExcute'));
		Route::get('testdom', array('uses' => 'Admin@testdom'));
		Route::get('testdom2', array('uses' => 'Admin@testdom2'));
		// add property
		Route::post('them-thuoc-tinh', array('uses' => 'Admin@addProperty'));
		// update status of products in order
		Route::get('update-status', array('uses' => 'Admin@updateStatusOrderProduct'));
		// Export file pdf from data is html
		Route::get('export-pdf/{orderId}', array('uses' => 'Admin@exportPdfOrderDetail'));
		Route::get('test-pdf', array('uses' => 'Admin@testPdf'));
		//Import
		Route::get('import/{table}',array( 'uses'=>"Admin@import"));
		Route::post('do_import/{table}',array( 'uses'=>"Admin@do_import"));
		//Phaan quyen
		Route::post('do_assign/{table}',array( 'uses'=>"Admin@do_assign"));
		Route::post('getCrypt',array( 'uses'=>"Admin@getCrypt"));
		//Khác
		Route::get('terms',array( 'uses'=>"Admin@termService"));				
		Route::post('changepass',array( 'uses'=>"Admin@changePass"));
		//Ngôn ngữ
		Route::get('changelang/{lang}',array( 'uses'=>"Admin@changeLanguage"));
		//JBIG
		Route::get('view_user/{userid}',array( 'uses'=>"Admin@view_user"))->where(['userid' => '[0-9]+']);
		//
		Route::get('login','AuthController@getLogin');
		Route::post('login','AuthController@postLogin');
		Route::get('register','AuthController@getRegister');
		Route::post('register','AuthController@postRegister');
		Route::get('logout','AuthController@logout');
		//Media
		Route::get('media/manager','MediaController@showmedia');
		Route::get('media/view','MediaController@media');
		Route::get('media/trash','MediaController@trash');
		Route::post('media/createDir','MediaController@createDir');
		Route::post('media/getInfoLasted','MediaController@getInfoLasted');
		Route::post('media/deleteFolder/{type?}','MediaController@deleteFolder')->where(['type' => '[0-9]+']);//->where(['id' => '[0-9]+', 'name' => '[a-z]+']);
		Route::post('media/uploadFile','MediaController@uploadFile');
		Route::post('media/uploadFileWm','MediaController@uploadFileWm');
		Route::post('media/restore','MediaController@restoreFile');
		Route::post('media/getInfoFileLasted','MediaController@getInfoFileLasted');
		Route::post('media/deleteFile/{type?}','MediaController@deleteFile')->where(['type' => '[0-9]+']);
		Route::post('media/deleteAll/{type?}','MediaController@deleteAll')->where(['type' => '[0-9]+']);
		Route::post('media/copyFile','MediaController@copyFile');
		Route::post('media/moveFile','MediaController@moveFile');
		Route::post('media/listFolder','MediaController@listFolder');
		Route::post('media/listFolderMove','MediaController@listFolderMove');
		Route::post('media/getDetailFile','MediaController@getDetailFile');
		Route::post('media/saveDetailFile','MediaController@saveDetailFile');
		Route::post('media/duplicateFile','MediaController@duplicateFile');
		Route::post('media/rename','MediaController@rename');
		//Sys
		Route::get('vtableview','SysController@inserttable');
		Route::get('onoffmenu','SysController@onOffMenu');
		Route::get('onoffmenu','SysController@onOffMenu');
		Route::post('inserttableview','SysController@doinserttable');
		Route::get('deleteCache','SysController@deleteCache');
		Route::match(['get', 'post'],'editRobot','SysController@editRobot');
		Route::get('editSitemap','SysController@editSitemap');
		Route::post('updateSitemap','SysController@updateSitemap');
		// Kiot viet
		Route::get('syn-quantity-from-kiot',array( 'uses'=>"Admin@synQuantityFromKiot"));
		// promotion
		Route::post('create-promotion', array( 'uses'=>"Admin@createPromotion"));
		Route::post('add-item-promotion', array( 'uses'=>"Admin@addItemPromotion"));
		Route::post('edit-item-promotion', array( 'uses'=>"Admin@editItemPromotion"));
		Route::post('delete-item-promotion', array( 'uses'=>"Admin@deleteItemPromotion"));
		Route::post('change-act-promotion', array( 'uses'=>"Admin@changeActProduct"));
		Route::post('delete-promotion',array('uses'=>'Admin@deletePromotion'));
		// deal
		Route::post('create-deal', array( 'uses'=>"Admin@createDeal"));
		Route::get('edit-deal/{deal}', array( 'uses'=>"Admin@editDeal"));
		Route::post('edit-deal/{deal}', array( 'uses'=>"Admin@editDeal"));
		Route::post('deal-product-main-action/{deal}', array( 'uses'=>"Admin@dealProductMainAction"));
		Route::post('deal-product-sub-action/{deal}', array( 'uses'=>"Admin@dealProductSubAction"));
		Route::post('update-price-deal-sub/{deal}', array( 'uses'=>"Admin@updatePriceDealSub"));
		Route::get('delete-deal/{deal}', array( 'uses'=>"Admin@deleteDeal"));
		// voucher
		Route::post('create-voucher', array( 'uses'=>"Admin@createVoucher"));
		Route::post('deleteItemProductVoucher',array('uses'=>'Admin@deleteItemVoucher'));
		Route::post('editItemProductVoucher',array('uses'=>'Admin@getEditVoucher'));
		Route::get('searchVoucher', array('uses'=>"Admin@searchAllProductPromotions"));
		//FlashSale
		Route::post('/them-san-pham-flash-sale',array('uses'=>'Admin@addProductToFlashSale'));
		Route::post('/sua-san-pham-flash-sale',array('uses'=>'Admin@editProductFlashSale'));
		Route::post('/tao-flash-sale',array('uses'=>'Admin@createFlashSale'));
		Route::post('/tim_khung_gio',array('uses'=>'Admin@findSlotTime'));
		Route::post('/xoa-san-pham-flash-sale',array('uses'=>'Admin@deleteItem'));
		Route::post('/thay-doi-trang-thai-flash-sale',array('uses'=>'Admin@changeAct'));
		Route::post('/edit-flash-sale',array('uses'=>'Admin@editFlashSale'));
		//Combo
		Route::post('/tao-combo-event',array('uses'=>'Admin@createComboEvent'));
		Route::post('/them-san-pham-combo',array('uses'=>'Admin@addItemToCombo'));
		Route::post('/xoa-san-pham-combo',array('uses'=>'Admin@deleteItemInCombo'));
		// marketing
		Route::get('marketings',array('uses'=>"Admin@marketing"));
		//statistics
		Route::get('statistic',array('uses'=>"Admin@statistic"));
		// mailchimp
		Route::get('manager-mailchimp',array('uses'=>"Admin@managerMailchimp"));
		// seach product on modal
		Route::get('search-product-modal', array('uses'=>"Admin@searchProductModal"));
		// choose product on modal
		Route::post('choose-product-modal', array('uses'=>"Admin@chooseProductModal"));
		// chi tiết đấu giá
		Route::get('detail/auctions/{id}', array('uses'=>"Admin@auctionDetail"));
		Route::get('dashboard', array('uses'=>"Admin@dashboard"));
		//update status orders carries 
		Route::post('/update-status-ghn', array('uses'=>"Admin@updateStatusGhn"));
		Route::post('/update-status-ghtk', array('uses'=>"Admin@updateStatusGhtk"));
		//order 
		Route::post('/showOrderDetail',array('uses'=>"Admin@getDetailOrder"));
		Route::post('/createOrderCarrier',array('uses'=>"Admin@confirmOrder"));
		Route::post('/showPreOrderDetail',array('uses'=>"Admin@getDetailPreOder"));
		Route::post('/sentMailHaveProduct',array('uses'=>"Admin@sentMailHaveProduct"));
		Route::post('/findImgAcceptance',array('uses'=>"Admin@filterImgAcceptance"));
		Route::post('/saveInfoOrder',array('uses'=>"Admin@saveInfoOrder"));
		//showAttribute

		Route::post('/showAttribute',array('uses'=>"Admin@showAttribute"));
		Route::post('/showAgency',array('uses'=>"Admin@showAgency"));
		Route::post('/showTechnician',array('uses'=>"Admin@showTechnician"));
		Route::post('/confirmAgency',array('uses'=>"Admin@confirmAgency"));
		Route::post('/confirmTechnician',array('uses'=>"Admin@confirmTechnician"));
		Route::post('/activeOrder',array('uses'=>"Admin@activeOrder"));
		Route::post('/tranferTechnician',array('uses'=>"Admin@transferTechnician"));


		//Get ADDRESS 

		Route::post('/get-wards',array('uses'=>"Admin@getWards"));
		Route::post('/get-districts',array('uses'=>"Admin@getDistricts"));
		Route::post('get-district-by-province',array('uses'=>"Admin@getDistrictByProvince"));
		Route::get('/an-view',array('uses'=>"Admin@anView"));
		Route::post('/shoot-to-technician', array('uses'=>"Admin@shootToTechnician"));
		Route::post('/agreeRefund',array('uses'=>"Admin@agreeRefund"));
		Route::post('/shoot-to-carrier', array('uses'=>"Admin@shootToCarrier"));

		Route::post('/send-notification-voucher', array('uses'=>"Admin@sendNotificationVoucher"));
		Route::post('/rep-ask',array('uses'=>"Admin@repAskQuestion"));


		/*Route Sapo*/
		Route::get('/dong-bo-sapo',array('uses'=>"Admin@syscSapoShop"));
		Route::get('/apply-for-permission-sapo-shop',array('uses'=>"Admin@applyPermissionSapoShop"));
		Route::get('/callback-premission-sapo',array('uses'=>"Admin@callBackPermissionSapo"));

		Route::match(['GET','POST'],'/rakuten',array('uses'=>"Admin@syncRakuten"));
		Route::match(['GET','POST'],'/syncCategory',array('uses'=>"Admin@syncCategory"));
	});
?>