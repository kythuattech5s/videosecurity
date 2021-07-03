<?php
namespace modulevideosecurity\managevideo\Setting;
interface VideoSettingInferface{
	public function setKeyUrlResolver($key);
	public function setMediaUrlResolver($mediaFilename);
    public function setPlaylistUrlResolver($playlistFilename);
}