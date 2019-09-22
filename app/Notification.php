<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model {
    protected $table = "queue_fcms";

    protected $fillable = [
        'user_id',
        'sentas',
        'subject',
        'message',
        'queuetime',
        'client_id',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected $hidden = [ 

    ];

    protected $dates = ['deleted_at'];
}