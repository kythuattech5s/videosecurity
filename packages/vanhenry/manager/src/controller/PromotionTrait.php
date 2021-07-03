<?php 
namespace vanhenry\manager\controller;
use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\Product;
use App\Models\ProductPromotion;
use App\Models\PromotionTranslation;
use Carbon\Carbon;
use DB;
trait PromotionTrait{
	public function createPromotion(Request $request)
	{
		$startTime = Carbon::parse($request->start_at);
		$endTime = Carbon::parse($request->expired_at);
		$now = Carbon::parse(Carbon::now());

		if ($request->name == null) {
			return \Support::json(['code' => 100,'message'=>'Nhập tên chương trình']);
		}	
		if (strlen($request->name) > 150) {
			return \Support::json(['code' => 101,'message'=> 'Tên phải nhỏ hơn 150 ký tự']);
		}

		if($startTime->diffInMinutes($endTime) < 60 OR $startTime > $endTime){
			return \Support::json(['code'=> 100,'message'=> 'Chương trình phải kéo dài ít nhất là 1h kể từ khi bắt đầu']);
		}

		
		$promotion = $this->addPromotion($request);
		$this->createTranslation($promotion->id,$request);
		
		return response()->json(['code'=>200, 'message'=>'Thêm mới thành công chương trình','url'=>'/esystem/view/promotions']);
	}

	protected function addPromotion($request){
		$promotion = new Promotion;
		$promotion->start_at = date('Y-m-d H:i:s',strtotime($request->start_at));
		$promotion->expired_at = date('Y-m-d H:i:s',strtotime($request->expired_at));
		$promotion->act = 1;
		$promotion->created_at = new \DateTime;
		$promotion->updated_at = new \DateTime;
		$promotion->save();
		return $promotion;
	}

	protected function createTranslation($id,$request){
		$promotion_translation_vi = new PromotionTranslation();
		$promotion_translation_vi->language_code = 'vi';
		$promotion_translation_vi->map_id = $id;
		$promotion_translation_vi->name = $request->name;
		$promotion_translation_vi->save();

		$promotion_translation_en = new PromotionTranslation();
		$promotion_translation_en->language_code = 'en';
		$promotion_translation_en->map_id = $id;
		$promotion_translation_en->name = $request->name;
		$promotion_translation_en->save();
	}

	public function addItemPromotion(Request $request){
		if(isset($request->array_product_id) == false){
			return response()->json(['code'=>100,'message'=>'Vui lòng chọn sản phẩm muốn thêm vào']);
		};
		$data = [];
		foreach($request->array_product_id as $key => $id){
			$product = Product::find($id);
		    $item = [];
		    $item['product_id'] = $id;
		    $item['price'] = $product->price_old !== null ? $product->price_old : $product->price;
		    $item['limit'] = 1;
		    $item['act'] = 0;
		    $item['promotion_id'] = $request->promotion_id;
		    $item['created_at'] = new \DateTime;
		    $item['updated_at'] = new \DateTime;
		    $data[] = $item;
		}
		DB::table('product_promotion')->insert($data);
		return response()->json(['code'=>200,'message'=>'Sản phẩm đã được thêm vào chương trình của tôi','url'=>url('/esystem/viewdetail/promotions/'.$request->promotion_id)]);
	 }

	public function editItemPromotion(Request $request){
		
		foreach($request->array_id as $key => $id){
			$product = Product::find($id);

			if($product->price_old <= (int)$request->array_price[$key]){
				return response()->json(['code'=>100,'message'=>'Giá giảm đang bằng hoặc lớn hơn giá gốc của sản phẩm']);
			}
			if($request->array_limit[$key] < 1){
				return response()->json(['code'=>100,'message'=>'Số lượng mua tối thiểu phải bằng 1']);
			}

			$item = ProductPromotion::where('promotion_id',$request->promotion_id)->where('product_id',$id)->first();
			$item->price = $request->array_price[$key];
			$item->limit = $request->array_limit[$key];
			$item->act = 1;
			$item->save();
		}
		return response()->json(['code'=>200,'message'=>"Cập nhật sản phẩm thành công",'url'=>url('/esystem/viewdetail/promotions/'.$request->id)]);
	}

	public function changeActProduct(request $request){
		$item = ProductPromotion::where('promotion_id',$request->promotion_id)->where('product_id',$request->product_id)->first();
		if($item->act == 1){
			$item->act = 0;
			$item->save();
			return response()->json(['code'=>200,'message'=>'Thay đổi trạng thái thành công']);
		}else{
			$product = Product::find($request->product_id);
			if(isset($request->price) && isset($request->limit)){
				if($request->price >= $product->price_old){
					return response()->json(['code'=>100,'message'=>'Giá giảm đang bằng hoặc lớn hơn giá gốc của sản phẩm']);
				}elseif($request->limit < 1){
					return response()->json(['code'=>100,'message'=>'Số lượng mua tối thiểu phải lớn hơn hoặc bằng 1']);
				}else{
					$item->act = 1;
					$item->price = $request->price;
					$item->limit = $request->limit;
					$item->save();
				}
			}else{
				if($product->price_old <= $item->price){
					return response()->json(['code'=>100,'message'=>'Giá giảm đang bằng hoặc lớn hơn giá gốc của sản phẩm']);
				}
				if($item->limit < 1){
					return response()->json(['code'=>100,'message'=>'Số lượng mua tối thiểu phải lớn hơn hoặc bằng 1']);
				}
				$item->act = 1;
				$item->save();
			}
			return response()->json(['code'=>200,'message'=>'Thay đổi trạng thái thành công']);
		}

	}	
	public function deleteItemPromotion(Request $request){
		$item = ProductPromotion::where('promotion_id',$request->promotion_id)->whereIn('product_id',$request->array_product_id)->delete();
		$count = ProductPromotion::where('promotion_id',$request->promotion_id)->get()->count();
		return response()->json(['code'=>200,'message'=>'Xóa sản phẩm thành công','count'=>$count]);
	}

	public function deletePromotion(Request $request){
		$promotion = Promotion::find($request->promotion_id)->delete();
		$promotionProdcut = ProductPromotion::where('promotion_id',$request->promotion_id)->delete();

		return response()->json(['code'=>200,'message'=>'Đã xóa thành công chương trình','url'=>'/esystem/view/promotions']);
	}

	public function checkTimeEdit($request){
		if (strlen($request->name) > 150) {
			return 'Tên phải nhỏ hơn 150 ký tự';
		}
		$startTime = Carbon::parse($request->start_at);
		$endTime = Carbon::parse($request->expired_at);
		if($request->action == 'edit'){
			$voucher = Promotion::find($request->id);
			$voucherStartTime = Carbon::parse($voucher->start_at);
			$voucherEndTime = Carbon::parse($voucher->expired_at);
			if ($voucherStartTime < Carbon::now() && $voucherEndTime > Carbon::now()) {
				if($startTime->diffInMinutes($voucherStartTime) !== 0 OR $endTime->diffInMinutes($voucherEndTime) !== 0){
					return 'Chương trình hiện đang diễn ra bạn không được thay đổi thời gian';
				}
			}else{
				if(Carbon::now()>$voucherEndTime){
					return 'Chương trình đã kết thúc bạn có thể tạo 1 chương trình mới';
				}
				return true;
			}
			if ($startTime > $endTime) {
				return 'Thời gian bắt đầu không thể muộn hơn thời gian kết thúc';
			}
			if ($startTime->diffInMinutes($endTime) < 60) {
				return 'Chương trình phải kéo dài ít nhất là 1h kể từ khi bắt đầu';
			}
		}
		return true;
	}
}