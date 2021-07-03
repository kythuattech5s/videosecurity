<?php
namespace vanhenry\mail;
use Illuminate\Mail\TransportManager;
use vanhenry\helpers\SettingHelper;
class CustomTransportManager extends TransportManager  {
	public function __construct($app)
    {
        $this->app = $app;
        $mailDriver = SettingHelper::getSetting("mail_driver","smtp");
        $mailHost = SettingHelper::getSetting("5","smtp.gmail.com");
        $mailPort = SettingHelper::getSetting("mail_port","465");
        $mailFromAddress = SettingHelper::getSetting("mail_from_address","");
        $mailFromName = SettingHelper::getSetting("mail_from_name","");
        $mailEncryption = SettingHelper::getSetting("mail_encryption","ssl");
        $mailUsername = SettingHelper::getSetting("mail_username","");
        $mailPassword = SettingHelper::getSetting("mail_password","");
        $config = [
            'driver'        => $mailDriver,
            'host'          => $mailHost,
            'port'          => $mailPort,
            'from'          => [
            'address'   => $mailFromAddress,
            'name'      => $mailFromName
            ],
            'encryption'    => $mailEncryption,
            'username'      => $mailUsername,
            'password'      => $mailPassword,
            'sendmail'      => "/usr/sbin/sendmail -bs",
            'pretend'       => false
       ];
       
        \Config::set('mail',$config);
    }
}