<?php 
namespace vanhenry\manager\controller;
use Illuminate\Http\Request;
use App\Models\Promotion;
use App\Models\ProductPromotion;
trait MarketingTrait{
     public function marketing(){
          return view('vh::view.marketing');
     }
}