<?php 



return array(

	"max_lockcount" =>3, // Số lần vi phạm tối đa

	"max_connect"=>20, //Số lần kết nối / time_limit

	"time_limit"=>1,

	"time_wait"=>20, //Thời gian khóa do vi phạm

	"proxy"=>true,//DIsable Proxy

	"htaccess"=>'.htaccess',//Đường dẫn file htaccess

	"folder"=>'firewall/',//Đường dẫn file htaccess

	"htaccess_bak"=>"firewall/htaccess_bak" //backup htaccess

);



 ?>