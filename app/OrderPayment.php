<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model {

    protected $fillable = [];

    protected $hidden = [ 

    ];

    public function order_detail()
    {
        return $this->belongsTo('App\OrderDetail');
    }
}