<?php 
namespace vanhenry\manager\controller;
use Illuminate\Http\Request;
use vanhenry\helpers\helpers\SettingHelper;
use vanhenry\rakuten_ichiba\service\Connect;
use App\Models\{RakutenSeller,RakutenProduct,RakutenCategoryProduct,RakutenCategory};
use Stichoza\GoogleTranslate\GoogleTranslate;
trait RakutenTrait{
    
    public function syncRakuten(Request $request){
        set_time_limit(0);
        $genre_id="";
        $page="";
        $keyword="";
        $itemCode="";
        $shopCode="";

        $dataProduct = [];
        $dataSeller = [];
        $dataPivot = [];
        if($request->isMethod('POST')){
            if(isset($request->number)){
                $page = round($request->number / 30,0);
                if($page > 100){
                    $page = 100;
                }
                if($page <= 1){
                    $page = 1;
                }
            }
            if(isset($request->keyword)){
                $keyword = $request->keyword;
            }
            if(isset($request->shop_code)){
                $shopCode = $request->shop_code;
            }
            if(isset($request->category_code)){
                $genre_id = $request->category_code;
            }
            if(isset($request->item_code)){
                $itemCode = $request->item_code;
            }

            if($genre_id=="" && $keyword=="" && $itemCode=="" && $shopCode==""){
                $categories =RakutenCategory::orderBy('crawl_index','ASC')->get()->take(1000);
                foreach($categories as $item){
                    $item->crawl_index += 1;
                    $item->save();
                    $page = round($request->number / 30,0);
                    if($page > 100){
                        $page = 100;
                    }
                    if($page <= 1){
                        $page = 1;
                    }
                    $genre_id = $item->id_r;
                    
                    $this->cronDataProduct($genre_id,$page,$keyword,$itemCode,$shopCode);
                }
            }else{
                $this->cronDataProduct($genre_id,$page,$keyword,$itemCode,$shopCode);
            }
            return \Support::response([
                'code'=>'200',
                'message'=>'Đã đồng bộ sản phẩm thành công'
            ]);
        }
        return view('vh::view.rakuten.index');
    }

    public function syncCategory(Request $request){
        $genre_id = "";
        $level = "";
        if(isset($request->category_code)){
            $genre_id = $request->category_code;
        }
        if(isset($request->level)){
            $level = $request->level;
        }
        
        if($genre_id == "" && $level == ""){
            $message = $this->createDataCategory($genre_id);
        }
        if($genre_id == "" && $level > 0){
            $categories = RakutenCategory::where('level',$level)->where('act',1)->get();
            foreach($categories as $item){
                $message = $this->createDataCategory($item->id_r);
            } 
        }
        if($genre_id !== ""){
            $message = $this->createDataCategory($genre_id);
        }

        return \Support::response([
            'code'=>200,
            'message'=>$message
        ]);
    }
    
    public function createDataCategory($genre_id){
        $keyApp = SettingHelper::getSetting('rakuten_key_app');
        $rakuten = new Connect($keyApp);
        $result = $rakuten->findCategory($genre_id);
        $result = json_decode($result);
        if(isset($result->error)){
            return 'Đã xảy ra lỗi ở danh mục'.$genre_id;
        }
        foreach($result->children as $item){
            $this->createCategory($item);
        }
        return 'Đồng bộ danh mục thành công';
    }

    public function createCategory($item){
        $category = RakutenCategory::where('id_r',$item->child->genreId)->first();
        if($category == null){
            // $tr = new GoogleTranslate(); // Translates to 'en' from auto-detected language by default
            // $tr->setSource(); // Detect language automatically
            // $tr->setTarget('vi'); // Translate to Georgian
            // $name_vi = $tr->translate($item->child->genreName);
            $category = new RakutenCategory;
            // $category->name = $name_vi;
            $category->name_jp = $item->child->genreName;
            $category->id_r = $item->child->genreId;
            $category->level = $item->child->genreLevel;
            $category->act = 1;
            $category->link = 'https://www.rakuten.co.jp/category/'.$item->child->genreId.'/';
            // $category->s_title = $name_vi;
            // $category->s_des = $name_vi;
            // $category->s_key = $name_vi;
            $category->number_product = 1000;
            $category->save();
        }   
    }

    public function cronDataProduct($genre_id,$page,$keyword,$itemCode,$shopCode){
        $keyApp = SettingHelper::getSetting('rakuten_key_app');
        $rakuten = new Connect($keyApp);
        for($i = 1; $i <= $page; $i++){
            $result = $rakuten::findItems($genre_id,$page,$keyword,$itemCode,$shopCode);
            $result = json_decode($result);
            if(isset($result->error)){
                return \Support::response([
                    'code'=>100,
                    'message' => 'Vui lòng nhập dữ liệu hợp lệ hoặc không nhập gì để tự đồng bộ!'
                ]);
            }
            foreach($result->Items as $item){
                if($genre_id !== ''){
                    $this->createProduct($item,$genre_id);
                }
                $this->createProduct($item);
            }
        }
    }

    public function createProduct($item,$category = ''){
        $product = RakutenProduct::where('code',$item->Item->itemCode)->first();
        if($product == null){
            $product = new RakutenProduct;
            $product->name = $item->Item->itemName;
            $product->short_content = $item->Item->itemCaption;
            $product->content = $item->Item->itemCaption;
            $product->img = $this->createGroupImg($item->Item->mediumImageUrls);
            $product->lib_img = $this->createGroupImg($item->Item->smallImageUrls);
            $product->parent = $category;
            $product->id_r = $item->Item->genreId;
            $product->act = 1;
            // $product->sugget = $item->Item->
            // $product->status_product = $item->Item->
            $product->price = $item->Item->itemPrice;
            // $product->count = $item->Item->
            $product->root_link = $item->Item->itemUrl;
            // $product->home = $item->Item->
            // $product->price_sale = $item->Item->itemPrice;
            $product->ship_fee = 0;
            $product->sale = 0;
            $product->more = 0;
            $product->hot = 0;
            $product->code = $item->Item->itemCode;
            // $product->r_properties = $item->Item->
            // $product->trademark = $item->Item->
            // $product->brand_name = $item->Item->
            // $product->rating = $item->Item->
            // $product->purchase_fee = $item->Item->
            // $product->is_sold = $item->Item->
            // $product->solds = $item->Item->
            // $product->ord = $item->Item->
            $product->s_title = $item->Item->itemName;
            $product->s_des = $item->Item->itemName;
            $product->s_key = $item->Item->itemName;
            // $product->inventory_available = $item->Item->
            $product->seller_name = $item->Item->shopName;
            // $product->type_product = $item->Item->
            if($item->Item->shopName && $item->Item->shopCode){
                $shopId =  $this->addSeller($item->Item);
                $product->shop_id = $shopId;
            }
            $product->save();
            $this->createPivotCategoryProduct($product,$category);
        }
        return false;
    }

    public function createGroupImg($arrayImg){
        $data = [];
        foreach($arrayImg as $img){
            $data[]= str_replace('128','500',$img->imageUrl);
        }
        return json_encode($data);
    }

    public function createPivotCategoryProduct($product,$category){
        $category = RakutenCategory::where('id_r',$category)->first();
        if($category !== null){
            $newPivot = new RakutenCategoryProduct;
            $newPivot->product_id = $product->id;
            $newPivot->category_id = $category->id;
            $newPivot->save();
        }
    }
    
    public function createManyProduct($data){
        RakutenProduct::insert($data);
    }

    public function addSeller($item){
        $seller = RakutenSeller::where('id_r',$item->shopCode)->first();
        if($seller == null){
            $seller = new RakutenSeller;
            $seller->name = $item->shopName;
            $seller->id_r = $item->shopCode;
            $seller->link = $item->shopUrl;
            $seller->act = 1;
            $seller->ord = 0;
            $seller->count = 0;
            $seller->s_title = $item->shopName;
            $seller->s_des = $item->shopName;
            $seller->s_key = $item->shopName;
        }
        $seller->save();
        return $seller->id;
    }
}