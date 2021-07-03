<?php
namespace App\Helpers;
use vanhenry\manager\model\Media as MediaModel;
use WebPConvert\WebPConvert;
class Media
{
	public static function createDir($uploadRootDir, $uploadDir, $pathRelative, $pathAbsolute,$parent = 0)
    {	
    	$mediaParent = null;
		if($parent > 0){
			$mediaParent  = MediaModel::where('id', $parent)->first();
		}
		$pathRelative = isset($mediaParent)?$mediaParent->path.$mediaParent->name.'/':$uploadRootDir.'/';
		$pathAbsolute = base_path($pathRelative);
    	if (file_exists($pathAbsolute)) {
    		$media = MediaModel::where('name', $uploadDir)->where('path', $pathRelative)->where('parent', $parent)->first();
    		if ($media != null) {
    			return $media->id;
    		}
    		else{
    			return self::insertMediaDir($uploadDir,$parent,$uploadRootDir,$pathAbsolute,$pathRelative);
    		}
    	}
    	else{
    		mkdir($pathAbsolute, 0777, true);
    		return self::insertMediaDir($uploadDir,$parent,$uploadRootDir,$pathAbsolute,$pathRelative);
    	}
    }
    private static function insertMediaDir($uploadDir,$parent,$uploadRootDir,$pathAbsolute,$pathRelative){
    	$media = new MediaModel;
		$media->name = $uploadDir;
		$media->file_name = $uploadDir;
		$media->is_file = 0;
		$media->parent = $parent;
		$media->path = $pathRelative;
		$media->file_name = $uploadDir;
		$extra['extension'] = '';
		$extra['size'] = self::human_filesize(filesize($pathAbsolute));
		$extra['date'] = filemtime($pathAbsolute);
		$extra['isfile'] = 0;
		$extra['dir'] = $pathRelative;
		$extra['path'] = $pathRelative.$uploadDir.'/';
		$media->extra = json_encode($extra);
		$media->save();
		return $media->id;
    }
    public static function convertWebpImage($uploadToResizeDir,$fileName){
    	$tmpRealName = substr($fileName, 0,strrpos($fileName, '.'));
		$ext = strtolower(substr($fileName, strrpos($fileName, '.')));
		if(in_array($ext,['.jpg','.jpeg','.png'])){
			WebPConvert::convert($uploadToResizeDir.$fileName,$uploadToResizeDir.$tmpRealName.'.webp', []);
		}
    }
    public static function resizeImage($pathAbsolute, $resizeConfigs, $image, $fileName)
    {
    	if ($resizeConfigs == null) {
    		return;
    	}
    	$resizeConfigs = json_decode($resizeConfigs->value, true);
    	if ($resizeConfigs == null) {
    		return;
    	}
		$s = getimagesize($pathAbsolute.$fileName);
		$w = count($s)>0?$s[0]:1;
		$h = count($s)>1?$s[1]:1;
		array_push($resizeConfigs,array("name"=>"def","width"=>100,"height"=>(int)($h*100/$w),"quality"=>80));
    	foreach ($resizeConfigs as $kconfig => $config) {
			$interventionImg = \Image::make($pathAbsolute.$fileName);
			if ($config['height'] <= 0) {
				$interventionImg->resize($config['width'], null, function ($constraint) {
				    $constraint->aspectRatio();
				});
			}
			elseif($config['width'] <= 0){
				$interventionImg->resize(null, $config['height'], function ($constraint) {
				    $constraint->aspectRatio();
				});
			}
			else{
				$interventionImg->resize($config['width'], $config['height']);
			}
			$uploadToResizeDir = $pathAbsolute.'thumbs/'.$config['name'].'/';
			if(!is_dir($uploadToResizeDir)){
	        	mkdir($uploadToResizeDir, 0777, true);
	        }
	        $interventionImg->save($uploadToResizeDir.$fileName, 100);
	        $tmpRealName = substr($fileName, 0,strrpos($fileName, '.'));
			$ext = strtolower(substr($fileName, strrpos($fileName, '.')));
			if(in_array($ext,['.jpg','.jpeg','.png'])){
				WebPConvert::convert($uploadToResizeDir.$fileName,$uploadToResizeDir.$tmpRealName.'.webp', []);
			}
		}
    }
    public static function insertImageMedia($uploadRootDir, $pathAbsolute, $pathRelative, $fileName, $parentId){
		$m = new MediaModel;
		$m->name = $fileName;
		$m->file_name = $fileName;
		$m->is_file =1;
		$m->parent = $parentId;
		$m->path = $pathRelative;
		$extra['extension'] = '';
		$extra['size'] = self::human_filesize(filesize($pathAbsolute.$fileName));
		$extra['date'] = filemtime($pathAbsolute.$fileName);
		$extra['isfile'] = 0;
		$extra['dir'] = $uploadRootDir;
		$extra['path'] = $pathRelative;
		$sizes = getimagesize($pathAbsolute.$fileName);
		$extra['width'] = $sizes[0];
		$extra['height'] = $sizes[1];
		$extra['thumb'] = $pathRelative.'thumbs/def/'.$fileName;
		$m->extra = json_encode($extra);
		$m->save();
		return $m->id;
	}
	public static function human_filesize($bytes, $decimals = 2) {
	  $sz = 'BKMGTP';
	  $factor = floor((strlen($bytes) - 1) / 3);
	  return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . (" ".@$sz[$factor]."B");
	}
	public static function libImg($imgs)
	{
		$medias = MediaModel::whereIn('id', $imgs)->get();
		$arr = [];
		foreach ($medias as $key => $media) {
			$temp = [];
			$temp['id'] = $media->id;
			$temp['name'] = $media->name;
			$temp['title'] = '';
			$temp['caption'] = '';
			$temp['alt'] = '';
			$temp['description'] = '';
			$temp['created_at'] = \Carbon\Carbon::parse($media->created_at)->format('Y-m-d H:i:s');
			$temp['parent'] = $media->parent;
			$temp['is_file'] = 1;
			$temp['path'] = $media->path;
			$temp['path'] = $media->path;
			$temp['file_name'] = $media->file_name;
			$temp['extra'] = $media->extra;
			$temp['updated_at'] = \Carbon\Carbon::parse($media->updated_at)->format('Y-m-d H:i:s');
			$arr[] = $temp;
		}
		return json_encode($arr);
	}
	public static function img($img_id){
		$img = MediaModel::find($img_id);
		$temp = [];
		$temp['id'] = $img->id;
		$temp['name'] = $img->name;
		$temp['title'] = '';
		$temp['caption'] = '';
		$temp['alt'] = '';
		$temp['description'] = '';
		$temp['created_at'] = \Carbon\Carbon::parse($img->created_at)->format('Y-m-d H:i:s');
		$temp['parent'] = $img->parent;
		$temp['is_file'] = 1;
		$temp['path'] = $img->path;
		$temp['path'] = $img->path;
		$temp['file_name'] = $img->file_name;
		$temp['extra'] = $img->extra;
		$temp['updated_at'] = \Carbon\Carbon::parse($img->updated_at)->format('Y-m-d H:i:s');
		return json_encode($temp);
	}
}