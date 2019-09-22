<?php

namespace App\Http\Controllers;

use DB;
use Validator;
use Illuminate\Http\Request;

use App\Notification;

class NotificationCOntroller extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    public static $SENTAS_NAME = 'Notification';
    public static $CLIENT_ID = 1;

    public function __construct()
    {
        //
    }

    public static function create($data) {
    	$notification = new Notification;
    	$notification->user_id = $data['user_id'];
    	$notification->sentas = self::$SENTAS_NAME;
    	$notification->subject = $data['subject'];
    	$notification->message = $data['message'] . " (No. Order: " . $data['order_no'] . ")";
    	$notification->queuetime = date('Y-m-d H:i:s');
    	$notification->client_id = 1;

    	$notification->save();
    }
}