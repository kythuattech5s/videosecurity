<?php
namespace vanhenry\manager\controller;
use Illuminate\Http\Request;
use App\Models\PreOrder;
use App\Models\PreOrderPaymentPaypal;
use App\Models\PreOrderPaymentVnpay;
use App\Models\TokenCheckOutPreOrder;
use App\Models\Product;
use vanhenry\helpers\helpers\SettingHelper;
use Hash;
trait PreOrderTrait{
	public function getDetailPreOder(Request $request){
		$pre_order = PreOrder::with(['product'=>function($q){
			$q->translation();
		}])->where('id',$request->pre_order_id)->first();
		return response()->json(['html'=>view('vh::view.orders.modal_pre_order',compact('pre_order'))->render()]);
	}

	public function sentMailHaveProduct(request $request){
		$pre_order = PreOrder::with(['product'=>function($q){
			$q->translation();
		}])->where('id',$request->pre_order_id)->first();

		$infoMails = $this->infoSendMail($pre_order);
		if (count($infoMails) > 0) {
            try{
                \Mail::to($infoMails['email'])->send(new \App\Notifications\AuctionMail($infoMails['subject'], $infoMails['html']));    
            }
            catch(\Exception $ex){

            }
        }

		$pre_order->notification_have_product = 2;
		$pre_order->save();
		return response()->json(['code'=>200,'message'=>'Đã gửi thông báo cho khách hàng']);
	}

	public function infoSendMail(PreOrder $pre_order){
        $keyToken = uniqid().$pre_order['id'];
        $token = new TokenCheckOutPreOrder();
        $token->token = Hash::make($keyToken);
        $token->pre_order_id = $pre_order['id'];
        $token->save();
        $auctionTemplate = view('templates.pre_orders.had_product', compact('pre_order','token'))->render();
        
        $overviewTemplate = $auctionTemplate;
        if ($pre_order['language_pre_order'] == 'vi') {
            $subject = 'Thông báo sản phẩm '.\Support::getProductNameLanguage($pre_order['product_id'],$pre_order['language_pre_order']).' có hàng';
        }
        else{
            $subject = 'Notice that '.\Support::getProductNameLanguage($pre_order['product_id'],$pre_order['language_pre_order']).' products are in stock';
        }
        return ['subject' => $subject, 'html' => $overviewTemplate,'email'=>$pre_order['email']];
    }

    
}
?>