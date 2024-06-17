<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mail;

class MailSendController extends Controller
{
    public function index(){

    	$data = [];

    	Mail::send('email.sample', $data, function($message){
    	    $message->to('abc987@example.com', 'Test')
    	    ->subject('This is a test mail');
    	});
        return '送信完了!';
    }
}
