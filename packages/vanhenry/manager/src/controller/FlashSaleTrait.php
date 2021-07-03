<?php 
namespace vanhenry\manager\controller;

use vanhenry\helpers\helpers\SettingHelper;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Auction;
use App\Models\FlashSale;
use App\Models\FlashSaleProduct;
use App\Models\FlashSaleTimeSlot;
use Carbon\Carbon;
use DB;

trait FlashSaleTrait{
	
	public function addProductToFlashSale(Request $request){
		if($request->array_product_id == null){
			return response()->json(['code'=>100,'message'=>'Vui lòng chọn sản phẩm muốn thêm vào chương trình']);
		}
		$itemFlashSaleProducts = FlashSaleProduct::where('flash_sale_id',$request->flash_sale_id)->get();

		$flashSale = FlashSale::find($request->flash_sale_id);

		$slotFlashSale = FlashSaleTimeSlot::find($flashSale->time_slot);
		
		if(count($request->array_product_id)> (int)$slotFlashSale->qty){
			return response()->json(['code'=>100,'message'=>'Số lượng sản phẩm trong flash sale tối đa là '.(int)$slotFlashSale->qty.' sản phẩm']);
		}
		if($itemFlashSaleProducts->count() + count($request->array_product_id) > (int)$slotFlashSale->qty){
			return response()->json(['code'=>100,'message'=>'Bạn đã chọn đủ '.(int)$slotFlashSale->qty.' sản phẩm trong chương trình này']);
		};
		$arrayItemFlashSale = [];
		foreach($request->array_product_id as $key => $id){
			$product = Product::find($id);
			$data=[];
			$data['product_id'] = $id;
			$data['flash_sale_id'] = $request->flash_sale_id;
			$data['qty'] =  $product->qty == null ? 1 : $product->qty;
			$data['limit'] =  1;
			$data['price'] =  $product->price_old !== null ? $product->price_old : $product->price;
			$data['act'] =  0;
			$data['created_at'] = new \DateTime();
			$data['updated_at'] = new \DateTime();
			$arrayItemFlashSale[] = $data;
		}

		DB::table('flash_sale_products')->insert($arrayItemFlashSale);
		return response()->json(['code'=>200,'message'=>'Thêm sản phẩm thành công','url'=>url()->previous()]);
	}

	public function changeAct(Request $request){
		$productFlashSale = FlashSaleProduct::where('flash_sale_id',$request->flash_sale_id)->where('product_id',$request->product_id)->first();
		$product = Product::find($request->product_id);
		if($productFlashSale->act == 1){
			$productFlashSale->act = 0;
			$productFlashSale->save();
			return response()->json(['code'=>200,'message'=>'Thay đổi trạng thái thành công']);
		}else{
			if(isset($request->price) && isset($request->qty) && isset($request->qty)){
				if((int)$product->price <= (int)$request->price){
					return response()->json(['code'=>100,'message'=>'Giá giảm không thể lớn hơn hoặc bằng giá gốc...']);
				}elseif($request->limit > $request->qty){
					return response()->json(['code'=>100,'message'=>'Số lượng tối đa không thể nhỏ hơn số lượng sản phẩm']);
				}else{
					$productFlashSale->act = 1;
					$productFlashSale->price = $request->price;
					$productFlashSale->qty = $request->qty;
					$productFlashSale->limit = $request->qty;
					$productFlashSale->save();
				}
			}else{
				if($product->price <= $productFlashSale->price){
					return response()->json(['code'=>100,'message'=>'Giá giảm không thể lớn hơn hoặc bằng giá gốc']);
				}else{
					$productFlashSale->act = 1;
					$productFlashSale->save();
				}
			}
			return response()->json(['code'=>200,'message'=>'Thay đổi trạng thái thành công']);
		}
	}

	public function editProductFlashSale(Request $request){
		
		foreach($request->array_id as $key => $id){
			$product = Product::find($id);
			$itemProductFlashSale = FlashSaleProduct::where('flash_sale_id',$request->flash_sale_id)->where('product_id',$id)->first();
			if($itemProductFlashSale->act == 1){
				if((int)$request->array_limit[$key] > (int)$request->array_qty[$key]){
					return response()->json(['code'=>100,'message'=>'Giới hạn sản phẩm không thể lớn hơn số lượng sản phẩm bán ra']);
				}
				if((int)$request->array_price[$key] >= (int)$product->price){
					return response()->json(['code'=>100,'message'=>'Số tiền giảm không thể bằng hoặc lớn hơn giá gốc của sản phẩm']);
				}

				$itemProductFlashSale->price = $request->array_price[$key];
				$itemProductFlashSale->qty = $request->array_qty[$key];
				$itemProductFlashSale->limit = $request->array_limit[$key];
				$itemProductFlashSale->updated_at = new \DateTime();
				$itemProductFlashSale->act = 1;
				$itemProductFlashSale->save();
			}
		}
		return response()->json(['code'=>200,'message'=>'Cập nhật sản phẩm thành công']);
	}    

	public function createFlashSale(Request $request){
		$checkValue = $this->checkValueCreate($request->all());

		if($checkValue !== true){
			return response()->json(['code'=>100,'message'=>$checkValue]);
		}
		if($request->time == 'undefined'){
			return response()->json(['code'=>100,'message'=>'Vui lòng chọn lại ngày']);
		}
		$time = FlashSaleTimeSlot::find($request->slot_time);
		$timeStart = $request->time.' '.date('H:i:s', strtotime($time->start_time));
		$timeEnd = $request->time.' '.date('H:i:s', strtotime($time->end_time));
		$flash_sale = new FlashSale();
		$flash_sale->start_at = $timeStart;
		$flash_sale->expired_at = $timeEnd;
		$flash_sale->act = 1;
		$flash_sale->time_slot = $request->slot_time;
		$flash_sale->save();
		$url = '/esystem/viewdetail/flash_sales/'.$flash_sale->id;
		return response()->json(['code'=>200,'message'=>'Tạo flash sale thành công','url'=>url($url)]);	
	}

	public function checkValueCreate(array $data){
		if(!isset($data['slot_time'])){
			return 'Hãy chọn khung giờ diễn ra flash sale';
		}
		if(!isset($data['time'])){
			return 'Hãy chọn ngày sale';
		}
		return true;
	}

	public function findSlotTime(Request $request){

		$now = strtotime(Carbon::now());
		$slot_times = FlashSaleTimeSlot::all();
		$time_slot = [];
		foreach($slot_times as $time){
			$data = $request->time.' '.date('H:i:s', strtotime($time->start_time));
			$timeData = strtotime($data);
			$flash_sale = FlashSale::where('start_at',$data)->first();
			if($flash_sale == null){
				if($timeData > $now){
					$time_slot[] = $time;
				}
			}
		}
		return view('vh::view.flashsale.flash_sale_slot_time',compact('time_slot'));
		
	}

	public function editFlashSale(Request $request){
		$checkValue = $this->checkValueCreate($request->all());

		if($checkValue !== true){
			return response()->json(['code'=>100,'message'=>$checkValue]);
		}
		$time = FlashSaleTimeSlot::find($request->slot_time);
		$timeStart = $request->time.' '.date('H:i:s', strtotime($time->start_time));
		$timeEnd = $request->time.' '.date('H:i:s', strtotime($time->end_time));

		$flash_sale = FlashSale::find($request->flash_sale_id);
		$flash_sale->start_at = $timeStart;
		$flash_sale->expired_at = $timeEnd;
		$flash_sale->time_slot = $request->slot_time;
		$flash_sale->save();
		return response()->json(['code'=>200,'message'=>'Sửa flash sale thành công']);	
	}

	public function deleteItem(Request $request){
		$arrayId = FlashSaleProduct::whereIn('product_id',$request->array_product_id)->where('flash_sale_id',$request->flash_sale_id)->delete();
		$count = FlashSaleProduct::where('flash_sale_id',$request->flash_sale_id)->get()->count();
		return response()->json(['code'=>200,'message'=>'Xóa sản phẩm thành công','count'=>$count]);
	}
}