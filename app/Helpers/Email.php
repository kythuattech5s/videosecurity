<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Mail;
use Swift_Mailer;
use Swift_SmtpTransport;

// load models
use App\Models\SysEmail;

class Email
{
    public static function send($data)
    {
        // reset count email
        SysEmail::whereNotBetween('updated_at', [date('Y-m-d 00:00:00', time()), date('Y-m-d 23:59:59', time())])->update(array('count'=>0));

        // get account send mail
        $sys_email = SysEmail::where(array('deleted'=>false))->orderBy('count', 'ASC')->get()->first();
        //$sys_email = SysEmail::where([ ['count', '<', 200], 'deleted'=>false ])->get()->first();

        // Backup your default mailer
        if($sys_email){
            $data['from'] = $sys_email->email;

            $backup = Mail::getSwiftMailer();
            $host      = config('variables.mail_server');
            $port      = 587;
            $security  = '';
            $usermail = $sys_email->email;
            $password = $sys_email->password;

            $transport = new Swift_SmtpTransport($host, $port, $security);
            $transport->setUsername($usermail);
            $transport->setPassword($password);
            $gmail = new Swift_Mailer($transport);

            // Set the mailer as gmail
            Mail::setSwiftMailer($gmail);

            // Send your message
            Mail::send(['html'=>'email/template'], $data, function($message) use ($data)
            {
                $message->from($data['from'], config('variables.webname'));
                $message->to($data['to'])->subject($data['subject']);
            });

            $count = $sys_email->count+1;
            SysEmail::where('id', $sys_email->id)->update(array('count'=>$count));
            // Restore your original mailer
            Mail::setSwiftMailer($backup);
        }
    }
}
