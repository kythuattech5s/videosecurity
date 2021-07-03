<?php
namespace App\Helpers;
use DB;
use Carbon\Carbon;
use App\Models\Menu;
use App\Models\MenuCategory;
use App\Helpers\Media;
use App\Models\Comment;
use App\Models\Province;
use App\Models\District;
use App\Models\Product;
use App\Models\Ward;
use App\Models\UserVoucher;
use App\Models\OrderPaymentGhn;
use App\Models\OrderPaymentGhtk;
use App\Models\UserProductFavourite;
use App\Models\ComboTranslation;
use App\Models\Color;
use App\Helpers\Carriers\GiaoHangNhanh;
use Auth;
use vanhenry\helpers\helpers\SettingHelper;
use App\Helpers\Order\Order as OrderHelper;
use App\Helpers\Mobile_Detect as MobileDetect;
use Currency;
use WebPConvert\WebPConvert;
class Support
{
	public static function isDateTime($string, $format = 'Y-m-d H:i:s')
	{
		return \DateTime::createFromFormat($format, $string);
	}
	public static function showDateTime($string, $format = 'H:i d-m-Y')
	{
		if (self::isDateTime($string)) {
			return Carbon::parse($string)->format($format);
		}
	}
	public static function showDateName($string,$format = 'l d-m-Y')
	{
		if (self::isDateTime($string)) {
			$stringName = Carbon::parse($string)->format($format);
			return self::dateNameToVi($stringName);
		}
	}
	public static function dateNameToVi($string){
		if(is_int(strpos($string,'Monday'))){
			$day = str_replace('Monday','Thứ 2',$string);
		}elseif(is_int(strpos($string,'Tuesday'))){
			$day = str_replace('Tuesday','Thứ 3',$string);
		}elseif(is_int(strpos($string,'Wednesday'))){
			$day = str_replace('Wednesday','Thứ 4',$string);
		}elseif(is_int(strpos($string,'Thursday'))){
			$day = str_replace('Thursday','Thứ 5',$string);
		}elseif(is_int(strpos($string,'Friday'))){
			$day = str_replace('Friday','Thứ 6',$string);
		}elseif(is_int(strpos($string,'Saturday'))){
			$day = str_replace('Saturday','Thứ 7',$string);
		}elseif(is_int(strpos($string,'Sunday'))){
			$day = str_replace('Sunday','CN',$string);
		}
		return $day;
	}
	public static function showDate($string, $format = 'd-m-Y'){
		if (self::isDateTime($string)){
			return Carbon::parse($string)->format($format);
		}
	}
	public static function showTime($item,$key = 'created_at',$haveTime = false){
		$updateTime = strtotime($item->$key);
		$now = strtotime(date("Y-m-d H:i:s"));
		$time = $updateTime-$now;
			$day = floor($time/(24*60*60));
			$hour = floor($time/(60*60));
			$minutes = floor($time/60);
			$second = floor($time);
		if($time < 0){
			if(-$time >= 60){
				$value = -$minutes . " phút trước";
			}else{
				$value = -$second . " giây trước";
			}
			if(-$time >= (60*60)){
				$value = -$hour . " giờ trước";
			}
			if(-$time >= (24*60*60)){
				$value = -$day . " ngày trước";
			}
			if(-$time >= (2*24*60*60)){
				if ($haveTime) {
					$value = date("d-m-Y H:i:s",$updateTime);
				}else {
					$value = date("d-m-Y",$updateTime);
				}
			}
		}
		else{
			if($time >= 60){
				$value = $minutes . " phút nữa";
			}else{
				$value = $second . " giây nữa";
			}
			if($time >= (60*60)){
				$value = $hour . " giờ nữa";
			}
			if($time >= (24*60*60)){
				$value = $day . " ngày nữa";
			}
			if($time >= (2*24*60*60)){
				$value = date("d-m-Y",$updateTime);
			}
		}
		return $value;
	}
	public static function show($object, $key, $def = '')
	{
		if (!is_object($object) && !is_array($object)) {
			return $def;
		}
		if(is_object($object)){
			$value = isset($object->$key) ? $object->$key : '';
		}
		else{
			$value = isset($object[$key]) ? $object[$key] : '';
		}
		switch ($key) {
			case 'created_at':
			case 'updated_at':
				return self::isDateTime($value) == true ? Carbon::parse($value)->format('d/m/Y') : '';
				break;
			case 'price':
			case 'price_old':
			case 'starting_price':
			case 'origin_price':
			case 'price_step':
			case 'subtotal':
			case 'priceTotal':
				return Currency::showMoney($value);
				break;
			case 'slug':
				return route('home').'/'.$value;
				break;
			case 'link':
				return self::language($value);
				break;
			default:
				return $value;
				break;
		}
	}
	public static function language($value){
		if(\App::getLocale() == 'en'){
			return '/en'.'/'.$value;
		}else{
			return $value;
		}
	}
	public static function showPrice($price){
		return \Currency::showMoney($price);
	}
	public static function checkArr(&$var)
	{
		return is_array($var) && count($var) > 0;
	}
	public static function checkObj(&$var)
	{
		return is_object($var) && count((array)$var) > 0;
	}
	public static function checkStr(&$var)
	{
		return is_string($var) && trim($var) != '';
	}
	public static function checkInt(&$var)
	{
		return is_numeric($var) && (int)$var > 0;
	}
	public static function checkFloat(&$var)
	{
		return is_numeric($var) && (float)$var > 0;
	}
	public static function uploadImg($inputName,$saveFrom){
    	if (!request()->hasFile($inputName)) {
    		return '';
    	}
		$image = request()->file($inputName);
		$uploadRootDir = 'public/uploads';
		$uploadDir = $saveFrom;
		$pathRelative = $uploadRootDir.'/'.$uploadDir.'/';
		$pathAbsolute = base_path($pathRelative);
		$dirs = explode('/', $uploadDir);
		$parentId = 0;
		foreach($dirs as $item){
			$parentId = Media::createDir($uploadRootDir, $item, $pathRelative, $pathAbsolute,$parentId);
		}
		if (is_bool($parentId)) {
			return '';
		}
		$resizeConfigs = \DB::table('v_configs')->select('value')->where('name', 'SIZE_IMAGE')->first();
		$ext = $image->getClientOriginalExtension();
		$fileName = strtolower(\Str::random(5)).'-'.time().'.'.$ext;
		$image->move($pathAbsolute, $fileName);
		Media::convertWebpImage($pathAbsolute,$fileName);
		Media::resizeImage($pathAbsolute, $resizeConfigs, $image, $fileName);
		$img_id = Media::insertImageMedia($uploadRootDir, $pathAbsolute, $pathRelative, $fileName, $parentId);
		return Media::img($img_id);
    }
    public static function uploadImgs($inputName,$saveFrom){
    	if (!request()->hasFile($inputName)) {
    		return '';
    	}
    	$imgs = [];
		$images = request()->file($inputName);
		$uploadRootDir = 'public/uploads';
		$uploadDir = $saveFrom;
		$pathRelative = $uploadRootDir.'/'.$uploadDir.'/';
		$pathAbsolute = base_path($pathRelative);
		$parentId = Media::createDir($uploadRootDir, $uploadDir, $pathRelative, $pathAbsolute);
		if (is_bool($parentId)) {
			return '';
		}
		$resizeConfigs = \DB::table('v_configs')->select('value')->where('name', 'SIZE_IMAGE')->first();
		foreach ($images as $key => $image) {
			if ($image == null) {
				continue;
			}
			$ext = $image->getClientOriginalExtension();
			$fileName = strtolower(\Str::random(5)).'-'.time().'.'.$ext;
			$image->move($pathAbsolute, $fileName);
			Media::convertWebpImage($pathAbsolute,$fileName);
			Media::resizeImage($pathAbsolute, $resizeConfigs, $image, $fileName);
			$imgs[] = Media::insertImageMedia($uploadRootDir, $pathAbsolute, $pathRelative, $fileName, $parentId);
		}
		return Media::libImg($imgs);
    }



	public static function getFirstSegmentWithLang($langCode)
	{
		$locale = \App::getLocale();
		if ($langCode == $locale) {
			return;
		}
		$defaultLocale = \Config::get('app.locale_origin');
		$hiddenDefaultLocale = \Config::get('laravellocalization.hideDefaultLocaleInURL');
		$segments = request()->segments();
		if ($locale == $defaultLocale && $hiddenDefaultLocale == true) {
			if (!isset($segments[0])) {
				return;
			}
			else{
				return $segments[0];
			}
		}
		else{
			if (!isset($segments[1])) {
				return;
			}
			else{
				return $segments[1];
			}
		}
	}
	public static function getMenuRecursive($group = null,int $take = null)
	{
		$menus = Menu::where('menu_category_id', $group)->where('parent', 0)->act()->ord()->with('recursiveChilds');
		if ($take != null) {
			return $menus->take($take)->get();
		}
		return $menus->get();
	}
	
	public static function showMenuRecursive($menus)
	{
		if ($menus->count() > 0) {
			echo '<ul>';
				foreach ($menus as $menu) {
					$active = url()->current() == url($menu->link) ? "active" : " ";
					echo '<li>';
						echo '<a href="'.$menu->link.'" title="'.\Support::show($menu, 'name').'" class="'.$active.'" >';
								if($menu->icon != ''){
									echo '<img src="'.\FCHelper::eimg2($menu,'icon','200x0').'" alt="'.\Support::show($menu, 'name').'" title="'.\Support::show($menu, 'name').'"/>';
								}
								echo \Support::show($menu, 'name');
						echo '</a>';
						self::showMenuRecursive($menu->recursiveChilds);
					echo '</li>';	
				}
			echo '</ul>';
		}
	}
	
	public static function json(array $arr, int $status = -1)
	{
		if ($status != -1) {
			return response()->json($arr, $status);
		}
		return response()->json($arr);
	}
	public static function response(array $arr, int $status = -1)
	{
		if (request()->ajax()) {
			return self::json($arr, $status);
		}
		else{
			\Session::flash('typeNotify', $arr['code'] != 200 ? 'error' : 'success');
			\Session::flash('messageNotify', $arr['message']);
			return (!isset($arr['redirect']) ? redirect('/') : $arr['redirect']==false) ? back() : redirect($arr['redirect']);
		}
	}
	
	public static function exeCurl($url, $type = 'GET', $data = null, $headers = []){
		$curl = curl_init();
		$params = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 100,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $type,
			CURLOPT_FOLLOWLOCATION => 0, // 0 cho phép redirect theo nếu link curl đích bị redirect
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0
		);
		if ($type == 'POST' && is_string($data)) {
			$params[CURLOPT_POSTFIELDS] = $data;
		}
		if($type == 'POST' && is_array($data)){
			$params[CURLOPT_POSTFIELDS] = http_build_query($data);
		}
		if ($type == 'GET' && is_array($data)) {
			$params[CURLOPT_URL] = $url.'?'.http_build_query($data);
		}
		if($headers){
			$params[CURLOPT_HTTPHEADER] = $headers;
		}
		curl_setopt_array($curl, $params);
		
		$res = curl_exec($curl); 
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE); 
		$err = curl_error($curl); 
		curl_close($curl);
		if (!empty($err)) {
			return $err;
		}
		return $res;
	}	
	public static function transformAsterisks($key){
		return substr($key, 0, strrpos($key, ' ')).' '.str_repeat('*', mb_strlen(substr($key, strrpos($key, ' ') + 1)));
	}
	public static function getSegment($request, $level)
	{
		if (\App::getLocale() == \Config::get('app.locale_origin')) {
            $seg = $request->segment($level, '');
        }
        else{
            $seg = $request->segment($level + 1, '');   
        }
        return $seg;
	}
	public static function generateRandomString($length = 10,$string = '0123456789') {
	    $characters = $string;
	    $charactersLength = strlen($characters);
	    $randomString = '';
	    for ($i = 0; $i < $length; $i++) {
	        $randomString .= $characters[rand(0, $charactersLength - 1)];
	    }
	    return $randomString;
	}
	public static function getLinkStatic($locale, $route)
	{
		if ($locale == \App::getLocale()) {
			return url()->full();
		}
		else{
			if ($route == null) {
				if ($locale == \Config::get('app.locale_origin')) {
					return url('/');
				}
				else{
					return url('/').'/'.$locale;	
				}
			}
			$segments = request()->segments();
			$link = self::getSegment(request(), 1);
			if ($link == 'danh-muc-dau-gia' || $link == 'auction-category') {
				return \Jager::staticLinkAuctionCategory($locale, $route, $link);
			}
			$param = $_SERVER['QUERY_STRING'];
			if (($key = array_search(\App::getLocale(), $segments)) !== false) {
			    unset($segments[$key]);
			}
			if (($key = array_search($route->{\App::getLocale().'_link'}, $segments)) !== false) {
			    unset($segments[$key]);
			}
			if ($locale == \Config::get('app.locale_origin')) {
				return url('/').'/'.$route->{$locale.'_link'}.(count($segments) > 0 ? '/'.implode('/', $segments) : '').($param != '' ? '?'.$param : '');
			}
			else{
				return url('/').'/'.$locale.'/'.$route->{$locale.'_link'}.(count($segments) > 0 ? '/'.implode('/', $segments) : '').($param != '' ? '?'.$param : '');
			}
		}
	}
	public static function checkComment($product_id,$use_id){
		$comment = Comment::where('user_id',$use_id)->where('map_id',$product_id)->first();
		if($comment == null){
			$noComment = true;
		}else{
			$noComment = false;
		}
		return $noComment;
	}
	public static function countVoucherUsed($voucher_id){
		return UserVoucher::where('voucher_id',$voucher_id)->count();
	}
	public static function getNameComboTranslation($combo_id,$language = null){
		if($language == null){
			$language = \App::getLocale();
		}
			$name = ComboTranslation::where('map_id',$combo_id)->where('language_code',$language)->first();
		if($name == null){
			$name = '';
		}else{
			$name = $name->name;
		}
		return $name;
	}
	public static function checkUserLikeProduct($item){
		if(isset($item->likeProduct)){
            if(count($item->likeProduct) !== 0){
                if(Auth::check()){
                    $active = 'active';
                }else{
                    $active = '';
                }
            }else{
                $active = '';
            }
        }else{
            $active = '';
        }
        return $active;
	}
	public static function showValueVoucher($voucher){
		return '';
	}
	public static function getNameOrderAddressDetail($order,$type){
		switch ($type) {
			case 'province':
				return Province::find(isset($order->province) ? $order->province : $order->province_id)->name;
			break;
			case 'district':
				return District::find(isset($order->district) ? $order->district : $order->district_id)->name;
			break;
			case 'ward':
				return Ward::find(isset($order->ward) ? $order->ward : $order->ward_id)->name;
			break;
		}
	}
	public static function getAddressCarrier($order)
	{	
		$province = Province::where('id', $order->province)->first();
		if ($province == null) {
			return response()->json(['code'=>100,'message'=>trans('fdb::province_not_exist')]);
		}
		$district = District::where('id', $order->district)->first();
		if ($district == null) {
			return response()->json(['code'=>100,'message'=>trans('fdb::district_not_exist')]);
		}
		$wards = Ward::where('id', $order->wards)->first();
		if ($wards == null) {
			return response()->json(['code'=>100,'message'=>trans('fdb::ward_not_exist')]);
		}
		return ['province' => $province, 'district' => $district, 'wards' => $wards];
	}
	public static function getOrders($address,$products,$order)
	{
		$pick_province = Province::where('id', SettingHelper::getSetting('province_store'))->first();
		$pick_district = District::where('id', SettingHelper::getSetting('district_store'))->first();
		$orders['total'] = $order->total_final;
		$orders['id'] = uniqid().'-'.$order->code;
		$orders['order'] = [
			"id"=> $orders['id'],
			"pick_name"=> SettingHelper::getSetting('SENDER_NAME'),
			"pick_address"=>  SettingHelper::getSetting('address_shop'),
			"pick_province"=> $pick_province->name,
			"pick_district"=> $pick_district->name,
			"pick_tel"=> SettingHelper::getSetting('PHONE_SHOP'),
			"tel" => $order->phone ?? '0123456789',
			"name"=> $order->name ?? 'get Fee',
			"address" => $address['address'],
			"province" => $address['province']->name,
			"district" => $address['district']->name,
			"ward" => $address['wards']->name,
			"hamlet"=> "Khác",
			"is_freeship"=> 1,
			"pick_money"=> 0,
			"value" => $order->total_final,
			"note"=> $order->message ?? 'Liên hệ với khách hàng trước khi đến giao hàng',
		];
		$orders['products'] = [];
		$weight = 0;
		foreach($products as $item){
			if($weight !== null){
				$weight += $item['weight'] / 1000;
			}
			if($item['length']/10 > 40 || $item['width']/10 > 40 || $item['height']/10 > 40 ){
				$weight =  ($item['length']/10 * $item['width']/10 * $item['height']/10)/6000;
			}else{
				$weight = $item['weight'] / 1000 < 0.01 ? 0.2 : $item['weight'] / 1000;
			}
			$orders['products'][] = [
				"name" => $item['name'],
				"weight" => $weight,
				"quantity" => $item['qty'],
				"product_code" => $item['code'],
			];
		}
		return $orders;
	}
	public static function getOrdersGHN($address, $products, $order){
		$district_shop = District::where('id', SettingHelper::getSetting('district_store'))->first();
		$ward_shop = Ward::where('id', SettingHelper::getSetting('ward_store'))->first();
		$ghn = new \App\Http\Controllers\Carriers\GiaoHangNhanhController;
		if (count($products) == 0) {
			return $this->json(['code' => 100, 'message' => 'Giỏ hàng trống']);
		}
		// tổng cân nặng đơn hàng
		$weight = 10;
		// chiều dài đơn hàng
		$height = 0;
		// length
		$length = 0;
		// width
		$width = 0;
		
		foreach ($products as $key => $item) {
			$weight += $item['weight'];
			if($weight == 0){
				$weight = 10;
			}
			if ($item['height'] > $height) {
				$height = round($item['height']/10);
			}
			if ($item['length'] > $length) {
				$length = round($item['length']/10);
			}
			if ($item['width'] > $width) {
				$width = round($item['width']/10);
			}
		}
		$dataGetServices = vsprintf('{"shop_id":%d, "from_district":%d, "to_district":%d}', [SettingHelper::getSetting('SHOP_ID_GHN'), $district_shop->district_id, $address['district']->district_id]);
		$giaoHangNhanh = new GiaoHangNhanh('/shipping-order/available-services', $dataGetServices);
		$services = $giaoHangNhanh->getService();
		$services = json_decode($services, true);
		if (!is_array($services) || $services['code'] != 200) {
			return ['code' => 100, 'name' => 'Giao Hàng Nhanh', 'message' => 'Không có loại hình vận chuyển hỗ trợ'];
		}
		$servicesData = [];
		foreach ($services['data'] as $key => $value) {
			if ($value['service_id'] == 53322) { // đi bộ
				$servicesData = $value;
			}
		}
		$servicesData = count($servicesData) == 0 ? $services['data'][0] : $servicesData;
		$data = [
               "payment_type_id" => 1, 
               /*Kiểu thanh toán 
                    1: Cửa hàng/ người bán trả tiền phí
                    2: Người mua/ người nhận trả tiền phí
               */
               "required_note"=> "CHOXEMHANGKHONGTHU", // Ghi chú đơn hàng
               "return_phone"=> SettingHelper::getSetting('PHONE_SHOP'),
               "return_address"=> SettingHelper::getSetting('address'),
               "return_district_id"=> $district_shop->district_id,
               "return_ward_code"=> $ward_shop->code,
               "to_name"=> $order->name, // Tên người nhận
               "to_phone"=> $order->phone, // Số điện thoại người nhận
               "to_address"=> $address['address'], //địa chỉ người nhận
               "to_ward_code"=> $address['wards']->code, // code phường xã người nhận
               "to_district_id"=> $address['district']->district_id, // id quận huyện người nhận
               "cod_amount"=> 0, // tổng tiền đơn hàng
               "client_order_code" => uniqid(),
               "content"=> $order->message == null ? "Nhận hàng mới thanh toán" : $order->message, // Nội dung đơn hàng
               "weight"=> $weight, // Cân nặng
               "length"=> $length, // chiều dài
               "width"=> $width, // chiều rộng
               "height"=> $height, // chiều cao
               "pick_station_id"=> 0,  // Mã kho giao nhận cho người bán/shop đến gửi hàng, shipper sẽ không đến địa chỉ shop lấy nữa
               "insurance_value"=> $order->total_final, //GNH sẽ dựa trên giá trị này để bồi thường nếu có bất kì điều gì bất ngờ xảy ra (bị thất lạc, hỏng...)
               "service_id"=>$servicesData['service_id'],
               /*
                    Mã dịch vụ (nếu ko nhập service_id)
                    1. Bay
                    2. Đi bộ
               */
		];
		foreach($products as $key => $item){
			$data['items'][$key] = [
				"name" => $item['name'],
				"code" => $item['code'],
				"quantity" => $item['qty']
			];
		}
		return $data;
	}
	public static function getOrdersOnCarrier($order){
		if ($order->carrier == 1) {
			$orderCarrier = OrderPaymentGhtk::where('order_id',$order->id)->orderBy('id','DESC')->first();
		}elseif($order->carrier == 2) {
			$orderCarrier = OrderPaymentGhn::where('order_id',$order->id)->orderBy('id','DESC')->first(); 
		}
		return $orderCarrier;
	}
	public static function getStatusOrder($status){
		switch ($status) {
			case '2':
				return 'Chờ lấy hàng';
				break;
			case '3':
				return 'Đang giao';
				break;
			case '4':
				return 'Đã giao';
				break;
			case '5':
				return 'Đang giao';
				break;
			case '6':
				return 'Đã hủy';
				break;
		}
	}
	public static function getStatusPreOrder($status){
		switch ($status) {
			case '1':
				return 'Chưa đặt cọc';
				break;
			case '2':
				return 'Đã đặt cọc';
				break;
		}
	}
	public static function getProduct($id){
		$product = Product::where('id',$id)->first();
		return $product;
	}
	public static function getValueWithLanguage($key,$lang){
		$key = trim($key);
		$value = DB::table('languages')->where('keyword',$key)->where('act',1)->first();
		if($value !== null){
			if($lang == 'vi'){
				return $value->vi_value;
			}else{
				return $value->en_value;
			}
		}else{
			return $key;
		}
	}
	public static function getProductNameLanguage($product_id,$language){
        return Product::join('product_translations','products.id','=','product_translations.map_id')->where('products.id',$product_id)->where('product_translations.language_code',$language)
            ->first()->name;
    }
    public static function getProductSlugLanguage($product_id,$language){
        return Product::join('product_translations','products.id','=','product_translations.map_id')->where('products.id',$product_id)->where('product_translations.language_code',$language)
            ->first()->slug;
    }
    public static function getSaleOrder($sale){
    	switch ($sale['type']) {
    		case 'combo':
    			return \App\Models\Combo::where('id',$sale['id'])->first();
    			break;
			case 'flash_sale':
    			return \App\Models\FlashSale::find($sale['id']);
    			break;
			case 'promotion':
    			return \App\Models\Promotion::find($sale['id']);
    			break;
    		case 'deal':
    			return \App\Models\Deal::find($sale['id']);
    			break;
    		default:
    			return \App\Models\Voucher::with('products')->where('id',$sale)->first();
    			break;
    	}
    }
    public static function findProductSale($sale,$order){
    	switch ($sale['type']) {
    		case 'voucher':
    			return \App\Models\OrderProduct::where('order_id',$order['id'])->where('voucher_id',$sale['id'])->get();
    			break;
    		case 'deal':
    			return \App\Models\OrderProduct::where('order_id',$order['id'])->get();
    			break;
    		default:
				return \App\Models\OrderProduct::where('order_id',$order['id'])->where('promotion_type',$sale['type'])->where('promotion_id',$sale['id'])->get();    			
				break;
    	}
    	
    }
    public static function findSalePromotionProduct($sale,$product_id){
    	switch ($sale['type']) {
    		case 'flash_sale':
    			return \App\Models\FlashSaleProduct::where('flash_sale_id',$sale['id'])->where('product_id',$product_id)->first();
    			break;
    		case 'voucher':
    			return \App\Models\VoucherProduct::where('voucher_id',$sale['id'])->where('product_id',$product_id)->first();
    			break;
    		case 'promotion':
    			return \App\Models\ProductPromotion::where('promotion_id',$sale['id'])->where('product_id',$product_id)->first();
    			break;
			case 'deal':
    			return \App\Models\ProductPromotion::where('promotion_id',$sale['id'])->where('product_id',$product_id)->first();
    			break;
    	}
    	
    }
    public static function sendMailToUser($email, \Illuminate\Contracts\Mail\Mailable $mailable)
    {
    	return \Mail::to($email)->send($mailable);
    }
    public static function sendMailToAdmin(\Illuminate\Contracts\Mail\Mailable $mailable)
    {
    	$email = SettingHelper::getSetting('mail_admin_get_info');
    	return \Mail::to($email)->send($mailable);
    }
    public static function getUrlFilter($get,$name){
		$url="";
		if(isset($get['_token'])){
			unset($get['_token']);
		}
		if(!empty($get[$name])){
		    if(is_array($get[$name])){
			   foreach($get[$name] as $data){
				   if($data == null){
				}else{
					$value = str_replace(' ','-',$data);
					if(empty($url)){
						$url = "&".$name."=".$value;
					}else{
						$url .= "-".$value;
					}
				}
			   }
		    }else{
				$value = str_replace(' ','-',$get[$name]);
				if(empty($url)){
					$url = "&".$name."=".$value;
				}
		    }
		}
		return $url;
	}
	public static function checkMultipleFilter($get,$name,$value){
		if(!empty($get[$name])){
			if(in_array($value,$get[$name])){
				return 'checked';
			}else{
				return '';
			}
		}
	}
	public static function checkArrayFilter($val){
		if(strpos($val,',') > 0 ){
			$array = explode(',',$val);
			return $array;
		}else{
			return $array = ['0'=>$val];
		}
	}
	public static function jsonDecode($data){
		$result = json_decode($data,true);
		return @$result?$result:[];
	} 
	public static function _isMobile(){
	    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}
	public static function isMobile(){
		$detect = new Mobile_Detect;
		return $detect->isMobile();
	}
	public static function getCountFavourite($user_id){
		return UserProductFavourite::where('user_id',$user_id)->get()->count();
	}
	public static function showProvinces($province_id = null){
		$output='<option value="">Thành phố</option>';
		$provinces = Province::all();
		foreach($provinces as $item){
			if($province_id !== null && $province_id == $item->id){
				$selected = 'selected';
			}else{
				$selected = '';
			}
			$output.='<option value="'.$item->id.'"'.$selected.' data-real-id="'.$item->province_id.'">'.$item->name.'</option>';
		}
		return $output;
	}
	public static function showDistricts($province_id,$district_id = null){
		$province = self::getAddress($province_id,'province');
		$output='<option value="">Quận/ Huyện</option>';
		$districts = District::where('province_id',$province->province_id)->get();
		foreach($districts as $item){
			if($district_id !== null && $district_id == $item->id){
				$selected = 'selected';
			}else{
				$selected = '';
			}
			$output.='<option value="'.$item->id.'"'.$selected.' data-real-id="'.$item->district_id.'">'.$item->name.'</option>';
		}
		return $output;
	}
	public static function showWards($district_id,$ward_id = null){
		$district = self::getAddress($district_id,'district');
		$output='<option value="">Phường/ Xã</option>';
		$wards = Ward::where('district_id',$district->district_id)->get();
		foreach($wards as $item){
			if($ward_id !== null && $ward_id == $item->id){
				$selected = 'selected';
			}else{
				$selected = '';
			}
			$output.='<option value="'.$item->id.'"'.$selected.' data-real-id="'.$item->district_id.'">'.$item->name.'</option>';
		}
		return $output;
	}
	public static function getAddress($id,$type){
		switch ($type) {
			case 'province':
				return $province = Province::find($id);
				break;
			case 'district':
				return $district = District::find($id);
				break;
			case 'ward':
				return $ward = Ward::find($id);
				break;
		}
	}
	public static function showAddressOrder($order){
		return self::show($order,'address').', '.
		self::getAddress($order->ward_id,'ward')->name.', '.
		self::getAddress($order->district_id,'district')->name.', '.
		self::getAddress($order->province_id,'province')->name.'.';
	}
	public static function showStausRating($score){
		switch ($score) {
			case 5:
				return 'Cực kỳ hài lòng';
				break;
			case 4:
				return 'Rất tốt';
				break;
			case 3:
				return 'Bình thường';
				break;
			case 2:
				return 'Tạm được';
				break;
			case 1:
				return 'Không thích';
				break;
		}
	}
	public static function log($file, $data, $eol = false)
    {
        if (!is_string($data)) {
            $data = json_encode($data);    
        }
        $content = $eol == true ? PHP_EOL.$data : $data;
        file_put_contents($file, $content, FILE_APPEND);
    }
	public static function filterOrder($request,$orders){
		if(isset($request->name)){
			$orders->where('code','like','%'.$request->name.'%');
		}
		if(isset($request->date)){
			$datetime = explode('/',$request->date);
			$dateFind = $datetime[2].'-'.$datetime[1].'-'.$datetime[0];
			$orders->whereDate('created_at',$dateFind);
		}
		return $orders;
	}
	public static function generateRandomCode($length = 10) {
		$characters = '0123456789';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
		    $randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	 }
	public static function getTypeFile($type){
		switch ($type) {
			case 'xlsx':
				return 'Excel';
				break;
			default:
				return 'Excel';
				break;
		}
	}
	public static function getString($key,$data,$def=''){
		return isset($data[$key])?(string)$data[$key]:$def;
	}
	public static function getInt($key,$data,$def=0){
		return isset($data[$key])?(int)$data[$key]:$def;
	}
	public static function getArr($key,$data,$def=[]){
		return isset($data[$key])?(array)$data[$key]:$def;
	}
	
	public static function checkHasOrder($id,$field){
		$orders = \App\Models\Order::where([$field=>$id])->get();
		if($orders->count() == 0){
			return true;
		}
		foreach ($orders as $key => $item) {
			if($item->status == 11){
				return true;
			}
		}
		return false;
	}
	public static function checkPermissionViewAccount($idUser,$view){
		if(strtolower($view) == 'collaborator'){
			$userAgency = \App\Models\UserAgency::where('user_id',$idUser)->first();
			if(isset($userAgency)) return false;
			return true;
		}
		if(strtolower($view) == 'agency'){
			$userCollaborator = \App\Models\UserCollaborator::where('user_id',$idUser)->first();
			if(isset($userCollaborator)) return false;
			return true;
		}
		if(strtolower($view) == 'technician'){
			return true;
		}
		return false;
	}
	public static function transformNumberToAsterisks($string,$start,$end,$character = '*'){
		//Kiểm tra đầu vào
		$start = $start < 0 ? 1 : $start;
		$start = $start > 10 ? 1 : $start;
		$end = $end < 0 ? 0 : $end;
		$end = $end > 10 ? 10 : $end;
		
		if(strlen($string)>10){
			 return 'Sai định dạng số điện thoại';
		}
		//Độ dài muốn thay đổi
		$lengthChange = $end-$start+1;
		//Thay đổi độ dài
		$transformLengthToCharacter = $lengthChange <= 0 ? str_repeat($character,10)  : str_repeat($character,$lengthChange);
		$transformLengthToCharacter = $lengthChange > 10 ? str_repeat($character,10)  : str_repeat($character,$lengthChange);
		//Lấy số trước thay đổi
		if($start !== 0){
			 $start = $start - 1;
			 $keyStart = substr($string,0,$start);
		}else{
			 $keyStart='';
		}
		//Lấy số sau thay đổi
		$lengthString = strlen($string);
		$keyEnd = $lengthString-$end !== 0 ? substr($string,-($lengthString-$end)) : '';
		//Kết quả
		$phone = $keyStart.$transformLengthToCharacter.$keyEnd;
		return $phone;
   }
   public static function sendMail($view,$subject,$email,array $data){
		\Mail::send($view,[
			'data' => $data
		], function($mail) use($email,$subject){
			$mail->to($email);
			$mail->from('sendmail.tech5s@gmail.com','Eco248');
			$mail->subject($subject);
		});
	}
	public static function processSlug($string,$table){
		if($string == '') return '';
        $slug = \Str::slug($string);
        $total = 0;
        $count = count(DB::table('v_routes')->where('vi_link',$slug)->get());
        $total +=$count;
        $ext = $slug;
        while($count>0){
            $ext  = $slug.($count>0?"-".($total+1):"");
            $count = count(DB::table('v_routes')->where('vi_link',$ext)->get());
            $total +=1;
        }
        return $ext;
    }
	public static function getYoutubeId($link){
		if($link == '')return 0;
	    preg_match("/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $link, $matches);
	    if(isset($matches[1]) && $matches[1] !=""){
	        return $matches[1];
	    }
	    else{
	        return 0;
	    }
	}
	public static function checkUnique($field,$table,$value){
  		$results = \DB::table($table)->where($field,$value)->first();
  		return isset($results)?true:false;
  	}
  	public static function getFieldToUserName($userName){
  		return filter_var($userName, FILTER_VALIDATE_EMAIL)==true?'email':'name';
  	}
  	public static function getPriceAndPercen($product){
		$price= isset($product->price)?$product->price:0;
	    $price_old= isset($product->price_old)?$product->price_old:0;
	    $result = [
	    	'real_price'=>0,
	    	'price'=>0,
	    	'percent'=>0,
	    ];
	    if($price > 0 && $price_old > 0){
	        if($price_old > $price){
	        	$percent = ($price_old-$price)/$price_old*100;
	          	$result = [
			    	'real_price'=>\Currency::showMoney($price),
			    	'price'=>\Currency::showMoney($price_old),
			    	'percent'=>round($percent)
			    ];
	        }
	        else{
	            $result = [
	            	'real_price'=>\Currency::showMoney($price),
			    	'price'=>"",
			    	'percent'=>""
			    ];
	        }
	    }elseif($price > 0 && $price_old == 0){
			$percent = ($price-$price_old)/$price*100;
			$result = [
				'real_price'=>\Currency::showMoney($price),
				'price'=>"",
				'percent'=>""
			];
		}elseif($price_old > 0 && $price == 0){
			$result = [
				'real_price' => \Currency::showMoney($price_old),
				'price' => "",
				'percen' => ""
			];
		}elseif($price_old == 0 && $price == 0){
			$result = [
				'real_price' => "Liên hệ",
				'price' => "",
				'percen' => ""
			];
		}
	    
	    return $result;
	}
	
	public static function getColorById($id)
	{
		return Color::act()->where('id', $id)->first();
	}
	
	public static function countStatistic(){
		return \App\Models\StatisticsCount::count();
	}
	public static function getReasonCancelOrder(){
		return \App\Models\ReasonCancel::all();
	}
	public static function arrayPluck($array, $key) {
	    return array_map(function($v) use ($key) {
	        return is_object($v) ? $v->$key : $v[$key];
	    }, $array);
	}
	public static function getCategories(){
		return \App\Models\NewsCategory::with('news')->get();
	}
	
	public static function getNewsTag(){
		return \App\Models\NewsTag::with('news')->get();
	}
	
	public static function getNewsLast(){
		return \App\Models\News::where('hot',1)->act()->ord()->get();
	}
	public static function flatten(array $array) {
        $result = array();
	    if (!is_array($array)) {
	        $array = func_get_args();
	    }
	    foreach ($array as $key => $value) {
	        if (is_array($value)) {
	            $result = array_merge($result, self::flatten($value));
	        } else {
	            $result = array_merge($result, array($key => $value));
	        }
	    }
	    return $result;
    }
}