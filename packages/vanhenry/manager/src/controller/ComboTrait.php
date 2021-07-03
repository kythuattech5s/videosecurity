<?php 
namespace vanhenry\manager\controller;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Combo;
use App\Models\ComboProduct;
use App\Models\ComboTranslation;
use DB;

trait ComboTrait{
	
	protected $type_combo_money = 1;
    protected $type_combo_percen = 2;

    public function createComboEvent(Request $request){
        $validator = $this->validateCombo($request->all());
        if ($validator->fails()) {
            return response()->json([
                'code' => 100,
                'message' => $validator->errors()->first(),
            ]);
        }
        $checkVal = $this->checkVal($request);
        if($checkVal !== true){
            return response()->json(['code'=>100,'message'=>$checkVal]);
        }
        $combo = $this->createCombo($request);
        $createNameTranslation = $this->createNameTranslation($request,$combo);
        return response()->json(['code'=>200,'message'=>'Tạo combo khuyến mãi thành công','url'=>url('/esystem/view/combos')]);
    }

    protected function validateCombo(array $data){
        return \Validator::make($data, [
            'name' => 'required',
            'start_at' => 'required',
            'expired_at' => 'required',
            'type' => 'required',
            'buy_min' => 'required',
            'discount' => 'required',
            'limit' => 'required',
        ],[
            'required' => ':attribute đang thiếu',
            'max' => ':attribute content_max :max',
        ],[
            'name' => 'Tên combo khuyến mãi',
            'start_at' => 'Thời gian bắt đầu',
            'expired_at' => 'Thời gian kết thúc',
            'type' => 'Kiểu combo',
            'buy_min' => 'Số lượng',
            'discount' => 'Mức giảm',
            'limit' => 'Tối đa',
        ]);
    }

    protected function checkVal($request){
        $now = strtotime(Carbon::now('Asia/Ho_Chi_Minh'));
        $start_at = strtotime($request->start_at);
        $expired_at = strtotime($request->expired_at);
        if($start_at < $now){
            return 'Thời gian bắt đầu chương trình nhỏ hơn thời gian hiện tại';
        }
        if($start_at > $expired_at){
            return 'Thời gian bắt đầu chương trình đã hơn thời gian kết thúc';
        }
        if((int)$request->type == $this->type_combo_percen  && (int)$request->discount > 30 && (int)$request->discount < 1){
            return 'Giá sản phẩm sau khuyến mãi đang thấp hơn 70 % giá gốc.';        
        }
        if((int)$request->type == $this->type_combo_money && (int)$request->discount > 100000000 && (int)$request->discount < 1000){
            return 'Giá đã vượt quá giá trị tối đa 100.000.000 VND';
        }
        if((int)$request->buy_min < 2){
            return 'Số sản phẩm trong combo không thể ít hơn 2';
        }
        if((int)$request->limit < 1){
            return 'Số lượt mua không thể ít hơn 1';
        }
        return true;
    }

    protected function createCombo($request){
        $combo = new Combo();
        $combo->act = 1;
        $combo->name = $request->name;
        $combo->start_at = date('Y-m-d H:i:s',strtotime($request->start_at));
        $combo->expired_at = date('Y-m-d H:i:s',strtotime($request->expired_at));
        $combo->type = (int)$request->type;
        $combo->buy_min = (int)$request->buy_min;
        $combo->discount = (int)$request->discount;
        $combo->limit = (int)$request->limit;
        $combo->save();
        return $combo;
    }


    public function addItemToCombo(Request $request){
        if($request->array_product_id == null){
            return response()->json(['code'=>100,'message'=>'Vui lòng chọn sản phẩm']);
        }
        $combo_id = (int)$request->combo_id;
        $checkCombo = $this->checkCombo($request);
        if($checkCombo !== true){
            return response()->json(['code'=>100,'message'=>$checkCombo]);
        }

        $data = [];
        foreach((array)$request->array_product_id as $id){
            $item = [];
            $item['product_id'] = (int)$id;
            $item['combo_id'] = (int)$combo_id;
            $item['act'] = 1;
            $item['created_at'] = new \DateTime;
            $item['updated_at'] = new \DateTime;
            $data[] = (array)$item;
        }
        DB::table('combo_product')->insert($data);
        return response()->json(['code'=>200,'message'=>'Sản phẩm đã được thêm vào combo','url'=>url('/esystem/viewdetail/combos/'.$combo_id)]);
    }

    public function checkCombo($request){
        $combo_id = (int)$request->combo_id;
        $combo = Combo::where('act',1)->where('id',$combo_id)->first();
        if($combo == null){
            return 'Combo hiện tại không được kích hoạt';
        }elseif(new \DateTime($combo->expired_at) < new \DateTime){
            return 'Combo đã hết hạn';
        }

        // $priceAllProduct = 0;
        // $qtyAllProduct += count($request->array_product_id);

        // $qtyAllProduct = 0;
        // foreach($request->array_product_id as $id){
        //     $product = \App\Models\Product::select('price_old','id')->where('id',$id)->first();
        //     $priceAllProduct += $product->price_old;
        // }

        // $productIdInCombo = ComboProduct::with('product')->where('combo_id',$combo_id)->get();
        // if($productIdInCombo->count() !== 0){
        //     $priceAllProduct += $productIdInCombo->sum('product.price_old');
        //     $qtyAllProduct += $productIdInCombo->count();
        // }

        // if($combo->type == $this->type_combo_money){
        //     if($priceAllProduct < $combo->discount){
        //         return 'Tổng giá tiền của các sản phẩm trong combo phải lơn hơn giá khuyến mãi';
        //     }
        // }

        // if($combo->limit < $qtyAllProduct){
        //     return 'Tổng số sản phẩm không thể nhỏ hơn số lượng ít nhất của combo'
        // }
        
        return true;

    }

    public function deleteItemInCombo(Request $request){
        $item = ComboProduct::where('combo_id',(int)$request->combo_id)->whereIn('product_id',(array)$request->array_product_id)->delete();
        $count = ComboProduct::where('combo_id',(int)$request->combo_id)->get()->count();
        return response()->json(['code'=>200,'message'=>'Xóa sản phẩm thành công','count'=>$count]);
    }


    public function checkTimeEditCombo($request){
        $startTime = Carbon::parse($request->start_at);
        $endTime = Carbon::parse($request->expired_at);

        if($request->action == 'edit'){
            $combo = Combo::find($request->id);
            $voucherStartTime = Carbon::parse($combo->start_at);
            $voucherEndTime = Carbon::parse($combo->expired_at);

            if ($startTime > $endTime) {
                return 'Thời gian bắt đầu không thể muộn hơn thời gian kết thúc';
            }

            if ($startTime->diffInMinutes($endTime) < 60) {
                return 'Chương trình phải kéo dài ít nhất là 1h kể từ khi bắt đầu';
            }
            
            if ($voucherStartTime < Carbon::now() && $voucherEndTime > Carbon::now()) {
                if($startTime->diffInMinutes($voucherStartTime) !== 0 
                    OR $endTime->diffInMinutes($voucherEndTime) !== 0 
                    OR $request->type !== $combo->type 
                    OR $request->buy_min !== $combo->buy_min 
                    OR $request->discount !== $combo->discount 
                    OR $request->limit !== $combo->limit)
                {
                    return 'Chương trình hiện đang diễn ra';
                }

            }else{
                if(Carbon::now()>$voucherEndTime){
                    return 'Chương trình đã kết thúc bạn có thể tạo 1 chương trình mới';
                }
                return true;
            }

        }
        return true;
    }
}