<?php
namespace vanhenry\manager\controller;
use Illuminate\Http\Request;
use App\Models\{UserAgency,UserTechnician,OrderUserAgency,OrderUserTechnician,OrderPaymentGhtk,OrderPaymentGhn,Order,Notification,User,TokenNotification,UserAgencyShootOrder,AgencyService,TechnicianService,ReasonRefundOrder, Depot, Carrier,AskQuestionOrder,Acceptance};
use vanhenry\helpers\helpers\SettingHelper;
use App\Notifications\OrderSuccess;
use Mail;
trait OrderTrait{
	public function getDetailOrder(Request $request){
		$order = Order::with(['orderProducts'=>function($q){
			$q->with(['product','combo','flash_sale']);
		},'agencies',
		'technicians',
		'reasonRefund',
		'technicianOrderService',
		'depot',
		'userTechnicianRefusedOrder',
		'agencyService',
		'technicianService',
		'carrier',
		'agencyOrderService',
		'userCollaborator',
		'voucher',
		'paymentMethod',
		'products',
		'technicians',
		'agencies',
		'reasonCancel',
		'agency',
		'orderShoot',
		'technician',
		'getWards',
		'getDistrict',
		'getProvince',
		'user',
		'userAgencyRefusedOrder'
		])->where('id',$request->order_id)->first();
		return response()->json([
			'html'=>view('vh::view.orders.modal',compact('order'))->render()
		]);
	}

	public function showAgency(Request $request){
		$order = Order::find($request->order_id);

		$agencies = UserAgency::with(['OrderUserAgency'=>function($q) use($request){
			$q->where('order_id',$request->order_id)->with('service');
		}])->where('user_id','!=',$order->user_id)->get();

		$services = AgencyService::all();

		return response()->json(['html'=>view('vh::view.orders.modal_pick_agency',compact('agencies','services'))->render()]);
	}

	public function showTechnician(Request $request){
		$order = Order::with('agencies')->find($request->order_id);
		if($order->agency_id !== 0){
			$user_agency_id = UserAgency::find($order->agency_id)->user_id;
			$technicians = UserTechnician::with(['OrderUserTechnician'=>function($q) use($request){
				$q->where('order_id',$request->order_id)->with('service');
			}])->select('id','name')->where('user_id','!=',$user_agency_id)->get();
		}else{
			$technicians = UserTechnician::select('id','name')->get();
		}

		$services = TechnicianService::all();
		if($order->agency_service_id == \OrderHelper::AGENCY_PICK_UP_TRANSPORT){
			if($order->status == \OrderHelper::STATUS_WAIT_INSTALL){
				$orderUserTechnician = OrderUserTechnician::where('order_id',$order->id)->where('service_technician_id','!=',\OrderHelper::TECHNICIAN_INSTALLATION)->get();
				if($orderUserTechnician->count()>0){
					$orderUserTechnician->delete();
				}
				$services = TechnicianService::where('id',\OrderHelper::TECHNICIAN_INSTALLATION)->get();
			}
			$services = TechnicianService::where('id',\OrderHelper::TECHNICIAN_INSTALLATION)->get();
		}

		if($order->agency_service_id == \OrderHelper::AGENCY_PICK_UP ){
			if($order->status == \OrderHelper::STATUS_PENDING_CONFIRM){
				$orderUserTechnician = OrderUserTechnician::where('order_id',$order->id)->where('service_technician_id','!=',\OrderHelper::TECHNICIAN_INSTALLATION_TRANSPORT)->get();
				if($orderUserTechnician->count()>0){
					$orderUserTechnician->delete();
				}
				$services = TechnicianService::where('id',\OrderHelper::TECHNICIAN_INSTALLATION_TRANSPORT)->get();
			}
		}

		return response()->json(['html'=>view('vh::view.orders.modal_pick_technician',compact('technicians','services'))->render()]);
	}

	public function confirmAgency(Request $request){
		if(isset($request->agency)){
			if(count($request->agency) !== count(array_filter($request->agency_service_id))){
				return \Support::response([
					'code'=>100,
					'message'=>'Vui l??ng ch???n ?????i l?? v?? d???ch v??? c???a ?????i l??'
				]);
			}
			foreach($request->agency as $key => $agency_id){
				$order = OrderUserAgency::where('order_id',$request->order_id)->where('user_agency_id',$agency_id)->first();
				if($order == null){
					$orderAgency = new OrderUserAgency;
					$orderAgency->order_id = $request->order_id;
					$orderAgency->user_agency_id = $agency_id;
					$orderAgency->service_agency_id = array_values(array_filter($request->agency_service_id))[$key];
					$orderAgency->save();
				}else{	
					$order->service_agency_id = array_values(array_filter($request->agency_service_id))[$key];
					$order->save();
				}
			}
			$order = OrderUserAgency::whereNotIn('user_agency_id',$request->agency)->delete();
		}else{
			$order = OrderUserAgency::where('order_id',$request->order_id)->delete();
		}
		return response()->json(['code'=>200,'message'=>'Ch???n th??nh c??ng']);
	}

	public function confirmTechnician(Request $request){
		$order = Order::with('agencies')->find($request->order_id);
		if($order->agencies->count()>0 && $order->agency_id == 0 && $order->agency_service_id == 0){
			return \Support::response([
				'code'=>100,
				'message'=>'Vui l??ng ch??? ?????i l?? x??c nh???n ????n h??ng tr?????c khi ch???n d???ch v??? cho k??? thu???t vi??n'
			]);
		}
		
		if(isset($request->technician)){
			if(count($request->technician) !== count(array_filter($request->technician_service_id))){
				return \Support::response([
					'code'=>100,
					'message'=>'Vui l??ng ch???n ?????i l?? v?? d???ch v??? c???a ?????i l??'
				]);
			}
			foreach($request->technician as $key => $technician_id){
				$order = OrderUserTechnician::where('order_id',$request->order_id)->where('user_technician_id',$technician_id)->first();
				
				if($order == null){
					$orderTechnician = new OrderUserTechnician;
					$orderTechnician->order_id = $request->order_id;
					$orderTechnician->user_technician_id = $technician_id;
					$orderTechnician->service_technician_id = array_values(array_filter($request->technician_service_id))[$key];
					$orderTechnician->save();
				}else{
					$order->service_technician_id = array_values(array_filter($request->technician_service_id))[$key];
					$order->save();
				}
			}
			$order = OrderUserTechnician::whereNotIn('user_technician_id',$request->technician)->delete();
		}else{
			$order = OrderUserTechnician::where('order_id',$request->order_id)->delete();
		}
		return response()->json(['code'=>200,'message'=>'Ch???n th??nh c??ng']);
	}

	public function activeOrder(Request $request){
		$order = Order::with('agencies')->find($request->order_id);
		if($order->agencies->count() == 0){
			return response(['code'=>100,'message'=>'Vui l??ng ch???n ?????i l?? mu???n chuy???n ????n']);
		}
		$order->act = 1;
		$order->updated_at = new \DateTime();
		
		$users = OrderUserAgency::where('order_id',$order->id)->pluck('user_agency_id');
		$icon = url(json_decode(SettingHelper::getSetting('logo'))->path.json_decode(SettingHelper::getSetting('logo'))->file_name);
		if($order->save()){
			foreach($users as $user_id){
				//Th??ng b??o cho ?????i l??
				$noti = new Notification;
				$noti->notifications = "Ch??o ".UserAgency::with('user')->find($user_id)->user->name."! Eco248 v???a c???p nh???t ????n h??ng m???i cho b???n";
				$noti->watched = 0;
				$noti->user_id = UserAgency::find($user_id)->user_id;
				$noti->type = \NotificationConstant::TYPE_ORDER;
				$noti->noti_for = \NotificationConstant::FOR_AGENCY;
				$noti->title = 'C?? ????n h??ng m???i h??y x??c nh???n ngay!';
				$noti->link = \VRoute::getWithLanguageFull('detail-order-agency').'/'.$order->id;
				$noti->save();
				$count = Notification::where('user_id',$user_id)->where('watched',0)->get()->count();
				if(TokenNotification::where('user_id',UserAgency::find($user_id)->user_id)->first() !== null){
					$noti->singleUser(TokenNotification::where('user_id',UserAgency::find($user_id)->user_id)->first()->token,$noti->title,$noti->notifications,$icon,$noti->link,$user_id,$noti->id,Notification::NOTIFICAITON_SHOW_POPUP_ORDER);
				}
			}
		}
		return response()->json(['code'=>200,'message'=>'Chuy???n ????n h??ng th??nh c??ng']);
	}

	// public function transferTechnician(Request $request){
	// 	$order = Order::with('technicians','agencies')->find($request->order_id);
		
	// 	if($order->agencies->count()>0 && $order->agency_id == 0){
	// 		return \Support::response([
	// 			'code' => 100,
	// 			'message' => 'Ch??a c?? ?????i l?? n??o x??c nh???n ????n h??ng'
	// 		]);
	// 	};

	// 	if($order->technicians->count() == 0){
	// 		return response(['code'=>100,'message'=>'Vui l??ng ch???n k??? thu???t vi??n']);
	// 	}

	// 	$order->act = 1;
	// 	$order->updated_at = new \DateTime();
	// 	$order->save();

	// 	$orderShoot = new UserAgencyShootOrder();
	// 	$orderShoot->user_agency_id = $order->agency_id;
	// 	$orderShoot->order_id = $request->order_id;

	// 	$users = OrderUserTechnician::where('order_id',$request->order_id)->pluck('user_technician_id');
	// 	$userIds = UserTechnician::whereIn('id',$users)->pluck('user_id');
		
	// 	$icon = url(json_decode(SettingHelper::getSetting('logo'))->path.json_decode(SettingHelper::getSetting('logo'))->file_name);

	// 	if($order->agency_id !== 0){
	// 		$user_id_of_agency = UserAgency::find($order->agency_id)->user_id;
	// 		if(in_array($user_id_of_agency,$userIds->toArray())){
	// 			return \Support::response(['code'=>100,'message'=>'Vui l??ng ki???m tra l???i. Kh??ng th??? ch???n k??? thu???t vi??n v?? ?????i l?? c??ng m???t t??i kho???n']);
	// 		}
	// 	}
		
	// 	if($orderShoot->save()){
	// 		foreach($users as $user_id){
	// 			//Th??ng b??o cho k??? thu???t vi??n
	// 			$noti = new Notification;
	// 			$noti->notifications = "Ch??o ".UserTechnician::find($user_id)->name."! Eco248 v???a chuy???n 1 ????n h??ng m???i cho b???n";
	// 			$noti->watched = 0;
	// 			$noti->user_id = UserTechnician::find($user_id)->user_id;
	// 			$noti->type = \NotificationConstant::TYPE_ORDER;
	// 			$noti->noti_for = \NotificationConstant::FOR_TECHNICIAN;
	// 			$noti->title = 'C?? ????n h??ng m???i h??y x??c nh???n ngay!';
	// 			$noti->link = \VRoute::getWithLanguageFull('detail-order-technician').'/'.$order->id;
	// 			$noti->save();
	// 			$count = Notification::where('user_id',$user_id)->where('watched',0)->get()->count();
	// 			if(TokenNotification::where('user_id',UserTechnician::find($user_id)->user_id)->first() !== null){
	// 				$noti->singleUser(TokenNotification::where('user_id',UserTechnician::find($user_id)->user_id)->first()->token,$noti->title,$noti->notifications,$icon,$noti->link,UserTechnician::find($user_id)->user_id,$noti->id);
	// 			}
	// 		}
	// 	}
	// 	return \Support::response([
	// 		'code'=>200,
	// 		'message'=>'Chuy???n ????n h??ng th??nh c??ng'
	// 		]);	
	// }

	public function shootToTechnician(Request $request)
	{
		$technicians = (array)$request->technical;
		$service = (int)$request->service;
		$depot = (int)$request->depot;
		$order = (int)$request->order;

		if (count($technicians) == 0) {
			return \Support::response([
				'code'=>100,
				'message'=>'B???n ch??a ch???n k??? thu???t vi??n'
			]);
		}

		if ($service == 0) {
			return \Support::response([
				'code'=>101,
				'message'=>'B???n ch??a ch???n c??ng vi???c cho k??? thu???t vi??n'
			]);
		}

		if ($order == 0) {
			return \Support::response([
				'code'=>102,
				'message'=>'Thi???u th??ng tin ????n h??ng'
			]);
		}

		$orderUserTechnicians = [];
		foreach ($technicians as $key => $technician) {
			$orderUserTechnicians[$key]['order_id'] = $order;
			$orderUserTechnicians[$key]['user_technician_id'] = $technician;
			$orderUserTechnicians[$key]['created_at'] = new \DateTime;
			$orderUserTechnicians[$key]['updated_at'] = new \DateTime;
			$orderUserTechnicians[$key]['service_technician_id'] = $service;
		}

		OrderUserTechnician::where('order_id', $order)->whereIn('user_technician_id', $technicians)->delete();
		$ins = (new OrderUserTechnician)->insert($orderUserTechnicians);

		if ($ins == false) {
			if ($order == 0) {
				return \Support::response([
					'code'=>103,
					'message'=>'Kh??ng th??? th??m k??? thu???t vi??n'
				]);
			}			
		}

		if ($depot > 0) {
			\DB::table('depot_order')->where('order_id', $order)->delete();
			\DB::table('depot_order')->insert([
				'order_id' => $order,
				'depot_id' => $depot,
				'created_at' => new \DateTime,
				'updated_at' => new \DateTime
			]);
		}

		$orderObject = Order::find($order);
		$orderObject->updated_at = new \DateTime;
		$orderObject->technician_service_id = $service;

		if($orderObject->agency_id !== 0 && $orderObject->agency_service_id == 2 && $orderObject->status <= \OrderHelper::STATUS_DELIVERED){
			$orderObject->status = \OrderHelper::STATUS_WAIT_INSTALL;
		}
		$orderObject->save();

		if($orderObject->agency_id !== 0){
			$orderShoot = new UserAgencyShootOrder();
			$orderShoot->user_agency_id = $orderObject->agency_id;
			$orderShoot->order_id = $orderObject->id;
			$orderShoot->save();
		}
		
		$icon = url(SettingHelper::getSettingImage('logo'));
		foreach($technicians as $technician){
			//Th??ng b??o cho k??? thu???t vi??n
			$noti = new Notification;
			$noti->notifications = "Ch??o ".UserTechnician::find($technician)->name."! Eco248 v???a chuy???n 1 ????n h??ng m???i cho b???n";
			$noti->watched = 0;
			$noti->user_id = UserTechnician::find($technician)->user_id;
			$noti->type = \NotificationConstant::TYPE_ORDER;
			$noti->noti_for = \NotificationConstant::FOR_TECHNICIAN;
			$noti->title = 'C?? ????n h??ng m???i h??y x??c nh???n ngay!';
			$noti->link = \VRoute::getWithLanguageFull('detail-order-technician').'/'.$order;
			$noti->save();
			$count = Notification::where('user_id',$technician)->where('watched',0)->get()->count();
			if(TokenNotification::where('user_id',UserTechnician::find($technician)->user_id)->first() !== null){
				$noti->singleUser(TokenNotification::where('user_id',UserTechnician::find($technician)->user_id)->first()->token,$noti->title,$noti->notifications,$icon,$noti->link,UserTechnician::find($technician)->user_id,$noti->id,Notification::NOTIFICAITON_SHOW_POPUP_ORDER);
			}
		}

		return \Support::response([
			'code'=>200,
			'message'=>'Th??nh c??ng'
		]);
	}

	public function agreeRefund(Request $request){
		$orderReason = ReasonRefundOrder::where('order_id',$request->order_id)->first();
		if($request->type == 1){
			$orderReason->confirmation = $request->type;
			$orderReason->save();
			return \Support::response([
				'code'=>200,
				'message'=>'Y??u c???u ?????i tr??? ???? ???????c x??c nh???n'
			]);
		}

		if($request->type == 2){
			$orderReason->confirmation = $request->type;
			$orderReason->save();
			return \Support::response([
				'code'=>100,
				'message'=>'???? t??? ch???i y??u c???u ?????i tr???', 
			]);
		}
	}

	public function shootToCarrier(Request $request)
	{
		$agency = (int)$request->agency_pick_up;
		$depot = (int)$request->depot_pick_up;
		$carrier = (int)$request->carrier;
		$order = (int)$request->order;

		$place = null;
		if ($agency > 0) {
			$place = UserAgency::where('act', 1)->where('id', $agency)->first();
		}
		
		if ($depot > 0) {
			$place = Depot::where('act', 1)->where('id', $depot)->first();
		}
		
		if ($place == null) {
			return \Support::response([
				'code'=>100,
				'message'=>'Thi???u th??ng tin ?????a ??i???m l???y h??ng', 
			]);
		}

		$carrier = Carrier::where('act', 1)->where('id', $carrier)->first();
		if ($place == null) {
			return \Support::response([
				'code'=>101,
				'message'=>'H??ng v???n chuy???n kh??ng t???n t???i', 
			]);
		}

		$order = Order::find($order);

		// ?????a ??i???m l???y h??ng
		$pickUpProvince = $place->province;
		$pickUpDistrict =	$place->district;
		$pickUpWards = $place->wards;

		// ?????a ??i???m kh??ch h??ng
		$customerProvince = $order->getWards;
		$customerDistrict = $order->getDistrict;
		$customerWards = $order->getWards;

		return \Support::response([
			'code'=>102,
			'message'=>'Ch??a h??? tr??? h??ng v???n chuy???n', 
		]);
	}

	public function repAskQuestion(Request $request){
		if(strlen($request->content) == 0){
			return \Support::response([
				'code'=>100,
				'message'=>'Vui l??ng th??m c??u tr??? l???i'
			]);
		}

		$ask = AskQuestionOrder::with('user')->find($request->ask_id);

		$view = 'mail_templates.ask_question';
		$email = $ask->user->email;
		$subject = 'Tr??? l???i c??u h???i';
		$data = [];
		$data['question'] = $ask->content;
		$data['rep_question'] = $request->content;

		\Support::sendMail($view,$subject,$email,$data);

		$ask->act = 1;
		$ask->save();
		
		return \Support::response([
			'code'=>200,
			'message'=>'G???i c??u tr??? l???i th??nh c??ng'
		]);
	}

	public function filterImgAcceptance(Request $request){
		$datetime = explode('/',$request->date);
		$dateFind = $datetime[2].'-'.$datetime[1].'-'.$datetime[0];
		$imgs = Acceptance::where('order_id',$request->order_id)->whereDate('date',$dateFind)->orderBy('id','DESC')->first();
		$output = '';
		if($imgs !== null){
			$imgs = json_decode($imgs->imgs);
			foreach($imgs as $img){
				$output .='<a href="'.$img->path.$img->file_name.'" class="fancybox" rel="group">';
				$output .='	<img src="'.$img->path.$img->file_name.'">';
				$output .=' </a>';
			}
		}else{
			$output .= '<p>Kh??ng c?? ???nh nghi???m thu n??o</p>'; 
		}
		
		return \Support::response([
			'code'=>200,
			'message' => 'L???c s???n ph???m th??nh c??ng',
			'html' => $output
		]);
	}

	public function saveInfoOrder(Request $request){
		$order = Order::find($request->order_id);
		if($order == null){
			return \Support::response([
				'code'=>100,
				'message' => '????n h??ng kh??ng t???n t???i',
			]);
		}

		$date = explode('/',$request->scheduled_installation_date);
		$date = $date[0].'-'.$date[1].'-'.$date[2];
		$order->scheduled_installation_date = $date;
		$order->installation_fee = $request->installation_fee;
		$order->save();

		return \Support::response([
			'code'=>200,
			'message' => 'L??u c??i ?????t th??nh c??ng',
		]);
	}

}