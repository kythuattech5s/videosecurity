============== Include module bảo mật video
Sau khi copy package thực hiện các bước sau
1.Thêm vào composer json
"modulevideosecurity\\managevideo\\": "packages/modulevideosecurity/managevideo/src".
2.Cài đặt package 'pbmedia/laravel-ffmpeg' qua composer
3.Tạo bảng
php artisan migrate --path=/packages/modulevideosecurity/managevideo/src/Database/Migrations
4.Thêm vào config/app.
	- provider: modulevideosecurity\managevideo\VideoSecurityServiceProvider::class
	- aliases: 'VideoSetting' => modulevideosecurity\managevideo\Facades\VideoSettingFacades::class
5.Chịu khó copy tay folder public/tvs_theme trên git vào cùng vị trí. Copy vào sửa thoải mái.
Tạo thêm folder public/tech5s_security_videos
6.Thêm disks vào file filesystems.php
'tvsvideos' => [
    'driver' => 'local',
    'root' => public_path('tech5s_security_videos'),
    'url' => env('APP_URL').'/tech5s_security_videos',
    'visibility' => 'public',
],
'uploads' => [
    'driver' => 'local',
    'root' => public_path('uploads'),
    'url' => env('APP_URL'),
    'visibility' => 'public',
],
7.Tạo table jobs
	- php artisan queue:table
	- php artisan migrate
8. Đảm bảo các event sau hoạt động
	- vanhenry.manager.delete.success
	- vanhenry.manager.insert.success
	- vanhenry.manager.update_normal.success
	- vanhenry.manager.media.delete.success
	- vanhenry.manager.media.insert.success
	Về cơ bản là hoạt động hết trừ event vanhenry.manager.media.delete.success trong 1 số core đang không ném ra event. Sửa hàm _deleteFile trong MediaController như sau:
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
					$delfile = $d["path"].$d["file_name"];
					\Event::dispatch('vanhenry.manager.media.delete.success', array($delfile,$id));
					unlink($filePath);
					if (file_exists(str_replace($ext,'.webp', $filePath))) {
						unlink(str_replace($ext,'.webp', $filePath));
					}
				}
			}
			return $this->deleteMedia($id,$type);
		}
	}
9. composer update (tất nhiên rồi :v)