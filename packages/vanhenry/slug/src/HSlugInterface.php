<?php 
namespace vanhenry\slug;
interface HSlugInterface{
	public function getSlug();
	public function sluggify($force = false);
}

 ?>