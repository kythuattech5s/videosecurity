<?php 
namespace vanhenry\manager\controller;
use Illuminate\Http\Request;
use vanhenry\manager\helpers\DetailTableHelper;
use Illuminate\Support\Facades\Cache as Cache;
use DB;
use View;
use App;
use FCHelper;
use Session;
use Carbon\Carbon as Carbon;
use vanhenry\helpers\helpers\StringHelper as StringHelper;
use vanhenry\helpers\JsonHelper as JsonHelper;
use Illuminate\Http\RedirectResponse as Redirect;
use Illuminate\Database\Eloquent\Collection as Collection;
use vanhenry\manager\model\Media;
use Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Schema;
use WebPConvert\WebPConvert;
class MediaController extends BaseAdminController
{	
	public function __construct(){
		parent::__construct();
		$this->middleware('h_users');
		if (!defined('MEDIA_PER_PAGE')) {
			define("MEDIA_PER_PAGE", 100);	
		}
	}
	public function showmedia(){
		return view("vh::media.viewout");
	}
	public function media(){
		$path_uploads = \Config::get('manager.path_uploads');
		$prs = \vanhenry\manager\helpers\MediaHelper:: getParameter();
		$data = array();
		$data["trash"]=0;
		if(array_key_exists("folder", $prs)){
			$folder =urldecode($prs["folder"]);
			$folders = explode(",", $folder);
			$inputpath= $this->generatePathDir($folders);
			if(strlen($inputpath)>0 && count($folders)>0){
				$f =  $this->getSingleMedia($folders[count($folders)-1]);
				if($f->count()>0){
					$dbpath = $f[0]->path.$f[0]->file_name."/";	
				}
				else{
					$dbpath = "";
				}
				if($f->count()>0 && $dbpath==$inputpath){
					Session::put("PROCESS_FILE",array('CURRENT_PATH'=>$inputpath,"CURRENT_ID"=>$f[0]->id));
					$nums = $this->getNumberFileFolder($f[0]->id);
					$nums= count($nums)>0?$nums:array("file"=>0,"folder"=>0);
					$data["nums"] = $nums;
					$data["listItems"] = Media::where("parent",$f[0]->id)->where("trash",0)->orderBy("is_file","asc")->orderBy("name","asc")->paginate(MEDIA_PER_PAGE);
				}
				else{
					return view("vh::media.error");
				}
			}
			else{
				return view("vh::media.error");
			}
		}
		else{
			$nums = $this->getNumberFileFolder(0);
			$nums= count($nums)>0?$nums:array("file"=>0,"folder"=>0);
			$data["nums"] = $nums;
			Session::put("PROCESS_FILE",array('CURRENT_PATH'=>$path_uploads,"CURRENT_ID"=>0));	
			$data["listItems"] = Media::where("parent",0)->where("trash",0)->orderBy("is_file","asc")->orderBy("name","asc")->paginate(MEDIA_PER_PAGE);
		}
		$input = request()->input();
		if(!isset($input['page']) || $input["page"]==1){
			return view("vh::media.index",$data);	
		}
		else{
			return view("vh::media.media-manager",$data);	
		}
	}
	public function trash(){
		$data = [];
		$data["trash"]=1;
		$data["nums"] = array("file"=>0,"folder"=>0);
		$data["listItems"] = Media::whereRaw("trash is null or trash = 1")->orderBy("is_file","asc")->orderBy("name","asc")->paginate(MEDIA_PER_PAGE);
		$input = request()->input();
		if(!isset($input['page']) || $input["page"]==1){
			return view("vh::media.index",$data);	
		}
		else{
			return view("vh::media.media-manager",$data);	
		}
	}
	private function getSingleMedia($folder){
		return Media::where("id",$folder)->orderBy("is_file","asc")->orderBy("name","asc")->take(1)->get();
	}
	private function getNumberFileFolder($parent){
		$nums = Media::select(DB::raw("sum(case when is_file =1 then 1 else 0 end) file,sum(case when is_file =0 then 1 else 0 end) folder"))->where("parent",$parent)->where("trash",0)->get();
		return $nums->count()>0?array("file"=>$nums[0]->file,"folder"=>$nums[0]->folder):array("file"=>0,"folder"=>0);
	}
	private function generatePathDir($folders){
		$path_uploads = \Config::get("manager.path_uploads");
		foreach ($folders as $key => $folder) {
			$f = $this->getSingleMedia($folder);
			if($f->count()>0){
				$path_uploads.=$f[0]->name."/";	
			}
		}
		return $path_uploads;
	}
	private function human_filesize($bytes, $decimals = 2) {
	  $sz = 'BKMGTP';
	  $factor = floor((strlen($bytes) - 1) / 3);
	  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . (" ".@$sz[$factor]."B");
	}
	private function getInfoFile($filename,$file_path){
		$extimgs =\Config::get("manager.ext_img");
	 	$extvideos =\Config::get("manager.ext_video");
		$extfiles = \Config::get("manager.ext_file");
		$extmusic = \Config::get("manager.ext_music");
		$extmisc = \Config::get("manager.ext_misc");
		$pathuploads =\Config::get("manager.path_uploads");
		$basepath =\Config::get("manager.base_path");
		$obj = new \stdClass();
		$obj->extension = strtolower(substr(strrchr($filename,'.'),1));
		$obj->size= $this->human_filesize(filesize($file_path));
		$obj->date = filemtime($file_path);
		$obj->isfile = is_file($file_path)?1:0;
		$onlyDir =  substr($file_path,0, strrpos($file_path, "/")+1);
		//$onlyDir = str_replace("/", "", $onlyDir);
		$obj->dir= $onlyDir;
		$obj->path = $onlyDir.$filename;
		if($obj->isfile){
			if(in_array($obj->extension, $extimgs)){
			$imagedetails = getimagesize($file_path);
			$obj->width = $imagedetails[0];
			$obj->height= $imagedetails[1];
			    if(file_exists($onlyDir.'thumbs/def/'.$filename)){
			    $obj->thumb = $basepath.$onlyDir.'thumbs/def/'.$filename;
			    }
			    else{
			    $obj->thumb = $basepath.$onlyDir.$filename;
			    }
			}
			else if(in_array($obj->extension, $extvideos)){
			    if(file_exists("public/images/ico/".$obj->extension.".jpg")){
			    $obj ->thumb = $basepath."public/images/ico/".$obj->extension.".jpg";
			    }
			    else{
			    $obj->thumb = $basepath."public/images/file.jpg";
			    }
			}
			else if(in_array($obj->extension, $extfiles)){
			    if(file_exists("public/images/ico/".$obj->extension.".jpg")){
			    $obj ->thumb = $basepath."public/images/ico/".$obj->extension.".jpg";
			    }
			    else{
			    $obj->thumb = $basepath."public/images/file.jpg";
			    }
			  }
			else if(in_array($obj->extension, $extmusic)){
			    if(file_exists("public/images/ico/".$obj->extension.".jpg")){
			    	$obj ->thumb = $basepath."public/images/ico/".$obj->extension.".jpg";
			    }
			    else{
			    $obj->thumb = $basepath."public/images/file.jpg";
			    }
			  }
			else if(in_array($obj->extension, $extmisc)){
			    if(file_exists("public/images/ico/".$obj->extension.".jpg")){
			    	$obj ->thumb = $basepath."public/images/ico/".$obj->extension.".jpg";
			    }
			    else{
			    $obj->thumb = $basepath."public/images/file.jpg";
			    }
			  }
			else{
			  $obj->thumb = $basepath."public/images/file.jpg";
			}
		}
		return $obj;
	}
	private function deleteMedia($id,$type=1){
		if($type==0){
			return Media::where("id",$id)->delete();	
		}
		else if($type==1){
			return Media::where("id",$id)->update(["trash"=>1]);		
		}
	}
	public function createDir(Request $request){
		$post = $request->input();
		if(@$post && isset($post['folder_name'])){
			$pf = Session::get("PROCESS_FILE");
			if(!@$pf || !array_key_exists('CURRENT_PATH', $pf)){
				return response()->json([
				    'code' => 100,
				    'message' => 'Sai thông tin đường dẫn'
				]);
			}
			else{
				$currentpath  = $pf['CURRENT_PATH'];
				$folder_name = \Str::of($post['folder_name'])->slug('-');
				if(!is_dir($currentpath.$folder_name)){
					mkdir($currentpath.$folder_name,0777,TRUE);
					$m = new Media;
					$m->name= $folder_name;
					$m->file_name= $folder_name;
					$m->is_file= 0;
					$m->parent=$pf['CURRENT_ID'];
					$m->path=$currentpath;
					$m->file_name=$folder_name;
					$m->extra=json_encode($this->getInfoFile($folder_name,$currentpath));
					$m->save();
					\Event::dispatch('vanhenry.manager.media.createdir.success', array($folder_name,$m->id));
					return response()->json([
					    'code' => 200,
					    'message' => $m->id
					]);
				}
				else{
					return response()->json([
					    'code' => 100,
					    'message' => 'Thất bại'
					]);
				}
			}
		}
		else{
			return response()->json([
				    'code' => 150,
				    'message' => 'Thiếu thông tin dữ liệu'
				]);
		}
	}
	public function getInfoLasted(Request $request){
		$post = $request->input();
		if(@$post && @$post["id"]){
			$infos = $this->getSingleMedia($post["id"]);
			if($infos->count()>0){
				return view("vh::media.folder",array("file"=>$infos[0],"trash"=>0));
			}
		}
	}
	private function _deteleAllFolderFile($parent,$type=1){
		$ps = Media::where("parent",$parent)->get();
		foreach ($ps as $key => $value) {
			if($value->is_file==1){
				if($type==0){
					unlink($value->path.$value->file_name);	
				}
			}
			else{
				$this->_deteleAllFolderFile($value->id,$type);
			}
			$this->deleteMedia($value->id,$type);
		}
	}
	private function rrmdir($dir) { 
	   if (is_dir($dir)) { 
	     $objects = scandir($dir); 
	     foreach ($objects as $object) { 
	       if ($object != "." && $object != "..") { 
	         if (is_dir($dir."/".$object))
	           $this->rrmdir($dir."/".$object);
	         else
	           unlink($dir."/".$object); 
	       } 
	     }
	     rmdir($dir); 
	     return true;
	   } 
	   return false;
	 }
	public function deleteFolder(Request $request,$type=1){
		$post = $request->input();
		if(@$post && isset($post['id'])){
			$id = $post["id"];
			$dir = $this->getSingleMedia($id);
			if($dir->count()>0){
				$d = $dir[0];
				$this->_deteleAllFolderFile($id,$type);
				if($type==0){
					$bl = $this->rrmdir($d["path"].$d["file_name"]);
				}
				$bl = $this->deleteMedia($id,$type);
				if($bl){
					\Event::dispatch('vanhenry.manager.media.delete.success', array($d['file_name'],$id));
					return response()->json([
					    'code' => 200,
					    'message' => $id
					]);
				}
				else{
					return response()->json([
					    'code' => 100,
					    'message' => 'Không thể xóa thư mục'
					]);
				}
			}
			return response()->json([
				    'code' => 100,
				    'message' => 'Thất bại'
				]);
		}
		else{
			return response()->json([
				    'code' => 150,
				    'message' => 'Thiếu thông tin dữ liệu!'
				]);
		}
	}
	public function getInfoFileLasted(Request $request){
		$post = $request->input();
		if(@$post && @$post["id"]){
			$ret = array();
			if(is_array($post["id"])){
				foreach ($post["id"] as $id) {
					$infos = $this->getSingleMedia($id);
					if($infos->count()>0) array_push($ret,$infos[0]);
				}
			}
			else{
				$infos = $this->getSingleMedia($post["id"]);
				if($infos->count()>0) array_push($ret,$infos[0]);
			}
			return view("vh::media.multifile",array("infos"=>$ret,"trash"=>0));
		}
	}
	private function getSizes($file){
		if(file_exists($file)){
			$sizes = DB::table("v_configs")->select("value")->where("name","SIZE_IMAGE")->get();
			if($sizes!=null && count($sizes)>0){
				$json = $sizes[0]->value;
				$arr = json_decode($json,true);
				$arr = @$arr?$arr:array();
				$s = getimagesize($file);
				$w = count($s)>0?$s[0]:1;
				$h = count($s)>1?$s[1]:1;
				array_push($arr,array("name"=>"def","width"=>100,"height"=>(int)($h*100/$w),"quality"=>80));
				return $arr;
			}
		}
		return array();
	}
	private function insertImageMedia($path, $filename,$parent = 0){
		$m = new Media;
		$m->name = $filename;
		$m->file_name = $filename;
		$m->is_file =1;
		$m->parent = $parent;
		$m->path = $path;
		$m->extra = json_encode($this->getInfoFile($filename,$path.$filename));
		$m->save();
		return $m->id;
	}
	private function updateImageMedia($path, $filename,$id,$parent = -1){
		$m = Media::find($id);
		$m->name = $filename;
		$m->file_name = $filename;
		$m->is_file =1;
		if($parent !=-1){
			$m->parent= $parent;
		}
		$m->path = $path;
		$m->extra = json_encode($this->getInfoFile($filename,$path.$filename));
		return $m->save();
	}
	private function validateFileUpload($max_size,$ext){
		$input = request()->file("file");
		$rules = array();
		foreach ($input as $key => $value) {
			$rules[ sprintf( 'images.%d', $key ) ] ='max:'.$max_size."|mimes:".$ext;
		}
		$validator = Validator::make(request()->all(), $rules);
		return $validator;
	}
	private function renameIfExist($path,$filename){
		$img_name = \Str::slug(strtolower(pathinfo($filename, PATHINFO_FILENAME)));
		$img_ext =  strtolower(pathinfo($filename, PATHINFO_EXTENSION));
		$filename = $img_name.'.'.$img_ext;
		$filecounter = 1; 
		$filename = $img_name.'.'.$img_ext;
		$destinationPath = $path.$filename;
		while (file_exists($destinationPath)) {
		    $filename = $img_name . '_' . ++$filecounter . '.'. $img_ext;
		    $destinationPath = $path . $filename;  
		}
		return strtolower($filename);
	}
	private function resizeImage($upload_path,$baseName,$filename,$widthImage,$heightImage,$quality,$name){
		$img = \Image::make($upload_path.$filename);
		if($heightImage<=0){
			$img->resize($widthImage, null, function ($constraint) {
			    $constraint->aspectRatio();
			});
		}
		else if($widthImage<=0){
			$img->resize(null, $heightImage, function ($constraint) {
			    $constraint->aspectRatio();
			});
		}
		else{
			$img->resize($widthImage, $heightImage);
		}
		$newpath = $upload_path."thumbs/".$name."/";
		if(!is_dir($newpath)){
        	mkdir($newpath,0777,1);
        }
        $img->save($newpath.$filename, $quality);
        /*convert image to webp*/
        WebPConvert::convert($newpath.$filename, $newpath.$baseName.'.webp', []);
		return $newpath.$filename;
	}
	public function uploadFile(Request $request){
		$input = $request->input();
		$extimgs =\Config::get("manager.ext_img");
	 	$extvideos =\Config::get("manager.ext_video");
		$extfiles = \Config::get("manager.ext_file");
		$extmusic = \Config::get("manager.ext_music");
		$extmisc = \Config::get("manager.ext_misc");
		$pathuploads =\Config::get("manager.path_uploads");
		$basepath =\Config::get("manager.base_path");
		$max_size =\Config::get("manager.max_size");
		$ext = implode(",", $extimgs);
		$ext .= implode(",", $extvideos);
		$ext .= implode(",", $extfiles);
		$ext .= implode(",", $extmusic);
		$ext .= implode(",", $extmisc);
	  	$pf = Session::get('PROCESS_FILE');
		if(@$pf && array_key_exists('CURRENT_PATH', $pf)&& array_key_exists('CURRENT_ID', $pf)){
			$pathuploads = $pf['CURRENT_PATH'];
		}
		else{
			return response()->json([
				    'code' => 150,
				    'message' => 'Thiếu thông tin dữ liệu!'
				]);
		}
		$validator = $this->validateFileUpload($max_size,$ext);
		$images = array();
		if ($validator->fails()) {
			return response()->json([
				    'code' => 150,
				    'message' => 'Thất bại'
				]);
		}
		else{
			$files = request()->file("file");
			foreach ($files as $k => $file) {
				$mimeType = $file->getClientMimeType();
				$pathMove = $pathuploads;
				$name =$file->getClientOriginalName();
				$name = $this->renameIfExist($pathuploads,$name);
				$file->move($pathMove, $name);
				/*convert image to webp*/
				$imageMimeTypes = ['image/jpeg','image/gif','image/png','image/bmp','image/svg+xml'];
				if (in_array($mimeType, $imageMimeTypes)) {
					$baseName = \Str::beforeLast($name, '.');
					WebPConvert::convert($pathMove.$name, $pathMove.$baseName.'.webp', []);
					$arrSizes = $this->getSizes($pathMove.$name);
					if(count($arrSizes)>0){
	        			foreach ($arrSizes as $size) {
	        				$new_image = $this->resizeImage($pathMove,$baseName,$name,$size["width"],$size["height"],$size["quality"],$size["name"]);
	        			}
	        		}
				}
				$ret = $this->insertImageMedia($pathuploads,$name,$pf["CURRENT_ID"]);
	        	array_push($images,$ret);
	        	\Event::dispatch('vanhenry.manager.media.insert.success', array($name,$ret));
			}
			return response()->json($images);
		}
	}
	public function uploadFileWm(Request $request){
		$input = $request->input();
		$extimgs =\Config::get("manager.ext_img");
		$pathuploads =\Config::get("manager.path_uploads");
		$basepath =\Config::get("manager.base_path");
		$max_size =\Config::get("manager.max_size");

		$ext = implode(",", $extimgs);

	  	$pf = Session::get('PROCESS_FILE');
		if(@$pf && array_key_exists('CURRENT_PATH', $pf)&& array_key_exists('CURRENT_ID', $pf)){
			$pathuploads = $pf['CURRENT_PATH'];
		}
		else{
			return response()->json([
				    'code' => 150,
				    'message' => 'Thiếu thông tin dữ liệu!'
				]);
		}

		$validator = $this->validateFileUpload($max_size,$ext);
		
		$images = array();
		if ($validator->fails()) {
			return response()->json([
				    'code' => 150,
				    'message' => 'Thất bại'
				]);
		}
		else{
			$files = request()->file("file");
			foreach ($files as $k => $file) {
				$name = $file->getClientOriginalName();
				$name = $this->renameIfExist($pathuploads,$name);
				$file->move($pathuploads, $name);

				$uploadedFilePath = $pathuploads.$name;
				list($width, $height, $type, $attr) = getimagesize($uploadedFilePath);
				$logo = DB::table('configs')->whereIn('name', ['logo_big', 'logo_medium', 'logo_small'])->where('act', 1)->orderBy('id', 'asc')->get();
				$big = $normal = $small = '';
				foreach($logo as $lg){
					if($lg->name == 'logo_big'){
						$big = json_decode($lg->vi_value, true);
						if(count($big) > 0){
							$big = $big['path'].$big['file_name'];
						}
					}
					elseif($lg->name == 'logo_medium'){
						$normal = json_decode($lg->vi_value, true);
						if(count($normal) > 0){
							$normal = $normal['path'].$normal['file_name'];
						}
					}
					else{
						$small = json_decode($lg->vi_value, true);
						if(count($small) > 0){
							$small = $small['path'].$small['file_name'];
						}
					}
				}
            	if($width>1500){
            		$overlay = $big;
            	}
            	else if($width>800){
            		$overlay = $normal;
            	}
            	else{
            		$overlay = $small;
            	}
            	$img = \Image::make($uploadedFilePath);
                $watermark = \Image::make($overlay)->opacity(70);
                $img->insert($watermark, 'center');
                $img->save($uploadedFilePath);
                /*convert image to webp*/
                $baseName = \Str::beforeLast($name, '.');
				WebPConvert::convert($uploadedFilePath, $pathuploads.$baseName.'.webp', []);


				$arrSizes = $this->getSizes($pathuploads.$name);
				if(count($arrSizes)>0){
        			foreach ($arrSizes as $size) {
        				$new_image = $this->resizeImage($pathuploads,$baseName,$name,$size["width"],$size["height"],$size["quality"],$size["name"]);
        			}
        		}
				$ret = $this->insertImageMedia($pathuploads,$name,$pf["CURRENT_ID"]);
	        	array_push($images,$ret);
	        	\Event::dispatch('vanhenry.manager.media.insert.success', array($name,$ret));
			}
			return response()->json($images);
		}
	}
	private function _deleteFile($id,$type=1){
		$dir = $this->getSingleMedia($id);
		if($dir->count()>0){
			if($type==0){
				$d = $dir[0];
				$ext = strtolower(substr($d->file_name, strrpos($d->file_name, '.')));
				if(in_array($ext,['.jpg','.jpeg','.gif','.png'])){
					$sizes = $this->getSizes($d["path"].$d["file_name"]);
					foreach ($sizes as $key => $value) {
						$delfile = $d["path"]."thumbs/".$value["name"]."/".$d["file_name"];
						$delfileWebp = str_replace($ext,'.webp', $delfile);
						if(file_exists($delfile)){
							\Event::dispatch('vanhenry.manager.media.delete.success', array($delfile,$id));
							unlink($delfile);
							if (file_exists($delfileWebp)) {
								unlink($delfileWebp);
							}
						}
					}
				}
				$filePath = $d["path"].$d["file_name"];
				if(file_exists($filePath)){
					unlink($filePath);
					if (file_exists(str_replace($ext,'.webp', $filePath))) {
						unlink(str_replace($ext,'.webp', $filePath));
					}
				}
			}
			return $this->deleteMedia($id,$type);
		}
	}
	public function deleteFile(Request $request,$type=1){
		$post = $request->input();
		if(@$post && isset($post['id'])){
			$id = $post["id"];
			$bl = $this->_deleteFile($id,$type);
			if($bl){
				return response()->json([
				    'code' => 200,
				    'message' => $id
				]);
			}
			else{
				return response()->json([
				    'code' => 100,
				    'message' => 'Không thể xóa tệp tin!'
				]);
			}
		}
		else{
			return response()->json([
				    'code' => 100,
				    'message' => 'Thiếu thông tin dữ liệu!'
				]);
		}
	}
	private function _restoreParent($parent){
		do{
			$media = Media::where("id",$parent)->first();
			if($media!=null){
				$media->trash = 0;
				$media->save();
				$parent = $media->parent;
			}
		}
		while($parent>0);
	}
	private function _restoreFolder($id){
		$medias = Media::select("id","parent")->where("parent",$id)->get();
		foreach ($medias as $k => $media) {
			if($media->is_file==0){
				$this->_restoreFolder($media->id);
			}
			$media->trash=0;
			$media->save();
		}
	}
	public function restoreFile(Request $request){
		$post = $request->input();
		if(@$post && isset($post['id'])){
			$id = $post["id"];
			$file = $this->getSingleMedia($id);
			if(count($file)>0){
				$file = $file[0];
				$parentId = $file->parent;
				$this->_restoreParent($parentId);
				if($file->is_file==0){
					$this->_restoreFolder($id);
				}
				$bl = Media::where("id",$id)->update(["trash"=>0]);
			}
			if($bl){
				return response()->json([
				    'code' => 200,
				    'message' => $id
				]);
			}
			else{
				return response()->json([
				    'code' => 100,
				    'message' => 'Không thể khôi phục tệp tin!'
				]);
			}
		}
		else{
			return response()->json([
				    'code' => 100,
				    'message' => 'Thiếu thông tin dữ liệu!'
				]);
		}
	}
	private function _rename($old,$new){
		if(is_file($old) || is_dir($old)){
			$ret = rename($old, $new);
			return $ret;
		}
		return false;
	}	
	public function rename(Request $request){
		$post = $request ->input();
		if(@$post && isset($post['id'])&& isset($post['newname'])){
			$pf = Session::get('PROCESS_FILE');
			if(!@$pf || !array_key_exists('CURRENT_PATH', $pf)){
				return response()->json([
				    'code' => 100,
				    'message' => 'Thiếu thông tin dữ liệu!'
				]);
			}
			else{
				$currentpath  = $pf['CURRENT_PATH'];
				$id = $post["id"];
				$file = $this->getSingleMedia($id);
				if($file->count()>0){
					$file = $file[0];
					$extra = json_decode($file["extra"],true);
					$ex = ($extra["extension"]?".".$extra["extension"]:"");
					$sizes = $this->getSizes($currentpath.$file['file_name']);
					$ret = $this->_rename($currentpath.$file['file_name'],$currentpath.$post['newname'].$ex);
					if($ret){
						foreach ($sizes as $key => $value) {
							$old = $currentpath."thumbs/".$value["name"]."/".$file["file_name"];
							$new = $currentpath."thumbs/".$value["name"]."/".$post['newname'].$ex;
							\Event::dispatch('vanhenry.manager.media.update.success', array($old."=>".$new,$id));
							$this->_rename($old,$new);
						}
						$this->updateImageMedia($currentpath, $post['newname'].$ex,$id);
						return response()->json([
						    'code' => 200,
						    'message' => 'Đã cập nhật'
						]);
					}	
				}
				return response()->json([
				    'code' => 100,
				    'message' => 'Thất bại'
				]);
			}
		}
		else{
			return response()->json([
				    'code' => 100,
				    'message' => 'Thiếu thông tin dữ liệu!'
				]);
		}
	}
	public function deleteAll(Request $request,$type=1){
		$post = $request ->input();
		if(@$post && isset($post["ids"])){
			$ids = json_decode($post["ids"]);
			$ids = @$ids ?$ids:array();
			foreach ($ids as $key => $value) {
				$this->_deleteFile($value,$type);
			}
			return response()->json([
						    'code' => 200,
						    'message' => 'Đã cập nhật'
						]);
		}
		else{
			return response()->json([
				    'code' => 100,
				    'message' => 'Thiếu thông tin dữ liệu!'
				]);
		}
	}
	private function _copyFile($old,$new){
		if(file_exists($old)){
			return copy($old, $new);
		}
		return false;
	}
	public function duplicateFile(Request $request){
		$post = $request->input();
		if(@$post && isset($post['id'])){
			$id = $post["id"];
			$pf = Session::get('PROCESS_FILE');
			if(!@$pf || !array_key_exists('CURRENT_PATH', $pf)){
				return response()->json([
				    'code' => 100,
				    'message' => 'Thiếu thông tin dữ liệu!'
				]);
			}
			else{
				$currentpath  = $pf['CURRENT_PATH'];
				$file = $this->getSingleMedia($id);
				if($file->count()>0){
					$file = $file[0];
					$extra = json_decode($file["extra"],true);
					$ex = ($extra["extension"]?".".$extra["extension"]:"");
					$str = substr($file['file_name'], 0,strrpos($file['file_name'], '.'));
					$newfilename= $str."_".time();
					$ret = $this->_copyFile($currentpath.$file['file_name'], $currentpath.$newfilename.$ex);
					$sizes = $this->getSizes($currentpath.$file['file_name']);
					foreach ($sizes as $key => $value) {
						$old = $currentpath."thumbs/".$value["name"]."/".$file["file_name"];
						$new = $currentpath."thumbs/".$value["name"]."/".$newfilename.$ex;
						\Event::dispatch('vanhenry.manager.media.insert.success', array($old."=>".$new,$id));
						$this->_copyFile($old,$new);
					}
					$retid = $this->insertImageMedia($currentpath, $newfilename.$ex,$file->parent);
					if($ret){
						return response()->json([
						    'code' => 200,
						    'message' => $retid
						]);
					}
				}
				return response()->json([
				    'code' => 100,
				    'message' => 'Không thành công!'
				]);
			}
		}
		else{
			return response()->json([
				    'code' => 100,
				    'message' => 'Thiếu thông tin dữ liệu!'
				]);
		}
	}
	public function copyFile($deleteOld=false){
		$post = request()->input();
		if(@$post && isset($post["id"]) && isset($post["idfolder"])){
			$idfile = $post["id"];
			$idfolder = $post["idfolder"];
			$pf = Session::get('PROCESS_FILE');
			if(!@$pf || !array_key_exists('CURRENT_PATH', $pf)){
				return response()->json([
				    'code' => 100,
				    'message' => 'Thiếu thông tin dữ liệu!'
				]);
			}
			$file = $this->getSingleMedia($idfile);
			$folder = $this->getSingleMedia($idfolder);
			if($file->count()>0 && $folder->count()>0){
				$currentpath  = $pf['CURRENT_PATH'];
				$file = $file[0];
				$folder = $folder[0];
				$from = $currentpath.$file["file_name"];
				$to = $folder["path"].$folder["file_name"]."/".$file["file_name"];
				$this->_copyFile($from,$to);
				if($deleteOld){
					unlink($from);
				}
				$sizes = $this->getSizes($currentpath.$file['file_name']);
				foreach ($sizes as $key => $value) {
					$old = $currentpath."thumbs/".$value["name"]."/".$file["file_name"];
					$newdir = $folder["path"].$folder["file_name"]."/"."thumbs/".$value["name"]."/";
					if(!is_dir($newdir)){
						mkdir($newdir,0777,TRUE);
					}
					$new = $newdir.$file["file_name"];
					$this->_copyFile($old,$new);
					if($deleteOld){
						unlink($old);
					}
				}
				if($deleteOld){
					\Event::dispatch('vanhenry.manager.media.update.success', array($from,$idfile));
					$retid = $this->updateImageMedia($folder["path"].$folder["file_name"]."/",$file["file_name"],$idfile,$folder->id);
					return response()->json([
					    'code' => 200,
					    'message' => $retid
					]);
				}
				else{
					\Event::dispatch('vanhenry.manager.media.insert.success', array($from,$idfile));
					$this->insertImageMedia($folder["path"].$folder["file_name"]."/",$file["file_name"],$folder->id);
					return response()->json([
					    'code' => 200,
					    'message' => "Đã cập nhật"
					]);
				}
			}
			return response()->json([
					    'code' => 100,
					    'message' => "Thất bại"
					]);
		}
		else{
			return response()->json([
					    'code' => 150,
					    'message' => "Thiếu thông tin dữ liệu"
					]);
		}
	}
	public function moveFile(){
		return $this->copyFile(true);
	}
	private function save_image($inPath,$outPath)
		{ 
		$opts=array(
		    "ssl"=>array(
		        "verify_peer"=>false,
		        "verify_peer_name"=>false,
		    ),
		);  
		$in = fopen($inPath, 'r', false, stream_context_create($opts));
	    $out=   fopen($outPath, "wb");
	    while ($chunk = fread($in,8192))
	    {
	        fwrite($out, $chunk, 8192);
	    }
	    fclose($in);
	    fclose($out);
	}
	public function downloadImage(Request $request){
		$post = $request->input();
		if(@$post && isset($post['file'])&& isset($post['name'])&& isset($post['id'])){
			$pf = Session::get('PROCESS_FILE');
			if(!@$pf || !array_key_exists('CURRENT_PATH', $pf)){
				return response()->json([
				    'code' => 150,
				    'message' => 'Thiếu thông tin dữ liệu!'
				]);
			}
			else{
				$currentpath  = $pf['CURRENT_PATH'];
				$this->save_image($post['file'],FCPATH.$currentpath.$post['name']);
				if(file_exists(FCPATH.$currentpath.$post['name'])){
					$fileuploaded = $currentpath.$post['name'];
					$baseName = \Str::beforeLast($post['name'], '.');
					$arrSizes = $this->getSizes($fileuploaded);
	        		if(count($arrSizes)>0){
	        			$new_image = "";
	        			foreach ($arrSizes as $size) {
	        				$new_image = $this->resizeImage($currentpath,$baseName,$post["name"],$size["width"],$size["height"],$size["quality"],$size["name"]);
	        			}
	        		}
	        		$ret = $this->updateImageMedia($currentpath,$post["name"],$pf["CURRENT_ID"],$post["id"]);
				}
				return response()->json([
				    'code' => 200,
				    'message' => 'Thành công'
				]);
			}
		}
		else{
			return response()->json([
			    'code' => 150,
			    'message' => 'Thiếu thông tin dữ liệu!'
			]);
		}
	}
	public function getDetailFile(Request $request){
		$post =$request->input();
		if(@$post && isset($post["id"])){
			$file = $this->getSingleMedia($post["id"]);
			if(count($file)>0){
				return view("vh::media.modalinfo",array("file"=>$file[0]));
			}
		}
	}
	public function saveDetailFile(Request $request){
		$post = $request->input();
		if(@$post && isset($post["id"])){
			$m = Media::find($post["id"]);
			$m->caption= isset($post["caption"])?$post["caption"]:"";
			$m->alt= isset($post["alt"])?$post["alt"]:"";
			$m->title= isset($post["title"])?$post["title"]:"";
			$m->description= isset($post["description"])?$post["description"]:"";
			$ret = $m->save();
			if($ret){
				return response()->json([
				    'code' => 200,
				    'message' => 'Cập nhật thành công'
				]);
			}
			else{
				return response()->json([
				    'code' => 100,
				    'message' => 'Thất bại'
				]);
			}
		}else{
			return response()->json([
			    'code' => 150,
			    'message' => 'Cập nhật thành công'
			]);
		}
	}
	public function listFolder(){
		$str = $this->recusiveMediaFolder(0);
		return view("vh::media.choosefolder",array("folders"=>$str));
	}
	public function listFolderMove(){
		$str = $this->recusiveMediaFolder(0);
		return view("vh::media.choosefolder",array("folders"=>$str,"type"=>"move"));
	}
	private function getMediaFolder($parent){
		return Media::where("parent",$parent)->where("is_file",0)->orderBy("is_file","asc")->orderBy("name","asc")->get();
	}
	private function recusiveMediaFolder($parent =0 ){
		$arr = $this->getMediaFolder($parent);
		$str = "";
		foreach ($arr as $key => $value) {
			$str .= "<ul class='list-folders'>";
			$str .= "<li><a onclick=\"$('.list-folders li a').removeClass('active');$(this).addClass('active');return false;\" dt-id='".$value->id."' href='#'>".'<i class="fa fa-folder"></i>'.$value->name."</a>";
			$str .=$this->recusiveMediaFolder($value->id);
			$str .= "</li>";
			$str .= "</ul>";
		}
		return $str;
	}
}
 ?>