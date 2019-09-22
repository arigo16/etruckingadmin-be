<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDriverTruck extends Model {

    protected $fillable = [];

    protected $hidden = [ 

    ];

    public function order()
    {
        return $this->belongsTo('App\Order');
    }

    public function driver()
    {
        return $this->belongsTo('App\Driver');
    }

    public function truck()
    {
        return $this->belongsTo('App\Truck');
    }
}