<?php

class Wolfram {

    public $firstname;
    public $lastname;
    public $mailbox;
    public $address;
    public $password;
    public $time;
    public $status;

    private $loginURL = 'http://www.wolframalpha.com/input/login.jsp';
    private $trialURL = "http://www.wolframalpha.com/input/trial.jsp";
    private $nameURL = 'http://www.behindthename.com/random/random.php?number=1&gender=both&randomsurname=yes&all=no&usage_eng=1';
    private $passwordCharset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";

    function __construct(){
        Network::SendEvent(0);
        $this->time = microtime(true);
        $this->mailbox = new TempMail($_SERVER['REMOTE_ADDR']);
        $this->GenerateName();
        $this->mailbox->FetchAddress();
        $this->mailbox->SetMail($this->firstname, $this->lastname);
        $this->address = $this->mailbox->addr;
        $this->password = $this->GeneratePassword(8);
    }
    function GeneratePassword( $length ) {
        return substr(str_shuffle($this->passwordCharset),0,$length);
    }

    function GenerateName(){
        $output = Network::Request($this->nameURL);
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML($output);
        $xpath = new DOMXPath($doc);
        $nodes = $xpath->query("//*[contains(@class, 'plain')]");
        $this->firstname = $nodes->item(0)->nodeValue;
        $this->lastname = $nodes->item(1)->nodeValue;
    }

    function StartProTrial(){
        Network::SendEvent(3);
        Network::Request($this->trialURL);
    }

    function SignIn(){
        Network::SendEvent(2);
        $response = Network::Request($this->loginURL);
        $json = json_decode($response, true);

        $data = array(
            'username' => $this->address,
            'password' => $this->password,
        );

        $response = Network::Request($json['url'], true, http_build_query($data), true);

        if(preg_match('#Location: (.*)#', $response, $r))
            $l = trim($r[1]);

        Network::Request($l);
    }

    function VerifyPro($link){
        Network::SendEvent(5);
        Network::Request($link);
    }

    function ToJSON(){
        $json = array("status" => $this->status, "id" => $this->address, "password" => $this->password, "time" => (round(microtime(true)-$this->time, 3)), "percent" => 100);
        return json_encode($json);

    }

    function SignUp(){
        Network::SendEvent(1);
        $data = array(
            'email' => $this->address,
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'password' => $this->password,
            'passwordc' => $this->password,
            'referer' => ""
        );
        Network::Request('http://www.wolframalpha.com/input/signup.jsp', "POST", http_build_query($data), false, true);
    }
}