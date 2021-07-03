<?php 

namespace vanhenry\manager\controller;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse as Redirect;
use vanhenry\sapo\libs\Curl;
use vanhenry\sapo\services\Sync;
use vanhenry\sapo\model\SapoAccount;

trait SapoTrait {
	public function applyPermissionSapoShop(Request $request)
	{
		$user = \DB::table('sapo_accounts')->where(['act'=>1,'id'=>1])->first();
		if(!isset($user)) return 'Không thể thực hiện yêu cầu';
		$url = 'https://'.$user->name.'.mysapo.net/admin/oauth/authorize';
		$client_id = $user->client_id;
		$scope = 'read_content,write_content,read_themes,write_themes,read_products,write_products,read_customers,write_customers,read_orders,write_orders,read_script_tags,write_script_tags,read_price_rules,write_price_rules,read_draft_orders,write_draft_orders';
		$redirect_uri = url('/').'/esystem/callback-premission-sapo';
		$url .= '?client_id='.$client_id.'&scope='.$scope.'&redirect_uri='.$redirect_uri;
		return redirect($url);
	}

	public function callBackPermissionSapo(Request $request){
		$store = $request->input('store','');
		$code = $request->input('code');
		$hmac = $request->input('hmac');
		$stores = explode('.', $store);
		$name = isset($stores[0])?$stores[0]:null;
		\DB::table('sapo_accounts')->where(['name'=>$name])->update(['code'=>$code,'hmac'=>$hmac]);
		return redirect('/esystem')->with('typeNotify', 'success')->with('messageNotify', 'Yêu cầu cấp quyền thành công');;
	}

	public function syscSapoShop(){
		set_time_limit(0);
		$account = SapoAccount::where(['act'=>1,'id'=>1])->first();
		$sync = new Sync($account);
		$sync->execute();
	}

}