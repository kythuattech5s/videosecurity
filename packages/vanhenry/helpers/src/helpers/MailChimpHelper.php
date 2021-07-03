<?php 
namespace vanhenry\helpers\helpers;
use vanhenry\helpers\helpers\SettingHelper;

class MailChimpHelper{
	private $apiKey;
	private $apiServer;
	private $mailchimp;
	public function __construct(){
		$this->mailchimp = new \MailchimpMarketing\ApiClient();
		$this->setConfigMailchimp();
	}

	private function setConfigMailchimp(){
		$this->mailchimp->setConfig([
		  	'apiKey' => SettingHelper::getSetting('API_KEY_MAILCHIMP'),
		  	'server' => SettingHelper::getSetting('API_SERVER_MAILCHIMP')
		]);
	}
	public function getList($typeApi){
		return $this->mailchimp->$typeApi->list();
	}
	public function getAllCampaignReports(){
		return $this->mailchimp->reports->getAllCampaignReports();
	}
}
