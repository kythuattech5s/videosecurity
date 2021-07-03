<?php 

namespace vanhenry\manager\controller;

use App\Models\{UserTechnician, Order};
use Illuminate\Http\Request;


trait AnTrait {
	public function anView(Request $request)
	{
		echo '<pre>'; var_dump(\App\Models\FlashSaleTimeSlot::all()); die(); echo '</pre>';
		$orderId = $request->order_id;
		$order = Order::find($orderId);

		// order đã được đại lý nào xác nhận hay chưa
		$technicians = UserTechnician::where('act', 1)->with('user')->first();

		return view('vh::an.view', compact('technicians'));
	}
}