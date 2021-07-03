<?php 
namespace vanhenry\manager\controller;
use Illuminate\Http\Request;
use App\Models\{Order, OrderProduct, Product, User};
use Carbon\Carbon;
use DB;

trait StatisticTrait{
	public function statistic(Request $request){
		$data['type'] = $request->type;
		$data['startDate'] = (new \DateTime('-1 year'))->format('d/m/Y 00:00:00');
		$data['endDate'] = date('d/m/Y 23:59:59');
		$data['maxDate'] = date('d/m/Y 23:59:59');
		if (is_array($dateRequest = $this->startAndEndDateFromRequest($request))) {
			$data['startDate'] = $dateRequest['startDate'];
			$data['endDate'] = $data['maxDate'] = $dateRequest['endDate'];
		}
		$objStartDate = \DateTime::createFromFormat('d/m/Y H:i:s', $data['startDate']);
		$objEndDate = \DateTime::createFromFormat('d/m/Y H:i:s', $data['endDate']);
		switch ($data['type']) {
			// chi tiết doanh thu theo thời gian
			case 'revenue-over-time':
				$i = $objStartDate;
				$data['rangeMonths'] = [];
				do {
					$data['rangeMonths'][] = $i->format('m/Y');
					$month = (int)$i->format('m');
					$year = (int)$i->format('Y');
					$month += 1;
					if ($month > 12) {
						$month = 1;
						$year += 1;
					}
					$i = \DateTime::createFromFormat('d/m/Y H:i:s', '01/'.$month.'/'.$year.' 00:00:00');
				} while ($i <= $objEndDate);
				$data['revenueOverTime'] = $this->listOrder($objStartDate, $objEndDate);
				$data['dataMonths'] = [];
				$data['dataMonthsTotal'] = [];
				foreach ($data['rangeMonths'] as $key => $startDateOfMonth) {
					$startDateMonth = \DateTime::createFromFormat('d/m/Y H:i:s', '01/'.$startDateOfMonth.' 00:00:00');
					$endDateMonth = clone($startDateMonth);
					$endDateMonth->modify('last day of this month')->modify('23:59:59');
					$data['dataMonths'][$startDateOfMonth] = $data['revenueOverTime']->filter(function($v, $k) use($startDateMonth, $endDateMonth){
						return \DateTime::createFromFormat('Y-m-d H:i:s', $v->created_at) >= $startDateMonth && \DateTime::createFromFormat('Y-m-d H:i:s', $v->created_at) <= $endDateMonth;
					});
					$data['dataMonthsTotal'][] = $data['dataMonths'][$startDateOfMonth]->sum('total_final');
				}
				break;
			// chi tiết doanh thu theo sản phẩm bán chạy
			case 'selling-products':
				$result = $this->sellingProducts(null, $objStartDate, $objEndDate);
				$data['nameProductSellings'] = $result['nameProductSellings'];
				$data['qtyProductSellings'] = $result['qtyProductSellings'];
				$data['revenueProductSellings'] = $result['revenueProductSellings'];
				$data['productSellings'] = $result['productSellings'];
				$data['objStartDate'] = $objStartDate;
				$data['objEndDate'] = $objEndDate;
				break;
			// chi tiết doanh thu theo đơn hàng
			case 'revenue-with-order':
				$revenueWithOrder = $this->revenueWithOrder(null, $objStartDate, $objEndDate);
				$data['codeRevenueWithOrder'] = $revenueWithOrder->pluck('code');
				$data['moneyRevenueWithOrder'] = $revenueWithOrder->pluck('total_final');
				$data['revenueWithOrder'] = $revenueWithOrder;
				break;
			// chi tiết doanh thu theo khách hàng
			case 'revenue-with-user':
				$revenueWithUser = $this->revenueWithUser(null, $objStartDate, $objEndDate);
				$data['nameUserRevenueWithUser'] = $revenueWithUser->pluck('user.name');
				$data['moneyUserRevenueWithUser'] = $revenueWithUser->pluck('sum_total_final');
				$data['revenueWithUser'] = $revenueWithUser;
				break;
			// báo cáo trả hàng theo sản phẩm
			case 'refund-with-product':
				$refundWithProducts = $this->refundWithProduct(5, $objStartDate, $objEndDate);
				$data['qtyRefundWithProducts'] = $refundWithProducts->pluck('qty');
				$data['moneyRefundWithProducts'] = $refundWithProducts->pluck('total_money');
				$data['nameProductWithRefund'] = $refundWithProducts->pluck('product.name');
				$data['refundWithProducts'] = $refundWithProducts;
				break;
			case 'refund-with-order':
				$refundWithOrders = $this->refundWithOrder(5);
				$data['codeOrderRefund'] = $refundWithOrders->pluck('code');
				$data['moneyOrderRefund'] = $refundWithOrders->pluck('total_final');
				$data['refundWithOrders'] = $refundWithOrders;
				break;
			default:
				// doanh thu theo thời gian ngày hôm nay và ngày hôm qua
				$beginYesterday = (new \DateTime('-1 day'))->modify('00:00:00');
				$endToDay = (new \DateTime())->modify('23:59:59');
				$data['revenueOverTime'] = $this->revenueOverTime($beginYesterday, $endToDay);
				// sản phẩm bán chạy
				$result = $this->sellingProducts(12);
				$data['nameProductSellings'] = $result['nameProductSellings'];
				$data['qtyProductSellings'] = $result['qtyProductSellings'];
				$data['revenueProductSellings'] = $result['revenueProductSellings'];
				$data['productSellings'] = $result['productSellings'];
				// doanh thu theo đơn hàng
				$revenueWithOrder = $this->revenueWithOrder(5);
				$data['codeRevenueWithOrder'] = $revenueWithOrder->pluck('code');
				$data['moneyRevenueWithOrder'] = $revenueWithOrder->pluck('total_final');
				// doanh thu theo khách hàng
				$revenueWithUser = $this->revenueWithUser(5);
				$data['nameUserRevenueWithUser'] = $revenueWithUser->pluck('user.name');
				$data['moneyUserRevenueWithUser'] = $revenueWithUser->pluck('sum_total_final');
				// báo cáo trả hàng theo sản phẩm
				$refundWithProducts = $this->refundWithProduct(5);
				$data['qtyRefundWithProducts'] = $refundWithProducts->pluck('qty');
				$data['moneyRefundWithProducts'] = $refundWithProducts->pluck('total_money');
				$data['nameProductWithRefund'] = $refundWithProducts->pluck('product.name');
				// báo cáo trả hàng theo đơn hàng
				$refundWithOrders = $this->refundWithOrder(5);
				$data['codeOrderRefund'] = $refundWithOrders->pluck('code');
				$data['moneyOrderRefund'] = $refundWithOrders->pluck('total_final');
				return view('vh::view.pages.statistics', $data);
		}		
		return view('vh::view.pages.statistics', $data);
	}

	public function getStatisticByCodeOrder($type = null){
		$data = [];
		$getArrayCodeProductOrder = Order::from('orders as o')
		->join('order_product as od','od.order_id','=','o.id')
		->groupBy('o.code')
		->where('o.status',\OrderHelper::STATUS_DELIVERED);
		if($type == null){
			$getArrayByCode = $getArrayCodeProductOrder->select('o.code',DB::Raw('sum(od.qty) as totalQty'),DB::raw('sum(od.qty*od.price) totalPrice'))
			->get()
			->take(5);
			$data = [];
		    $data['arrayQtyByCodeProductOrder'] = json_encode($getArrayCodeProductOrder->pluck('totalQty'));
		    $data['arrayCodeByCodeProductOrder'] = json_encode($getArrayCodeProductOrder->pluck('code'));
		    $data['arrayPriceByCodeProductOrder'] = json_encode($getArrayCodeProductOrder->pluck('totalPrice'));
		}else{
			$data = $getArrayCodeProductOrder;
		}
	    return $data;
	}
	public function getStatisticByOrderRefund($type = null){
		$getNameProductOrderRefunds = OrderProduct::select('o.id as order_id','p.id as product_id','pt.name',DB::Raw('SUM(od.qty) as totalQty'),DB::Raw('SUM(od.qty*od.price) as totalPrice'))
		->from('order_product as od')
		->join('orders as o','o.id','=','od.order_id')
		->join('products as p','p.id','=','od.product_id')
		->join('product_translations as pt', 'pt.map_id', '=', 'p.id')
		->where('pt.language_code', \App::getLocale())
		->orderBy('totalPrice','DESC')
		->where('o.status',\OrderHelper::STATUS_REFUND)
		->groupBy('product_id')
		->get()
		->take(5);
 		$data = [];
	    $data['arrayNameProductOrderRefunds'] = json_encode($getNameProductOrderRefunds->pluck('name'));
	    $data['arrayQtyProductOrderRefunds'] = json_encode($getNameProductOrderRefunds->pluck('totalQty'));
	    $data['arrayPriceProductOrderRefunds'] = json_encode($getNameProductOrderRefunds->pluck('totalPrice'));
	    return $data;
	}
	public function getStatisticByCodeOrderRefund($type = null){
		$getArrayCodeProductOrderRefund = Order::select(DB::Raw('Sum(od.price*od.qty) as totalPrice'),DB::Raw('Sum(od.qty) as totalQty'),'o.id as order_id','o.code')
		->from('orders as o')
		->join('order_product as od','od.order_id','=','o.id')
		->where('o.status',\OrderHelper::STATUS_REFUND)
		->groupBy('o.code')
		->get()
		->take(5);
		
 		$data = [];
	    $data['arrayQtyByCodeProductOrderRefund'] = json_encode($getArrayCodeProductOrderRefund->pluck('totalQty'));
	    $data['arrayCodeByCodeProductOrderRefund'] = json_encode($getArrayCodeProductOrderRefund->pluck('code'));
	    $data['arrayPriceByCodeProductOrderRefund'] = json_encode($getArrayCodeProductOrderRefund->pluck('totalPrice'));
	    return $data;
	}
	public function getStatisticByOrderInOneDay($type = null){
		$now = Carbon::now();
		$yesterday = Carbon::today();
		$twoDayBefore = Carbon::now()->subDays(2);
		date_default_timezone_set('Asia/Saigon');
		$orderInDay = Order::whereBetween('created_at', array($yesterday, $now))->get();
		$orderInTwoDay = Order::whereDate('created_at','<',$twoDayBefore)->whereDate('created_at','<',$yesterday)->get();
		$dataTimeOrderInTwoDay = $orderInTwoDay->pluck('created_at');
		$data = [];
	}

	// $startDate = $endDate = new DateTime
	public function revenueOverTime($startDate, $endDate)
	{
		return Order::select(\DB::raw('orders.id, orders.user_id, orders.`code`, orders.total_final, transport_fee'))
			->where('updated_at', '>=', $startDate)
			->where('updated_at', '<=', $endDate)
			->with('orderProducts')
			->get();
	}

	// doanh thu theo thời gian
	// lấy all đơn hàng thành công và bị trả lại trong khoảng $startDate và $endDate
	// làm láo, đúng ra phải lấy theo thời điểm đơn hàng thanh toán thành công, or thời điểm đơn hàng hoàn trả, ví có thể tạo đơn hàng tháng 1 nhưng tháng 2 mới thanh toán => doanh thu phải là của tháng 2
	public function listOrder($startDate, $endDate)
	{
		return Order::from('orders as o')->select(\DB::raw('o.id, o.total_final, o.created_at, o.status, transport_fee'))
		->where(function($q){
			$q->where(function($q){
				$q->where('o.status', \OrderHelper::STATUS_EXCHANGE_RETURN)->orWhere('o.status', \OrderHelper::STATUS_COMPLETED_EXCHANGE_RETURN);
			})
			->orWhere('o.status', \OrderHelper::STATUS_TIME_TO_EXCHANGE_EXPIRED);
		})->where('o.created_at', '>=', $startDate)->where('o.created_at', '<=', $endDate)
		->with('orderProducts')
		->get();
	}

	// sản phẩm bán chạy
	// trang all là show ra 5 sp
	public function sellingProducts($take, $objStartDate = null, $objEndDate = null)
	{
		$productSellings = OrderProduct::select(\DB::raw('order_product.order_id, order_product.product_id, sum(order_product.qty) as qty, sum(order_product.price_at * order_product.qty) as total_money'))
			->whereHas('order', function($q) use ($objStartDate, $objEndDate){
				$q->where('orders.status', \OrderHelper::STATUS_TIME_TO_EXCHANGE_EXPIRED);
				if ($objStartDate != null) {
					$q->where('created_at', '>=', $objStartDate)->where('created_at', '<=', $objEndDate);
				}
			})
			->with(['product' => function($q){
				$q->select(['id', 'name', 'code']);
			}])
			->groupBy('product_id')
			->orderBy('qty', 'desc')
			->take($take)
			->get();
		$nameProductSellings = [];
		$qtyProductSellings = [];
		$revenueProductSellings = [];
		foreach ($productSellings as $key => $value) {
			$nameProductSellings[] = $value->product->name;
			$qtyProductSellings[] = $value->qty;
			$revenueProductSellings[] = $value->total_money;
		}
		return compact('productSellings', 'nameProductSellings', 'qtyProductSellings', 'revenueProductSellings');
	}

	// doanh thu theo đơn hàng
	// trang all là show ra 5 đơn hàng
	public function revenueWithOrder($take, $objStartDate = null, $objEndDate = null)
	{
		return Order::select(\DB::raw('
			orders.id, code, total, total_final, transport_fee'))
		->where(function($q) use ($objStartDate, $objEndDate){
			$q->where('orders.status', \OrderHelper::STATUS_TIME_TO_EXCHANGE_EXPIRED);
			if ($objStartDate != null) {
				$q->where('orders.created_at', '>=', $objStartDate)->where('orders.created_at', '<=', $objEndDate);
			}
		})
		->with('orderProducts')
		->orderBy('total_final', 'desc')
		->take($take)
		->get();
	}

	// doanh thu theo khách hàng
	// trang all là show ra 5 user
	public function revenueWithUser($take, $objStartDate = null, $objEndDate = null)
	{
		return Order::select(\DB::raw('orders.id, orders.user_id, orders.status, sum(orders.total_final) as sum_total_final, count(orders.id) as qty_user_order'))
			->whereHas('user')
			->with(['user' => function($q) use($objStartDate, $objEndDate){
				$q->with(['orderProducts.order' => function($q) use($objStartDate, $objEndDate){
					$q->where('orders.status', \OrderHelper::STATUS_TIME_TO_EXCHANGE_EXPIRED);
					if ($objStartDate != null) {
						$q->where('orders.created_at', '>=', $objStartDate)->where('orders.created_at', '<=', $objEndDate);
					}
				}]);
			}])
			->where(function($q) use ($objStartDate, $objEndDate){
				$q->where('orders.status', \OrderHelper::STATUS_TIME_TO_EXCHANGE_EXPIRED);
				if ($objStartDate != null) {
					$q->where('orders.created_at', '>=', $objStartDate)->where('orders.created_at', '<=', $objEndDate);
				}
			})
			->groupBy('orders.user_id')
			->orderBy('sum_total_final', 'desc')
			->take($take)
			->get();
	}

	// báo cáo trả hàng theo sản phẩm
	// trang all là show ra 5 báo cáo
	public function refundWithProduct($take, $objStartDate = null, $objEndDate = null)
	{
		return OrderProduct::select(\DB::raw('order_product.product_id, sum(order_product.qty) as qty, sum(order_product.price_at * order_product.qty) as total_money'))
			->whereHas('order', function($q) use ($objStartDate, $objEndDate){
				$q->where(function($q){
					$q->where('orders.status', \OrderHelper::STATUS_EXCHANGE_RETURN)->orWhere('orders.status', \OrderHelper::STATUS_COMPLETED_EXCHANGE_RETURN);
				});
				if ($objStartDate != null) {
					$q->where('orders.created_at', '>=', $objStartDate)->where('orders.created_at', '<=', $objEndDate);
				}
			})
			->with(['product' => function($q){
				$q->select(['id', 'code', 'name']);
			}])
			->groupBy('order_product.product_id')
			->orderBy('qty', 'desc')
			->take($take)
			->get();
	}

	// báo cáo trả hàng theo đơn hàng
	// trang all là show ra 5 báo cáo
	public function refundWithOrder($take, $objStartDate = null, $objEndDate = null)
	{
		return Order::select(\DB::raw('orders.id, orders.code, orders.status, orders.total_final'))
			->where(function($q) use ($objStartDate, $objEndDate){
				$q->where(function($q){
					$q->where('orders.status', \OrderHelper::STATUS_EXCHANGE_RETURN)->orWhere('orders.status', \OrderHelper::STATUS_COMPLETED_EXCHANGE_RETURN);
				});
				if ($objStartDate != null) {
					$q->where('orders.created_at', '>=', $objStartDate)->where('orders.created_at', '<=', $objEndDate);
				}
			})
			->with('orderProducts')
			->orderBy('total_final', 'desc')
			->take($take)
			->get();
	}

	public function startAndEndDateFromRequest($request)
	{
		if ($request->daterange != null) {
			$dateRanger = explode('-', $request->daterange);
			if (count($dateRanger) == 2 && \Support::isDateTime($dateRanger[0].' 00:00:00', 'd/m/Y H:i:s') && \Support::isDateTime($dateRanger[1].' 23:59:59', 'd/m/Y H:i:s')) {
				$startDate = $dateRanger[0].' 00:00:00';
				$endDate = $data['maxDate'] = $dateRanger[1].' 23:59:59';
				return compact('startDate', 'endDate');
			}
		}
		return false;
	}
}