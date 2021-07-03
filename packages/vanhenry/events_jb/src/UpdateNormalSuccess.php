<?php 
namespace vanhenry\events_jb;

use \vanhenry\helpers\MHelper;

use DB;

class UpdateNormalSuccess{

    public function handle($table,$data,$injects,$_id){

    	if($table instanceof \vanhenry\manager\model\VTable){

    		$tblmap = $table->table_map;

           

        }

        else if(is_string($table)){

            $tblmap = $table;

        }

         $sysid = \Auth::guard("h_users")->id();

        if($tblmap=="order_pcbs"){

            $od = new \App\OrderDetail;

            $od->order_id = $_id;

            $od->status = $data["status"];

            $od->sys_user_id = $sysid;

            $od->type = 1;

            $od->save();

            $this->sendMail($_id,"pcb");

        }

        else if($tblmap=="order_stencils"){

            $od = new \App\OrderDetail;

            $od->order_id = $_id;

            $od->status = $data["status"];

            $od->sys_user_id = $sysid;

            $od->type = 2;

            $od->save();

            $this->sendMail($_id,"stencil");

        }

        else if($tblmap=="orders"){

            $od = new \App\OrderDetail;

            $od->order_id = $_id;

            $od->status = $data["status"];

            $od->sys_user_id = $sysid;

            $od->type = 3;

            $od->save();

            $this->sendMail($_id,"lkn");

            

        }

    }

    private function sendMail($id,$type="pcb"){

        $order= new \stdClass();

        $typetext = "Mạch PCB";

        if($type=='pcb'){

            $typetext = "Mạch PCB";

            $order = \App\OrderPcb::find($id);

        }

        else if($type=='stencil'){

            $typetext = "Mạch Stencil";

            $order = \App\OrderStencil::find($id);

        }else{

            $typetext = "Link kiện nhanh";

            $order = \App\Order::find($id);

        }

        $user= \App\User::find($order->user_id);

        $email = $user->email;

        $view = \View::make("havi.template_mail.notify",compact("order",'id','type'));

        $ordercontent = $view->render();

        $userinfo = \View::make("havi.template_mail.userinfo",compact("user"));

        \Mail::send([], [], function ($message) use($ordercontent,$typetext,$email,$user,$userinfo){

          

            $temp = \vanhenry\helpers\SettingHelper::getSetting("TEMPLATE_MAIL");

            $temp = str_replace("@NAME@", $user->name, $temp);

            $temp = str_replace("@TYPE@", $typetext, $temp);

            $temp = str_replace("@ORDER@", $ordercontent, $temp);

            $temp = str_replace("@USERINFO@", $userinfo, $temp);



            $message->to($email)

            ->subject('Thông báo trạng thái đơn đặt hàng từ Havicom!')

            ->setBody($temp, 'text/html'); 

        });

    }

   

}