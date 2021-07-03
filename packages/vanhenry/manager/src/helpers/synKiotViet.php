<?php
namespace vanhenry\manager\helpers;
class synKiotViet
{
	public static $urlGetAccessToken = 'https://id.kiotviet.vn/connect/token';
	public static $urlCategories = 'https://public.kiotapi.com/categories';
	public static $urlProducts = 'https://public.kiotapi.com/products';
	public static $urlUpdateProduct = 'https://public.kiotapi.com/';
	public static $urlUpdateProductByCode = 'https://public.kiotapi.com/products/code/{code}?code=';

	public static $clientId = '65aec620-e043-41b5-9371-7539292f98fe';
	public static $clientSecret = '06216E868FD33D9B00D6271F060A0C43619F574C';
	public static $retailer = 'icdigi';

	public static function synQuantityFromKiot(){
		$products = self::getProducts(); // syn số lượng từ kiot về
	}
	public static function synQuantityToKiot($pros, $type){
		foreach($pros as $k => $v){
			$proKiot = self::getProductDetailByCode($v['code']);
			if($proKiot == null) continue;
			if(!isset($proKiot['inventories'][0])) continue;
			$onHand = $proKiot['inventories'][0]['onHand'];
			if($type == 'minus'){
				$onHandAfter = $onHand > $v['quantity'] ? $onHand - $v['quantity'] : 0;
			}
			else $onHandAfter = $onHand + $v['quantity'];

			$post = [
				'name' => $v['name'],
				'inventories' => [
					[
						'branchId' => $proKiot['inventories'][0]['branchId'],
						'onHand' => $onHandAfter
					]
				]
			];

			self::updateProducts(['branchId' => $proKiot['inventories'][0]['branchId'], 'id' => $proKiot['id']], json_encode($post));
			\DB::table('products')->where('id', $v['id'])->update(['storage' => $onHandAfter]);
		}
	}
	public static function getToken(){
		$post = ['scopes' => 'PublicApi.Access', 'grant_type' => 'client_credentials', 'client_id' => self::$clientId, 'client_secret' => self::$clientSecret];
		$headers = array(
			    "Accept: */*",
			    "Accept-Encoding: gzip, deflate",
			    "Cache-Control: no-cache",
			    "Connection: keep-alive",
			    "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/76.0.3809.132 Safari/537.36",
			    "cache-control: no-cache"
		  	);
		$curl = self::curl(self::$urlGetAccessToken, 'POST',$headers, $post);
		if(!isset($curl['response'])){
			self::writeLog($curl['status'], $curl['err'], __line__);
			echo 'line: '.__line__.' - ';die('Không có dữ liệu token');
		}
		$responseJson = $curl['response'];
		$re = json_decode($responseJson,true);
    	if(array_key_exists("access_token", $re) && $re["access_token"] != ""){
    		// echo '<pre>'; var_dump($re["access_token"]);die(); echo '</pre>';
    		return $re["access_token"];
    	}
    	else{
    		self::writeLog($curl['status'], 'Không tồn tại access token', __line__);
    		echo 'line: '.__line__.' - ';die('Không tồn tại access token');
    	}
	}
	public static function getHeader(){
		return ['Retailer: '.self::$retailer, 'Authorization: Bearer '.self::getToken()];
	}
	public static function getCategories(){
		$curl = self::curl(self::urlCategories, 'GET', self::getHeader());
		if(!isset($curl['response'])){
			self::writeLog($curl['status'], $curl['err'], __line__);
			echo 'line: '.__line__.' - ';die('Không có dữ liệu danh mục');
		}
		$responseJson = $curl['response'];
		$re = json_decode($responseJson,true);
    	if(array_key_exists("data", $re)){
    		return $re;
    	}
    	else{
    		self::writeLog($curl['status'], 'Không tồn tại danh sách danh mục', __line__);
    		echo 'line: '.__line__.' - ';die('Không tồn tại danh sách danh mục');
    	}
	}
	public static function getProducts(){
		$currentItem = 0;
		$pageSize = 100;
		$total = 0;
		do{
			$curl = self::curl(self::$urlProducts."?currentItem=$currentItem&pageSize=$pageSize", 'GET', self::getHeader());
			if(isset($curl['response'])){
				$response = $curl['response'];
				$res = json_decode($response,true);

		    	$data = $res['data'];
		    	$total = $res['total'];
		    	foreach($data as $k => $v){
		    		$productDetailKiot = self::getProductDetailById($v['id']);
		    		if($productDetailKiot == null) continue;
		    		if(!isset($productDetailKiot['inventories'][0]['onHand'])) continue;
		    		$proDb = \App\Product::where('code', $productDetailKiot['code'])->first();
		    		if($proDb !== null){
		    			$proDb->storage = $productDetailKiot['inventories'][0]['onHand'];
		    			$proDb->kiot_id = $productDetailKiot['id'];
		    			$proDb->save();
		    		}
		    	}
		    	$currentItem += count($data);
			}
		}
		while($currentItem < $total);
	}
	public static function updateProducts($get, $json){
		$headers = self::getHeader();
		$headers[] = 'Content-Type: application/json';
		$updates = self::curl(self::$urlProducts."/{$get['id']}?".http_build_query($get), 'PUT', $headers, $json);
	}
	public static function getProductDetailById($id){
		$curl = self::curl(self::$urlProducts.'/'.$id, 'GET', self::getHeader());
		if(!isset($curl['reponse'])){
			$pro = json_decode($curl['response'], true);
			if(is_array($pro)){
				return $pro;
			}
		}
	}
	public static function getProductDetailByCode($code){
		$curl = self::curl(self::$urlUpdateProductByCode.$code, 'GET', self::getHeader());
		if(!isset($curl['reponse'])){
			$pro = json_decode($curl['response'], true);
			if(is_array($pro)){
				return $pro;
			}
		}
	}
	public static function curl($url, $method, $headers, $post = ''){
		$curl = curl_init();
		$param = array(
			CURLOPT_URL => $url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => $method,
			CURLOPT_HTTPHEADER => $headers
		);
		if($method == 'POST' || $method == 'PUT'){
			$param[CURLOPT_POSTFIELDS] = $post;
		}
		curl_setopt_array($curl, $param);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 2 dòng này để curl đến domain https(ssl)
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0); // 2 dòng này để curl đến domain https(ssl)
		$response = curl_exec($curl); // return false(can't connect to server) or mixed
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE); // return int = 0, 200, 201, 301, 400, 500... Or tùy server của bên cung cấp thiết lập. Thường thì >=400 or = 0 thì là error
		$err = curl_error($curl); // return string or ''
		curl_close($curl);
		if ($err) { // không thể thi hành(execute) curl
			// return "cURL Error #:" . $err;
			return ['status' => $status, 'err' => $err];
		} else {
			return ['status' => $status, 'response' => $response];
		}
	}
	public static function writeLog($status, $message, $line){
		file_put_contents('kiotviet/logs.txt', date('d/m/Y H:i:s')." - Line: $line - Status: $status - ".$message.PHP_EOL, FILE_APPEND);
	}
}