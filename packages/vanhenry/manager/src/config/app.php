<?php 
return [
	'providers'=>[
		vanhenry\helpers\HelperServiceProvider::class,
        vanhenry\customblade\CustomBladeServiceProvider::class,
	],
	'aliases'=>[
    	'FCHelper' => vanhenry\helpers\helpers\FCHelper::class,
        'StringHelper' => vanhenry\helpers\helpers\StringHelper::class
	]
]
?>