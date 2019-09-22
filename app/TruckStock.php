<?php 
namespace App;

use Illuminate\Database\Eloquent\Model;

class TruckStock extends Model {

    protected $fillable = [        
        'truck_id',
        'vendor_id',
        'qty_actual',
        'qty_available',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    protected $hidden = [ 

    ];

    public function truck()
    {
        return $this->belongsTo('App\Truck');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Vendor');
    }

    public function checkAvailability($vendor_id, $truck_id)
    {
        return self::where('truck_id', $truck_id)->where('vendor_id', $vendor_id)->count();
    }
}