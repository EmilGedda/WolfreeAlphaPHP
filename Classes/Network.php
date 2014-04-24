<?php

class Network {

    static public $ch;
    static public $tmp;
    static public $agent = "Mozilla/5.0 (Windows NT 6.2; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/32.0.1667.0 Safari/537.36";
    static private $msg = array("Generating temporary mail", "Creating account", "Signing in to wolframalpha", "Starting pro account", "Fetching activation mail", "Activating pro account", "Done!");

    static function Initialize(){
        self::$ch = curl_init();
        self::$tmp = tempnam('./cookies', 'COOKIE');

        curl_setopt(self::$ch,CURLOPT_USERAGENT, self::$agent);
        curl_setopt(self::$ch,CURLOPT_RETURNTRANSFER, true);
        curl_setopt(self::$ch, CURLOPT_COOKIEFILE, self::$tmp);
        curl_setopt(self::$ch, CURLOPT_COOKIEJAR, self::$tmp);
        curl_setopt(self::$ch, CURLINFO_HEADER_OUT, true);

        //curl_setopt(self::$ch, CURLOPT_FOLLOWLOCATION, true); Funkar inte med open_basedir :(
    }

    static function Close(){
        curl_close(self::$ch);
        unlink(self::$tmp);
    }
    static function SendEvent($id){
        $response = array(
            'msg' => self::$msg[$id],
            'percent' => round(($id+1) / 7, 3)*100
        );

        echo "data: " . json_encode($response) . PHP_EOL . PHP_EOL;
        ob_flush();
        flush();
    }
    static function Request($url, $post = false, $postdata = null, $header = false, $session = false){

        curl_setopt(self::$ch, CURLOPT_URL, $url);
        curl_setopt(self::$ch, CURLOPT_POST, $post);
        curl_setopt(self::$ch, CURLOPT_HTTPGET, !$post);
        curl_setopt(self::$ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt(self::$ch, CURLOPT_COOKIESESSION, $session);
        curl_setopt(self::$ch, CURLOPT_HEADER, $header);

        return curl_exec(self::$ch);

    }

}