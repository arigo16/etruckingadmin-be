<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model {

    protected $fillable = [];

    protected $hidden = [ 

    ];

    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Vendor');
    }

    public function promo()
    {
        return $this->belongsTo('App\Promotion');
    }

    public function truck_feet()
    {
        return $this->belongsTo('App\TruckFeet');
    }

    public function truck_type()
    {
        return $this->belongsTo('App\TruckType');
    }

    public function documents()
    {
        return $this->hasMany('App\OrderDocument')->orderBy('created_at', 'desc');
    }

    public function payment()
    {
        return $this->hasMany('App\OrderPayment')->orderBy('created_at', 'desc');
    }

    public function order_history()
    {
        return $this->hasMany('App\OrderDriverTruck');
    }
}