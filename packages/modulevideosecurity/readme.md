============== Include module bảo mật video
1.Thêm vào composer json
"modulevideosecurity\\managevideo\\": "packages/modulevideosecurity/managevideo/src".
2.Thêm vào config/app.
	- provider: modulevideosecurity\managevideo\VideoSecurityServiceProvider::class
	- aliases: 'VideoSetting' => modulevideosecurity\managevideo\Facades\VideoSettingFacades::class
3.Chịu khó copy tay folder public/tvs_theme trên git vào cùng vị trí. Copy vào sửa thoải mái.
4.Thêm disks vào file filesystems.php
'tvsvideos' => [
    'driver' => 'local',
    'root' => public_path('tech5s_security_videos'),
    'url' => env('APP_URL').'/tech5s_security_videos',
    'visibility' => 'public',
]
5.Cài đặt package 'pbmedia/laravel-ffmpeg' qua composer
6.Thêm dòng này vào hàm insertImageMedia trong file MediaController.php vanhenry.
$m->save();
\VideoSetting::createTvsSecret($m); // Đây là dòng cần thêm
return $m->id;