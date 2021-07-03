<?php 
namespace vanhenry\manager\controller;
use Illuminate\Http\Request;
use App\Models\Deal;
use App\Models\ProductCategory;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use DateTime;

trait DealTrait{
	public function createDeal(Request $request)
	{
		$validate = $this->validateWhenInsertOrUpdate($request);
		if ($validate instanceof JsonResponse) {
			return $validate;
		}
		$data = $request->all();
		unset($data['_token']);
		$data['start_at'] = Carbon::parse($request->start_at)->format('Y-m-d H:i:s');
		$data['expired_at'] = Carbon::parse($request->expired_at)->format('Y-m-d H:i:s');
		$deal = Deal::create($data);
		return \Support::json(['code' => 200, 'redirect' => url('/').'/esystem/edit/deals/'.$deal->id]);
	}
	public function editDeal(Request $request, Deal $deal)
	{
		if ($request->isMethod('post')) {
			$validate = $this->validateWhenInsertOrUpdate($request,$deal);
			if ($validate instanceof JsonResponse) {
				return $validate;
			}
			$data = $request->all();
			unset($data['_token']);
			$data['start_at'] = Carbon::parse($request->start_at)->format('Y-m-d H:i:s');
			$data['expired_at'] = Carbon::parse($request->expired_at)->format('Y-m-d H:i:s');
			$deal->update($data);
			return \Support::json(['code' => 200, 'redirect' => url('/').'/esystem/edit/deals/'.$deal->id]);	
		}
		else{
			return view('vh::view.deal.edit', compact('deal'));
		}
	}
	public function validateWhenInsertOrUpdate($request,$deal = null)
	{	
		$dealType = $request->deal_type;
		$name = $request->name;
		$start_at = $request->start_at;
		$expired_at = $request->expired_at;
		$limit = $request->limit;
		$buy_min = $request->buy_min;
		if (!\Support::checkStr($name)) {
			return \Support::json(['code' => 100, 'message' => 'Vui lòng nhập tên']);
		}
		if (!\Support::checkStr($start_at)) {
			return \Support::json(['code' => 100, 'message' => 'Vui lòng nhập giờ bắt đầu']);
		}
		if (!\Support::checkStr($expired_at)) {
			return \Support::json(['code' => 100, 'message' => 'Vui lòng nhập giờ kết thúc']);
		}
		if (!\Support::checkInt($limit)) {
			return \Support::json(['code' => 100, 'message' => 'Vui lòng nhập giới hạn sản phẩm']);
		}
		if (!\Support::isDateTime($start_at, 'd-m-Y H:i:s')) {
			return \Support::json(['code' => 100, 'message' => 'Giờ bắt đầu không đúng']);	
		}
		if (!\Support::isDateTime($expired_at, 'd-m-Y H:i:s')) {
			return \Support::json(['code' => 100, 'message' => 'Giờ kết thúc không đúng']);	
		}
		$start_at = Carbon::parse($start_at)->format('Y-m-d H:i:s');
		$expired_at = Carbon::parse($expired_at)->format('Y-m-d H:i:s');
		if($deal == null){
			if (new \DateTime($start_at) < (new \DateTime())->modify('+ 1 hour')) {
				return \Support::json(['code' => 100, 'message' => 'Giờ bắt đầu phải sau 1h so với hiện tại']);
			}
		}else{
			if (new DateTime($deal->start_at) <= new DateTime && new DateTime($deal->expired_at) >= new DateTime) {
				return \Support::json(['code' => 100, 'message' => 'Không thể thay đổi thời gian khi sự kiện đang diễn ra']);
			}
			elseif(new DateTime($deal->expired_at) < new DateTime){
				return \Support::json(['code' => 100, 'message' => 'Không thể sửa deal khi đã kết thúc']);
			}
		}
		if (new \DateTime($expired_at) < (new \DateTime($start_at))->modify('+ 1 hour')) {
			return \Support::json(['code' => 100, 'message' => 'Giờ kết thúc phải sau 1h so với giờ bắt đầu']);
		}
		if ($buy_min != null && (int)$buy_min <= 1000) {
			return \Support::json(['code' => 100, 'message' => 'Mua ít nhất là 1000 VND trở lên']);
		}
		return true;
	}
	public function connectDealToProductMain($chooses, $sale_id)
	{
		$data = [];
		foreach ($chooses as $key => $value) {
			$data[$key]['deal_id'] = $sale_id;
			$data[$key]['product_id'] = $value;
			$data[$key]['act'] = 1;
			$data[$key]['created_at'] = new \DateTime;
			$data[$key]['updated_at'] = new \DateTime;
		}
		\DB::table('deal_product_mains')->insert($data);
		return \Support::json(['code' => 200]);
	}

	public function dealProductMainAction(Request $request, Deal $deal)
	{

		$action = (int)$request->action;
		$products = $request->products;
		$products = !is_array($products) ? [$products] : $products;
		if (count($products) == 0) {
			return \Support::json(['code' => 100, 'message' => 'Vui lòng chọn sản phẩm']);
		}
		if ($action == 1) {
			\DB::table('deal_product_mains')
				->where('deal_id', $deal->id)
				->whereIn('product_id', $products)
				->update(['act' => 0]);
			return \Support::json(['code' => 200, 'message' => 'Thành công']);
		}
		if ($action == 2) {
			\DB::table('deal_product_mains')
				->where('deal_id', $deal->id)
				->whereIn('product_id', $products)
				->update(['act' => 1]);
			return \Support::json(['code' => 200, 'message' => 'Thành công']);
		}
		if ($action == 3) {
			$handler = 'reload';
			\DB::table('deal_product_mains')
				->where('deal_id', $deal->id)
				->whereIn('product_id', $products)
				->delete();
			\DB::table('deal_product_subs')
				->where('deal_id', $deal->id)
				->delete();
			\Session::flash('typeNotify', 'success');
			\Session::flash('messageNotify', 'Thành công');
			return \Support::json(['code' => 200, 'message' => 'Xóa sản phẩm thành công','handler'=>'reload']);
		}
		return \Support::json(['code' => 101, 'message' => 'Không thành công']);
	}
	public function dealProductSubAction(Request $request, Deal $deal)
	{
		$action = (int)$request->action;
		$price = $request->price;
		$products = $request->products;
		$products = !is_array($products) ? [$products] : $products;
		if (count($products) == 0) {
			return \Support::json(['code' => 100, 'message' => 'Vui lòng chọn sản phẩm']);
		}
		if ($action == 1) {
			$handler = 'no-reload';
			\DB::table('deal_product_subs')
				->where('deal_id', $deal->id)
				->whereIn('product_id', $products)
				->update(['act' => 0]);
			return \Support::json(['code' => 200, 'message' => 'Thành công']);
		}
		if ($action == 2) {
			$productDb = Product::whereIn('id', $products)->get();
			foreach ($productDb as $key => $item) {
				if ($price != null && $item->price <= $price) {
					return \Support::json(['code' => 102, 'message' => 'Giá mua kèm không thể cao hơn giá bán hiện tại']);
				}
			}
			$handler = 'no-reload';
			\DB::table('deal_product_subs')
				->where('deal_id', $deal->id)
				->whereIn('product_id', $products)
				->update(['act' => 1, 'price' => $price]);
			return \Support::json(['code' => 200, 'message' => 'Thành công']);
		}
		if ($action == 3) {
			$handler = 'reload';
			\DB::table('deal_product_subs')
				->where('deal_id', $deal->id)
				->whereIn('product_id', $products)
				->delete();
			\Session::flash('typeNotify', 'success');
			\Session::flash('messageNotify', 'Thành công');
			return \Support::json(['code' => 200,'message'=> 'Xóa sản phẩm phụ thành công','handler'=>'reload']);
		}
		return \Support::json(['code' => 101, 'message' => 'Không thành công']);
	}
	public function connectDealToProductSub($chooses, $sale_id)
	{
		$data = [];
		$products = Product::whereIn('id', $chooses)->get();
		foreach ($chooses as $key => $value) {
			$data[$key]['deal_id'] = $sale_id;
			$data[$key]['product_id'] = $value;
			$data[$key]['act'] = 0;
			$data[$key]['created_at'] = new \DateTime;
			$data[$key]['updated_at'] = new \DateTime;
			$product = $products->filter(function($v, $k) use($value){
				return $v->id == $value;
			});
			$data[$key]['price'] = $product->first()->price;
		}
		\DB::table('deal_product_subs')->insert($data);
		return \Support::json(['code' => 200]);
	}
	public function updatePriceDealSub(Request $request, Deal $deal)
	{
		$prices = $request->prices;
		$products = Product::whereIn('id', array_keys($prices))->get();
		foreach ($products as $key => $product) {
			if ($product->price <= $prices[$product->id]) {
				return \Support::json([
					'code' => 100,
					'message' => 'Giá mua kèm phải thấp hơn giá hiện tại',
				]);
			}
			\DB::table('deal_product_subs')
				->where('deal_id', $deal->id)
				->where('product_id', $product->id)
				->update(['price' => $prices[$product->id]]);
		}
		return \Support::json(['code' => 200, 'message' => 'Thành công']);
	}
	public function deleteDeal(Deal $deal)
	{
		$deal->delete();
		return redirect(url('/').'/esystem/view/deals');
	}
}