============== Include module bảo mật video
1.Thêm vào composer json
"modulevideosecurity\\managevideo\\": "packages/modulevideosecurity/managevideo/src".
2.Thêm vào config app.
	- provider: modulevideosecurity\managevideo\VideoSecurityServiceProvider::class
	- aliases: 'VideoSetting' => modulevideosecurity\managevideo\Facades\VideoSettingFacades::class
3.Chịu khó copy tay tvs_theme trong src của package đến public