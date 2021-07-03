<?php namespace vanhenry\minify;

class Firewall{
	var $msg_proxy = "Hệ thống chặn truy cập qua Proxy!";
	private $app;
	private $ip_address;
	private $folder;
	private $file;
	public function __construct($app){
		$this->app=$app;
	}
	function load() 
    { 
        $firewall = config('firewall');
        if($firewall["proxy"]){
        	if($this->blockProxy()) 
                die($msg_proxy); 
        }

        $this->ip_address =request()->ip(); 
        $this->ip_address = $this->ip_address=="::1"?"127.0.0.1":$this->ip_address;
        $this->folder = $firewall['folder']; 
        if(!file_exists($this->folder)){
        	mkdir($this->folder);
        }
        $this->file = $firewall['htaccess']; 
    }
    function blockProxy(){//kiểm tra coi có sử dụng Proxy         
        $proxy_headers = array(   
            'HTTP_VIA',   
            'HTTP_X_FORWARDED_FOR',   
            'HTTP_FORWARDED_FOR',   
            'HTTP_X_FORWARDED',   
            'HTTP_FORWARDED',   
            'HTTP_CLIENT_IP',   
            'HTTP_FORWARDED_FOR_IP',   
            'VIA',   
            'X_FORWARDED_FOR',   
            'FORWARDED_FOR',   
            'X_FORWARDED',   
            'FORWARDED',   
            'CLIENT_IP',   
            'FORWARDED_FOR_IP',   
            'HTTP_PROXY_CONNECTION'   
        ); 
        foreach($proxy_headers as $x){ 
            if (isset($_SERVER[$x]))  
                return true; 
            else 
                return false; 
        } 
    }
    function open(){ 
        $this->load();//khởi tao những thứ cần thiết. ko dung construc để tránh làm nặng trang 
        $now = time(); 
        $wait = 0; 

        //kiểm tra xem ip bị khóa vĩnh viễn ko 
        if (file_exists($this->folder. $this->ip_address .'.deny')){             
            $htaccess = 'deny from '. $this->ip_address."\n"; 
                         
            $this->write_file($this->file, $htaccess,'a');//ko cần gỡ vì IP đã bị chặn 
             
            $this->msg(1,-1); 
        }elseif( file_exists($this->folder. $this->ip_address .'.lock') ){//ip bị khóa tạm thời                         
            @$time = file_get_contents($this->folder. $this->ip_address.'.lock'); 
                         
            if (file_exists($this->folder. $this->ip_address .'.lockcount')) 
                $lock_count = file_get_contents($this->folder. $this->ip_address .'.lockcount'); 
            else 
                $lock_count = 0; 
             
            $wait = ((config('firewall.time_wait')*($lock_count+1)) + $time) - $now; 
             
            if ($wait > 0) //chưa hết thời gian mở khóa 
             
                $this->msg(2,$wait); 
                 
            else { // hết thời gian. mở khóa cho ip 
                 
                @unlink($this->folder. $this->ip_address.'.lock');     
                             
                $this->write_file($this->folder. $this->ip_address, "1|".$now,'w'); 
                 
            } 
        }else {//Neu chua bi khoa vinh vien va tam thoi 
            //Kiem tra xem IP nay da tung truy cap chua 
            if (file_exists($this->folder. $this->ip_address)){ 
            //Neu IP nay da tung truy cap thi kiem tra so ket noi     
                @$c=file_get_contents($this->folder. $this->ip_address); 
                     
                $explode = explode("|",$c); 
                 
                if ( ($explode[0]+1) >= config('firewall.max_connect') &&  
                     ($now - $explode[1]) <= config('firewall.time_limit') ){ 
                     
                    $this->write_file($this->folder. $this->ip_address.'.lock',$now,'w');//vượt giới hạn tạo file lock 
                     
                    if (file_exists($this->folder. $this->ip_address.".lockcount"))                         
                        @$lock_count = file_get_contents($this->folder. $this->ip_address.".lockcount"); 
                    else 
                        $lock_count = 0; 
                     
                    if ( ($lock_count+1) >= config('firewall.max_lockcount') ){//kiểm tra số lần vi phạm 
                     
                        $this->write_file($this->folder. $this->ip_address.'.lockcount',($lock_count+1)."|".$now,'w'); 
                         
                        $this->write_file($this->folder. $this->ip_address.'.deny',null,'w'); 
                         
                        $htaccess = 'deny from '. $this->ip_address."\n"; 
                         
                        $this->write_file(config('firewall.htaccess'), $htaccess,'a'); 
                         
                        $this->msg(1,-1); 
                         
                    }else{ 
                        $this->write_file($this->folder. $this->ip_address.'.lockcount',($lock_count+1)."|".$now,'w'); 
                        $this->msg(2,$wait); 
                    } 
                      
                }elseif (count($explode)>1 && ($explode[0]+1) < config('firewall.max_connect') &&  
                         ($now - $explode[1]) >= config('firewall.time_limit') ){ 
                              
                    $this->write_file($this->folder. $this->ip_address,"1|".$now,'w'); 
                }else 
                    $this->write_file($this->folder. $this->ip_address,($explode[0]+1)."|".$now,'w'); 
                     
            }else{
                $this->write_file($this->folder. $this->ip_address,"1|".$now,'w');     
            }
        }//#Neu chua bi khoa vinh vien va tam thoi 
    } 
    private function manyRequestToUrl(){
    
    }
    function msg($err,$time = 60){ 
        /* 
        1 : khóa vĩnh viễn 
        2 : chưa hết thời gian mở khóa 
        */ 
        switch($err){ 
            case '1': 
                $err = 'Hệ thống phát hiện truy cập của bạn có vấn đề. Để đảm bảo an toàn cho hệ thống truy cập của 
                bạn bị chặn. <br> Vui lòng kiểm tra lại các phần mềm chạy ngầm <br> 
                Truy cập của bạn sẽ được tự động mở khóa sau 24h'; 
            break; 
             
            case '2': 
                $err = 'Truy cập của bạn bị tạm khóa để đảm bảo an toàn cho hệ thống.'; 
            break; 
             
            default: 
                $err = 'Địa chỉ IP của bị nghi vấn.'; 
            break; 
        } 
         
        $content = array('err'=>$err, 
                         'time'=>$time); 
        echo \View::make("fw::view",$content)->render();
        die(); 
    } 
    private function write_file($path, $data, $mode = 'wb')
	{
		if ( ! $fp = @fopen($path, $mode))
		{
			return FALSE;
		}

		flock($fp, LOCK_EX);

		for ($result = $written = 0, $length = strlen($data); $written < $length; $written += $result)
		{
			if (($result = fwrite($fp, substr($data, $written))) === FALSE)
			{
				break;
			}
		}

		flock($fp, LOCK_UN);
		fclose($fp);

		return is_int($result);
	}
}