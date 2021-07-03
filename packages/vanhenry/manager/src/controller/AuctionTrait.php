<?php 
namespace vanhenry\manager\controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use App\Models\{Auction};

trait AuctionTrait{
	public function auctionDetail(Request $request)
	{
		$id = $request->segment(4);
		$returnurl = $request->returnurl;
		$returnurl = $returnurl == null ? url('/').'/esystem/view/auctions' : base64_decode($returnurl);
		$auction = Auction::where('id', $id)->first();
		if ($auction == null) {
			return redirect($returnurl);
		}
		$product = $auction->product()->translation()->first();
		if ($product == null) {
			\Session::flash('typeNotify', 'danger');
			\Session::flash('messageNotify', 'Không tìm thấy thông tin sản phẩm đấu giá');
			return redirect($returnurl);
		}
		$events = $auction->auctionEvents()->with('user')->orderBy('id', 'desc')->get();
		return view('vh::details.auction', compact('auction', 'returnurl', 'product', 'events'));
	}
}