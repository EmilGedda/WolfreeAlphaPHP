<?php

    header('Content-Type: text/event-stream; charset=utf-8');
    header('Cache-Control: no-cache');

    require('Classes/Network.php');
    require('Classes/TempMail.php');
    require('Classes/Wolfram.php');
    
    try{
        Network::Initialize();
        $user = new Wolfram();

        $user->SignUp();
        $user->SignIn();
        $user->StartProTrial();
        $user->mailbox->FetchMail();
        $link = $user->mailbox->GetVerifyLink();

        $user->VerifyPro($link);
        $user->status = "success";

        Network::Close(); //Finally statement not supported yet
    }
    catch(Exception $ex){ //It dun goofed.
        $user->status = $ex->getMessage();
    }

    echo "data: " . $user->ToJSON() . PHP_EOL . PHP_EOL;
