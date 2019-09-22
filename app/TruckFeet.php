<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class TruckFeet extends Model {

    protected $fillable = [        
        'name'
    ];

    protected $hidden = [ 

    ];

    public function trucks()
    {
        return $this->hasMany('App\Truck', 'truck_feet_id', 'id');
    }
}