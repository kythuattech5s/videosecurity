<?php
namespace modulevideosecurity\managevideo\Facades;

use Illuminate\Support\Facades\Facade;

class VideoSettingFacades extends Facade
{
    protected static function getFacadeAccessor()
    { 
        return \modulevideosecurity\managevideo\Setting\VideoSetting::class;
    }
}