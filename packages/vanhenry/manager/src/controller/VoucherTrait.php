<?php

namespace vanhenry\manager\controller;

use Illuminate\Http\Request;
use App\Models\{Deal, Combo, Promotion, flashSale, Product, Voucher, VoucherProduct,VoucherTranslation,Notification,User,TokenNotification};
use Carbon\Carbon;
use SettingHelper;
use DB;

trait VoucherTrait
{
	private	$type_code_all_product_shop = 1;
	private $type_voucher_promotion = 1;
	private $type_discount_money = 1;
	private $find_by_name = 1;
	private $find_by_code = 2;

	public function createVoucher(Request $request)
	{
		$validator = $this->validateVoucher($request->all());
		if ($validator->fails()) {
			return response()->json([
				'code' => 100,
				'message' => $validator->errors()->first(),
			]);
		}
		$checkInputData = $this->checkInputData($request);
		if ($checkInputData !== true) {
			return response()->json(['code' => 100, 'message' => $checkInputData]);
		}
		$checkTime = $this->checkTime($request);
		if ($checkTime !== true) {
			return response()->json(['code' => 100, 'message' => $checkTime]);
		}
		$voucher = $this->addVoucher($request);

		if((int)$request->type_code !== $this->type_code_all_product_shop){
			$createVoucher = $this->createProductVoucher($voucher->id, $request->id);
		}

		return response()->json(['code' => 200, 'message' => 'Tạo mã giảm giá thành công', 'url' => '/esystem/view/vouchers']);
	}

	protected function setName($id,$request){

		$voucherTranlationEN = new VoucherTranslation();
		$voucherTranlationEN->language_code = 'en';
		$voucherTranlationEN->name = $request->name;
		$voucherTranlationEN->map_id = $id;
		$voucherTranlationEN->save();

		$voucherTranlationVI = new VoucherTranslation();
		$voucherTranlationVI->language_code = 'vi';
		$voucherTranlationVI->name = $request->name;
		$voucherTranlationVI->map_id = $id;
		$voucherTranlationVI->save();
	}

	protected function checkInputData($request)
	{	
		if($request->voucher_id == null){
			$voucher = Voucher::where('code', 'JAGER' . $request->code)->first();
			if ($voucher !== null) {
				return 'Đã tồn tại mã giảm giá này';
			}
		}
		$arrayId = json_decode($request->id);
		if ((int)$request->type_code == $this->type_code_all_product_shop) {
			if ((int)$request->type_voucher == $this->type_voucher_promotion) {
				if ((int)$request->type_discount == $this->type_discount_money) {
					if (!$request->discount) {
						return 'Vui lòng nhập số tiền giảm giá';
					}
					if ((int)$request->discount > (int)$request->min_value_order) {
						return 'Mức giảm giá không được vượt quá giá trị đơn hàng tối thiểu';
					}
				} else {
					if (!$request->discount) {
						return 'Vui lòng nhập phần trăm giảm giá';
					}
					if ((int)$request->discount < 1 || (int)$request->discount > 99) {
						return 'Mã giảm giá không được nhỏ hơn 1% và lớn hơn 99%';
					}
				}
			} else {
				if (!$request->discount) {
					return 'Vui lòng nhập phần trăm giảm giá';
				}
				if ((int)$request->discount < 1 || (int)$request->discount > 99) {
					return 'Mã giảm giá không được nhỏ hơn 1% và lớn hơn 99%';
				}
			}
		} else {
			if ($request->id == null) {
				return 'Vui lòng chọn sản phẩm';
			}
			if ((int)$request->type_voucher == $this->type_voucher_promotion) {
				if ((int)$request->type_discount == $this->type_discount_money) {
					if (!$request->discount) {
						return 'Vui lòng nhập số tiền giảm giá';
					}
					foreach ($arrayId as $id) {
						$product = Product::find($id);
						if($product->price == 0 || $product->price == null){
							$productPrice = $product->price_old;
						}else{
							$productPrice = $product->price;
						}
						if ((int)$request->discount > $productPrice) {
							return 'Mức giảm giá không được vượt quá giá trị đơn hàng tối thiểu';
						}
					}
				} else {
					if (!$request->discount) {
						return 'Vui lòng nhập số tiền giảm giá';
					}
					// if ((int)$request->discount < 1 || (int)$request->discount > 99) {
					// 	return 'Mã giảm giá không được nhỏ hơn 1% và lớn hơn 99%';
					// }
				}
			} else {
				if (!$request->discount) {
					return 'Vui lòng nhập phần trăm giảm giá';
				}
				if ((int)$request->discount < 1 || (int)$request->discount > 99) {
					return 'Mã giảm giá không được nhỏ hơn 1% và lớn hơn 99%';
				}
			}
			// foreach ($arrayId as $id) {
			// 	$product = Product::find($id);
			// 	$productPrice = $product->price == null ? $product->price_old : $product->price;
			// 	if ((int)$request->min_value_order > $productPrice) {
			// 		return 'Mức giá tối thiểu không được vượt quá giá trị đơn hàng tối thiểu';
			// 	}
			// }
		}
		return true;
	}

	protected function addVoucher($request)
	{	
		$voucher = new Voucher();
		$voucher->name = $request->code;
		$voucher->code = 'ECO' . $request->code;
		$voucher->type_code = (int)$request->type_code;
		$voucher->start_at = new \DateTime($request->start_at);
		$voucher->expired_at = new \DateTime($request->expired_at);
		$voucher->type_voucher = $request->type_voucher;
		$voucher->type_discount = (int)$request->type_discount;
		$voucher->discount = (int)$request->discount;
		$voucher->min_value_order = $request->min_value_order;
		$voucher->qty = $request->qty;
		$voucher->short_content = $request->short_content;
		$voucher->is_public = (int)$request->is_public;
		$voucher->act = 1;
		$voucher->img = isset($request->img) ? \Support::uploadImg('img','voucher') : null;
		$voucher->save();
		return $voucher;
	}

	protected function checkTime($request){
		$startTime = Carbon::parse($request->start_at);
		$endTime = Carbon::parse($request->expired_at);
		if($request->action == null){
			if ($startTime < Carbon::now()) {
				return 'Vui lòng nhập thời gian bắt đầu muộn hơn thời gian hiện tại';
			}
			if ($startTime > $endTime) {
				return 'Thời gian bắt đầu không thể muộn hơn thời gian kết thúc';
			}
			if ($startTime->diffInMinutes($endTime) < 60) {
				return 'Chương trình phải kéo dài ít nhất là 1h kể từ khi bắt đầu';
			}
		}else{
			if ($startTime > $endTime) {
				return 'Thời gian bắt đầu không thể muộn hơn thời gian kết thúc';
			}
			if ($startTime->diffInMinutes($endTime) < 60) {
				return 'Chương trình phải kéo dài ít nhất là 1h kể từ khi bắt đầu';
			}
			$voucher = Voucher::find($request->voucher_id);
			if($voucher !== null){
				$voucherStartTime = Carbon::parse($voucher->start_at);
				$voucherEndTime = Carbon::parse($voucher->expired_at);
				if ($voucherStartTime < Carbon::now() && $voucherEndTime > Carbon::now()) {
					if($startTime->diffInMinutes($voucherStartTime) !== 0){
						return 'Chương trình hiện đang diễn ra bạn không được thay đổi thời gian bắt đầu sự kiện';
					}
					if($endTime < $voucherEndTime == true){
						return 'Chương trình hiện đang diễn ra bạn không giảm thời gian sự kiện';
					}
				}else{
					if(Carbon::now()>$voucherEndTime){
						return 'Chương trình đã kết thúc bạn có thể tạo 1 chương trình mới';
					}
					return true;
				}
			}
		}
		return true;
	}

	protected function createProductVoucher($voucher_id, $id)
	{
		$arrayId = json_decode($id);
		$data = [];
		foreach ($arrayId as $id) {
			$item = [];
			$item['product_id'] = $id;
			$item['voucher_id'] = $voucher_id;
			$item['created_at'] = new \Datetime();
			$item['updated_at'] = new \Datetime();
			$data[] = $item;
		}
		DB::table('product_voucher')->insert($data);
	}

	protected function validateVoucher(array $data)
	{
		return \Validator::make($data, [
			'type_code' => ['required'],
			'name' => ['required'],
			'code' => ['required'],
			'start_at' => ['required'],
			'expired_at' => ['required'],
			'type_voucher' => ['required'],
			'type_discount' => ['required'],
			'min_value_order' => ['required'],
			'qty' => ['required'],
		], [
			'required' => trans('fdb::required') . ' :attribute',
			'string' => ':attribute ' . trans('fdb::malformed'),
			'max' => ':attribute content_max :max',
		], [
			'name' => 'Tên chương trình giảm giá',
			'type_code' => 'Loại mã',
			'code' => 'Mã giảm giá',
			'start_at' => 'Thời gian bắt đầu',
			'expired_at' => 'Thời gian kết thúc',
			'type_voucher' => 'Loại voucher',
			'type_discount' => 'Loại giảm giá',
			'min_value_order' => 'Giá trị đơn hàng tối thiểu',
			'qty' => 'Số lượng mã giảm giá',
		]);
	}

	public function getEditVoucher(Request $request){
		$validator = $this->validateVoucher($request->all());
		if ($validator->fails()) {
			return response()->json([
				'code' => 100,
				'message' => $validator->errors()->first(),
			]);
		}
		$checkTime = $this->checkTime($request);
		if($checkTime !== true){
			$data = [];
			$voucher = Voucher::find((int)$request->voucher_id);
			$data['start_at'] = $voucher->start_at;
			$data['expired_at'] = $voucher->expired_at;
			return response()->json(['code'=>100,'message'=>$checkTime,'data'=>$data]);
		}
		$checkInputData = $this->checkInputData($request);
		if ($checkInputData !== true) {
			return response()->json(['code' => 100, 'message' => $checkInputData]);
		}
			$voucher = $this->editVoucherDetail($request);
		if((int)$request->type_code !== $this->type_code_all_product_shop){
			$createVoucher = $this->editVoucher($voucher->id, $request->id);
		}else{
			$deleteProductVoucher = $this->deleteVoucherProduct($voucher->id);
		}
		return response()->json(['code' => 200, 'message' => 'Cập nhật mã giảm giá thành công', 'url' => '/esystem/viewdetail/vouchers/'.$voucher->id]);
	}

	protected function editVoucherDetail($request){
		
		$voucher = Voucher::find($request->voucher_id);
		$voucher->name = $request->name;
		$voucher->code = 'ECO' . $request->code;
		$voucher->type_code = (int)$request->type_code;
		$voucher->start_at = new \DateTime($request->start_at);
		$voucher->expired_at = new \DateTime($request->expired_at);
		$voucher->type_voucher = $request->type_voucher;
		$voucher->type_discount = (int)$request->type_discount;
		$voucher->discount = (int)$request->discount;
		$voucher->min_value_order = $request->min_value_order;
		$voucher->qty = $request->qty;
		$voucher->is_public = (int)$request->is_public;
		$voucher->act = 1;
		$voucher->img = isset($request->img) ? \Support::uploadImg('img','voucher') : $voucher->img;
		$voucher->save();
		return $voucher;
	}

	public function editVoucher($voucher_id,$arrayId){
		$arrayId = json_decode($arrayId);
		$voucherProducts = VoucherProduct::where('voucher_id',$voucher_id)->get();
		$data = [];
		if($voucherProducts->count() !== 0){
			foreach($voucherProducts as $product){
				if ( ($key = array_search($product->product_id, $arrayId)) !== false) {
				    unset($arrayId[$key]);
				}
			}
		if(count($arrayId) !== 0)
			foreach($arrayId as $id){
				$item = [];
				$item['product_id'] = $id;
				$item['voucher_id'] = $voucher_id;
				$item['updated_at'] = new \DateTime();
				$data[] = $item;
			}
		}else{
			foreach($arrayId as $id){
				$item = [];
				$item['product_id'] = $id;
				$item['voucher_id'] = $voucher_id;
				$item['updated_at'] = new \DateTime();
				$data[] = $item;
			}
		}
		DB::table('product_voucher')->insert($data);
	}

	public function deleteVoucherProduct($voucher_id){
		$array = VoucherProduct::where('voucher_id',(int)$voucher_id)->delete();
	}

	public function deleteItemVoucher(Request $request){
		$item = VoucherProduct::where('voucher_id',(int)$request->id)->where('product_id',$request->product_id)->delete();
		return response()->json(['code'=>200,'message'=>'Xóa sản phẩm thành công']);
	}

	public function searchAllProductPromotions(Request $request){
		$type = $request->marketing_type;
		$id = $request->id;
		$instance = null;
		/*item mà user đã chọn trước đó*/
		$chooseStorage = json_decode($request->choose_storage, true);
		$chooseStorage = !is_array($chooseStorage) ? [] : $chooseStorage;

		switch ($type) {
			case 'voucher':
				$instance = Voucher::where('id',$id)->first();
				$productInPromotionID = \App\Models\VoucherProduct::where('voucher_id',$id)->pluck('product_id');
				break;
			case 'flashsale':
				$instance = \App\Models\FlashSale::where('id',$id)->first();
				$productInPromotionID = \App\Models\FlashSaleProduct::where('flash_sale_id',$id)->pluck('product_id');
				break;
			case 'combo':
				$instance = \App\Models\Combo::where('id',$id)->first();
				$productInPromotionID = \App\Models\ComboProduct::where('combo_id',$id)->pluck('product_id');
				break;
			case 'promotion':
				$instance = \App\Models\Promotion::where('id',$id)->first();
				$productInPromotionID = \App\Models\ProductPromotion::where('promotion_id',$id)->pluck('product_id');
				break;
		}
		$categoryId = $request->category;
		$type = $request->type;
		$keyword = $request->keyword;
		$productChooses = Product::whereDoesntHave('flashSale', function($q) use($instance){
    		$q->where('start_at', '<=', $instance->expired_at)->where('expired_at', '>=', $instance->start_at);
    	})
    	->whereDoesntHave('deal', function($q) use($instance){ // sp deal chính
    		$q->where(function($q2) use($instance){
    			$q2->where('start_at', '<=', $instance->expired_at)->where('expired_at', '>=', $instance->start_at);
			});
    		if ($instance instanceof Deal) {
    			$q->where('deals.id', $instance->id);
    		}
    	})
    	->whereDoesntHave('getDealSub', function($q) use($instance){ // sp deal đi kèm
    		$q->where(function($q2) use($instance){
    			$q2->where('start_at', '<=', $instance->expired_at)->where('expired_at', '>=', $instance->start_at);
			});
    		if ($instance instanceof Deal) {
    			$q->where('deals.id', $instance->id);
    		}
    	})
    	->whereDoesntHave('combo', function($q) use($instance){ // sp deal chính
    		$q->where(function($q2) use($instance){
    			$q2->where('start_at', '<=', $instance->expired_at)->where('expired_at', '>=', $instance->start_at);
			});
    		if ($instance instanceof Combo) {
    			$q->where('combos.id', $instance->id);
    		}
    	})
    	->
    	where(function($q) use($categoryId, $type, $keyword){
    		if ($categoryId != null) {
    			$q->whereHas('category', function($q2) use($categoryId){
    				$q2->where('id', $categoryId);
    			});
    		}
    		if ($type == 1 && $keyword != null && trim($keyword) != '') {
    			$q->where('name', 'like', '%'.$keyword.'%');
    		}
    		elseif($type == 2 && $keyword != null && trim($keyword) != ''){
    			$q->where('code', 'like', '%'.$keyword.'%');
    		}
    	})
    	->whereNotIn('id',$productInPromotionID)
    	->where('act',1)
    	->orderBy('id', 'desc')
    	->get();

    	return \Support::json([
    		'code' => 200,
    		'html' => view('vh::view.voucher.searchItem', compact('productChooses', 'chooseStorage'))->render(),
    	]);
	}

	public function sendNotificationVoucher(Request $request){
		$voucher = Voucher::find($request->voucher_id);
		$icon = url(json_decode(SettingHelper::getSetting('logo'))->path.json_decode(SettingHelper::getSetting('logo'))->file_name);
		$users = User::all();
		foreach($users as $user){
			$noti = new Notification;
			$noti->notifications = $voucher->short_content;
			$noti->watched = 0;
			$noti->user_id = $user->id;
			$noti->type = \NotificationConstant::TYPE_VOUCHER;
			$noti->noti_for = \NotificationConstant::FOR_USER;
			$noti->title = $voucher->name;
			$noti->link = \VRoute::getWithLanguageFull('counpon-account');
			$noti->save();
			$count = Notification::where('user_id',$user->id)->where('watched',0)->get()->count();
			if(TokenNotification::where('user_id',$user->id)->first() !== null){
				$noti->singleUser(TokenNotification::where('user_id',$user->id)->first()->token,$noti->title,$noti->notifications,$icon,$noti->link,$user->id,$noti->id);
			}
		}
		return \Support::response([
			'code'=>200,
			'message'=>'Gửi thông báo mã giảm giá cho khách hàng thàng công'
		]);
	}
}
