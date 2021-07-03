<?php
namespace App\Helpers;
use App;
class VRoute
{
	public static function get($code, $lang = null)
	{
		$route = \Cache::remember('v_route_static', 10 * 60, function () {
    	return \DB::table('v_routes')->where('is_static', 1)->get();
		});

		$route = $route->filter(function($v, $k) use($code){
			return $v->code == $code;
		});

		$route = $route->first();

		if($lang == null){
			$lang = App::getLocale();
		}

		if ($route == null) {
			return route('home');
		}

		return route('home').'/'.$route->{$lang.'_link'};
	}
}
