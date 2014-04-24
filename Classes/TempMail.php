<?php

class TempMail {

    private $domain = array('sharklasers.com', 'grr.la', 'spam4.me');
    private $token;
    private $timestamp;
    private $r;
    private $url = "http://api.guerrillamail.com/ajax.php";
    public $emails = array();
    public $ip;
    public $agent;
    public $addr;

    function __construct($IP){
        $this->ip = $IP;
        $this->agent = Network::$agent;
        $this->r = mt_rand(0,2);
    }

    function FetchAddress(){
        $output = Network::Request($this->url . "?f=get_email_address&ip=$this->ip&agent=" . urlencode($this->agent) . "&domain=" . $this->domain[1]);
        $json = json_decode($output, true);
        $this->addr = $json['email_addr'];
        $this->timestamp = $json['email_timestamp'];
        $this->token = $json['sid_token'];
    }

    function FetchMail(){
        Network::SendEvent(4);
        $a = $this->domain[$this->r];
        $output = Network::Request("?f=get_email_list&sid_token=$this->token&seq=0&offset=0&domain=$a");
        $t = json_decode($output, true);
        $this->emails =  $t['list'];
    }

    function SetMail($firstname, $lastname){
        $firstname = strtolower($firstname);
        $lastname = strtolower($lastname);
        $output = Network::Request("?f=set_email_user&sid_token=$this->token&email_user=$firstname.$lastname");
        $json = json_decode($output, true);
        $a = $this->domain[$this->r];
        $this->addr = "$firstname.$lastname@$a";
        $this->token = $json['sid_token'];
}

    function HasCreatedAccount(){
        var_dump(json_encode($this->emails));
        for($i = 0; $i<count($this->emails);$i++){
            if($this->emails[$i]["mail_from"] === "pro@wolframalpha.com")
                return true;
        }
        return false;
    }
    function GetVerifyLink(){
        for($i = 0; $i<count($this->emails);$i++){
            if(strpos($this->emails[$i]["mail_subject"],"Wolfram Alpha Pro") !== false){
                $output = Network::Request('?f=fetch_email&sid_token=' . $this->token . '&email_id='. $this->emails[$i]["mail_id"]);
                $msg = str_replace('\\', '', substr($output, strpos($output,'>http') + 1, strpos($output,'<\/a>') - strpos($output,'>http') - 1));
                return $msg;
            }
        }
        return "null";
    }
}